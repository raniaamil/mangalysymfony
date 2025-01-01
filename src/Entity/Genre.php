<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // Ajout de l'importation pour les groupes

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    /**
     * @Groups({"manga_suggestion"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({"manga_suggestion"})
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    // Ne pas inclure les mangas dans ce groupe
    /**
     * @var Collection<int, Manga>
     */
    #[ORM\OneToMany(targetEntity: Manga::class, mappedBy: 'genre', orphanRemoval: true)]
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
            // set the owning side to null (unless already changed)
            if ($manga->getGenre() === $this) {
                $manga->setGenre(null);
            }
        }

        return $this;
    }
}
