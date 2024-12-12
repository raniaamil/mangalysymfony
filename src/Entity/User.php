<?php

// Déclaration du namespace. Cela indique que cette classe appartient à l'espace "App\Entity"
// Cette classe représente une entité utilisateur qui est mappée à la base de données
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM; // On importe les annotations de Doctrine pour gérer la persistance des données en base de données
use Symfony\Component\Security\Core\User\UserInterface; // On importe l'interface UserInterface de Symfony pour la gestion des utilisateurs
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface; // On importe PasswordAuthenticatedUserInterface pour gérer l'authentification avec mot de passe

#[ORM\Entity] // Cette annotation indique à Doctrine que cette classe est une entité et qu'elle sera mappée à une table en base de données
class User implements UserInterface, PasswordAuthenticatedUserInterface // La classe User implémente UserInterface et PasswordAuthenticatedUserInterface, ce qui signifie que cette classe doit gérer les informations d'authentification de l'utilisateur
{
    #[ORM\Id] // Cette annotation indique que la propriété suivante (id) est la clé primaire de l'entité
    #[ORM\GeneratedValue] // Cette annotation indique que la valeur de la clé primaire sera générée automatiquement par la base de données
    #[ORM\Column] // Cette annotation définit cette propriété comme une colonne en base de données.
    private ?int $id = null; // Propriété privée pour stocker l'ID de l'utilisateur. Elle est de type int et peut être nulle (lors de la création de l'utilisateur)

    #[ORM\Column(length: 180, unique: true)] // La colonne 'email' est unique et doit être d'une longueur maximale de 180 caractères
    private ?string $email = null; // Propriété privée pour stocker l'email de l'utilisateur

    #[ORM\Column] // Cette annotation définit la propriété suivante comme une colonne de la table
    private array $roles = []; // Propriété privée pour stocker les rôles de l'utilisateur sous forme de tableau

    #[ORM\Column] // Cette annotation définit la propriété suivante comme une colonne de la table
    private ?string $password = null; // Propriété privée pour stocker le mot de passe de l'utilisateur

    #[ORM\Column(length: 50)] // La propriété 'name' est une colonne avec une longueur maximale de 50 caractères
    private ?string $username = null; // Propriété privée pour stocker le nom de l'utilisateur

    // Méthode pour obtenir l'ID de l'utilisateur. Elle renvoie l'ID de l'entité
    public function getId(): ?int { return $this->id; }

    // Méthode pour obtenir l'email de l'utilisateur
    public function getEmail(): ?string { return $this->email; }

    // Méthode pour définir l'email de l'utilisateur
    public function setEmail(string $email): self { 
        $this->email = $email;
        return $this;
    }

    // Méthode pour obtenir les rôles de l'utilisateur
    // Par défaut, la méthode ajoute 'ROLE_USER' pour s'assurer que chaque utilisateur a ce rôle
    public function getRoles(): array
    {
        $roles = $this->roles; // On copie les rôles existants de l'utilisateur
        $roles[] = 'ROLE_USER'; // On ajoute 'ROLE_USER' comme rôle par défaut si aucun autre rôle n'est défini
        return array_unique($roles); // On retourne les rôles sans doublons
    }

    // Méthode pour définir les rôles de l'utilisateur
    public function setRoles(array $roles): self { 
        $this->roles = $roles; // On assigne les rôles passés en argument à la propriété $roles
        return $this; 
    }

    // Méthode pour obtenir le mot de passe de l'utilisateur
    public function getPassword(): ?string { return $this->password; }

    // Méthode pour définir le mot de passe de l'utilisateur
    public function setPassword(string $password): self { 
        $this->password = $password; // On assigne le mot de passe passé en argument à la propriété $password
        return $this; 
    }

    // Méthode pour obtenir le nom de l'utilisateur
    public function getUsername(): ?string { return $this->username; }

    // Méthode pour définir le nom de l'utilisateur
    public function setUsername(string $username): self { 
        $this->username = $username; // On assigne le nom passé en argument à la propriété $username
        return $this; 
    }

    // Méthode requise par l'interface PasswordAuthenticatedUserInterface
    // Elle est vide ici car il n'y a pas de données sensibles à effacer après l'authentification
    public function eraseCredentials(): void 
    {
    }

    // Méthode requise par l'interface UserInterface. Elle renvoie l'identifiant de l'utilisateur (ici, l'email)
    public function getUserIdentifier(): string 
    {
        return $this->email; // Retourne l'email de l'utilisateur comme identifiant unique
    }
}
