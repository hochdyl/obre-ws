<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TITLE', fields: ['title'])]
#[UniqueEntity('title')]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['game'])]
    private ?User $owner = null;

    #[ORM\Column]
    #[Groups(['game'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['game'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column()]
    #[Assert\Type(
        type: '\DateTimeInterface',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Groups(['game', 'game.create'])]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game', 'game.create'])]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
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

    #[ORM\PrePersist]
    private function beforePersist(): void
    {
        $dateTimeNow = new DateTimeImmutable('now');
        $this->setCreatedAt($dateTimeNow);
    }

    #[ORM\PreUpdate]
    private function beforeUpdate(): void
    {
        $dateTimeNow = new DateTimeImmutable('now');
        $this->setUpdatedAt($dateTimeNow);
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
