<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Ce genre existe déjà')]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le nom du genre est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom du genre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom du genre ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom = null;

    /**
     * @var Collection<int, Manga>
     */
    #[ORM\OneToMany(targetEntity: Manga::class, mappedBy: 'genre')]
    private Collection $manga;

    public function __construct()
    {
        $this->manga = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, Manga>
     */
    public function getManga(): Collection
    {
        return $this->manga;
    }

    public function addManga(Manga $manga): static
    {
        if (!$this->manga->contains($manga)) {
            $this->manga->add($manga);
            $manga->setGenre($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): static
    {
        if ($this->manga->removeElement($manga)) {
            if ($manga->getGenre() === $this) {
                $manga->setGenre(null);
            }
        }

        return $this;
    }
}