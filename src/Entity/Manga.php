<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
#[UniqueEntity(fields: ['titre'], message: 'Un manga avec ce titre existe déjà')]
#[ORM\Index(name: 'idx_manga_titre', columns: ['titre'])]
#[ORM\Index(name: 'idx_manga_date_sortie', columns: ['date_sortie'])]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: false, unique: true)]
    #[Assert\NotBlank(message: 'Le titre du manga est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
    )]
    private string $titre;

    #[ORM\ManyToOne(inversedBy: 'manga')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner un genre')]
    private Genre $genre;

    #[ORM\Column(length: 50, nullable: false)]
    #[Assert\NotBlank(message: 'Le nom de l\'auteur est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom de l\'auteur doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom de l\'auteur ne peut pas dépasser {{ limit }} caractères'
    )]
    private string $auteur;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    #[Assert\NotNull(message: 'La date de sortie est obligatoire')]
    #[Assert\LessThanOrEqual('today', message: 'La date de sortie ne peut pas être dans le futur')]
    private \DateTimeInterface $date_sortie;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(
        min: 20,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères'
    )]
    private string $description;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'L\'URL de l\'image est obligatoire')]
    #[Assert\Url(message: 'L\'URL de l\'image n\'est pas valide')]
    private string $image;

    /**
     * @var Collection<int, Theorie>
     */
    #[ORM\OneToMany(targetEntity: Theorie::class, mappedBy: 'manga')]
    private Collection $theorie;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'manga')]
    private Collection $post;

    /**
     * @var Collection<int, Critiques>
     */
    #[ORM\OneToMany(targetEntity: Critiques::class, mappedBy: 'manga')]
    private Collection $critique;

    public function __toString(): string
    {
        return $this->titre; 
    }

    public function __construct()
    {
        $this->theorie = new ArrayCollection();
        $this->post = new ArrayCollection();
        $this->critique = new ArrayCollection();
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
            $post->setManga($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getManga() === $this) {
                $post->setManga(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Critiques>
     */
    public function getCritique(): Collection
    {
        return $this->critique;
    }

    public function addCritique(Critiques $critique): static
    {
        if (!$this->critique->contains($critique)) {
            $this->critique->add($critique);
            $critique->setManga($this);
        }

        return $this;
    }

    public function removeCritique(Critiques $critique): static
    {
        if ($this->critique->removeElement($critique)) {
            // set the owning side to null (unless already changed)
            if ($critique->getManga() === $this) {
                $critique->setManga(null);
            }
        }

        return $this;
    }

}
