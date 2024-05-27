<?php

namespace App\Entity;

use App\Repository\ProtagonistRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProtagonistRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_SLUG', fields: ['slug', 'game'])]
#[Vich\Uploadable]
class Protagonist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['protagonist'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Regex(
        pattern: '/^(?!edit$)[a-zA-Z0-9\- ]+$/',
        message: 'Name is invalid',
        match: true

    )]
    #[Groups(['protagonist', 'protagonist.create'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Regex(
        pattern: '/^(?!edit$)[a-zA-Z0-9\- ]+$/',
        message: 'Slug is invalid',
        match: true

    )]
    #[Groups(['protagonist', 'protagonist.create'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'protagonists')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['protagonist.dashboard'])]
    private ?Game $game = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['protagonist', 'protagonist.create'])]
    private ?string $story = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['protagonist'])]
    private ?Upload $portrait = null;

    #[ORM\ManyToOne]
    #[Groups(['protagonist'])]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['protagonist.dashboard'])]
    private ?User $creator = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(1)]
    #[Groups(['protagonist'])]
    private ?int $level = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getStory(): ?string
    {
        return $this->story;
    }

    public function setStory(?string $story): static
    {
        $this->story = $story;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getPortrait(): ?Upload
    {
        return $this->portrait;
    }

    public function setPortrait(?Upload $portrait): static
    {
        $this->portrait = $portrait;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
