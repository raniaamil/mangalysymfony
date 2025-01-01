<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // Ajout de l'importation pour les groupes

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
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
    private ?string $titre = null;

    // Ne pas inclure le genre dans ce groupe
    #[ORM\ManyToOne(inversedBy: 'manga')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genre $genre = null;

    #[ORM\Column(length: 150)]
    private ?string $auteur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_sortie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, Theorie>
     */
    #[ORM\OneToMany(targetEntity: Theorie::class, mappedBy: 'manga')]
    private Collection $theorie;

    public function __toString(): string
    {
        return $this->titre; // Retourne le titre du manga lorsqu'il est utilisé comme une chaîne
    }

    public function __construct()
    {
        $this->theorie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->date_sortie;
    }

    public function setDateSortie(\DateTimeInterface $date_sortie): static
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

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
            $theorie->setManga($this);
        }

        return $this;
    }

    public function removeTheorie(Theorie $theorie): static
    {
        if ($this->theorie->removeElement($theorie)) {
            // set the owning side to null (unless already changed)
            if ($theorie->getManga() === $this) {
                $theorie->setManga(null);
            }
        }

        return $this;
    }
}
