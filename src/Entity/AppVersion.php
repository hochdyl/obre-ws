<?php

namespace App\Entity;

use App\Repository\AppVersionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppVersionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_VERSION', fields: ['name', 'number'])]
#[UniqueEntity(['name', 'number'])]
class AppVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['appVersion'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['appVersion'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Regex(
        pattern: '^\d+\.\d+\.\d+$',
        message: 'Version number has to follow this syntax : {number}.{number}.{number}',
        match: true
    )]
    #[Groups(['appVersion'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['appVersion'])]
    private ?string $features = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['appVersion'])]
    private ?string $bugfix = null;

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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getFeatures(): ?string
    {
        return $this->features;
    }

    public function setFeatures(string $features): static
    {
        $this->features = $features;

        return $this;
    }

    public function getBugfix(): ?string
    {
        return $this->bugfix;
    }

    public function setBugfix(string $bugfix): static
    {
        $this->bugfix = $bugfix;

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
