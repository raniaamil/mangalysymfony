<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['username'], message: 'Ce nom d\'utilisateur est déjà pris.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email n\'est pas valide')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['update'])]
    #[Assert\Length(
        min: 6,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères'
    )]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $username = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'user')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Critiques>
     */
    #[ORM\OneToMany(targetEntity: Critiques::class, mappedBy: 'user')]
    private Collection $critiques;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    /**
     * @var Collection<int, Theorie>
     */
    #[ORM\OneToMany(targetEntity: Theorie::class, mappedBy: 'user')]
    private Collection $theorie;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $post;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'user')]
    private Collection $likes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'URL de l\'avatar n\'est pas valide')]
    private ?string $avatar = null;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->critiques = new ArrayCollection();
        $this->dateInscription = new \DateTime(); // Initialise automatiquement la date d'inscription
        $this->theorie = new ArrayCollection();
        $this->post = new ArrayCollection();
        $this->likes = new ArrayCollection();
        // $this->manga = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Critiques>
     */
    public function getCritiques(): Collection
    {
        return $this->critiques;
    }

    public function addCritique(Critiques $critique): static
    {
        if (!$this->critiques->contains($critique)) {
            $this->critiques->add($critique);
            $critique->setUser($this);
        }

        return $this;
    }

    public function removeCritique(Critiques $critique): static
    {
        if ($this->critiques->removeElement($critique)) {
            if ($critique->getUser() === $this) {
                $critique->setUser(null);
            }
        }

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): static
    {
        $this->dateInscription = $date_inscription;

        return $this;
    }

    /**
     * @return Collection<int, Theorie>
     */
    public function getTheorie(): Collection
    {
        return $this->theorie;
    }

    public function addTheorie(Theorie $theorie): static
    {
        if (!$this->theorie->contains($theorie)) {
            $this->theorie->add($theorie);
            $theorie->setUser($this);
        }

        return $this;
    }

    public function removeTheorie(Theorie $theorie): static
    {
        if ($this->theorie->removeElement($theorie)) {
            // set the owning side to null (unless already changed)
            if ($theorie->getUser() === $this) {
                $theorie->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): static
    {
        if (!$this->post->contains($post)) {
            $this->post->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}