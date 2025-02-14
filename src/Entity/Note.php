<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $notation = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Manga $manga = null;

    #[ORM\OneToOne(inversedBy: 'note', cascade: ['persist', 'remove'])]
    private ?Critiques $critiques = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotation(): ?float
    {
        return $this->notation;
    }

    public function setNotation(?float $notation): static
    {
        $this->notation = $notation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getManga(): ?Manga
    {
        return $this->manga;
    }

    public function setManga(?Manga $manga): static
    {
        $this->manga = $manga;

        return $this;
    }

    public function getCritiques(): ?Critiques
    {
        return $this->critiques;
    }

    public function setCritiques(?Critiques $critiques): static
    {
        $this->critiques = $critiques;

        return $this;
    }
}
