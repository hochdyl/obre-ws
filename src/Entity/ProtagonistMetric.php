<?php

namespace App\Entity;

use App\Repository\ProtagonistMetricRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProtagonistMetricRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_METRIC', fields: ['protagonist', 'metricDetails'])]
class ProtagonistMetric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['metric'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Protagonist $protagonist = null;

    #[ORM\ManyToOne(inversedBy: 'protagonistMetrics')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['metric'])]
    private ?Metric $metricDetails = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(['metric'])]
    private ?int $value = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['metric'])]
    private ?int $max = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProtagonist(): ?Protagonist
    {
        return $this->protagonist;
    }

    public function setProtagonist(?Protagonist $protagonist): static
    {
        $this->protagonist = $protagonist;

        return $this;
    }

    public function getMetricDetails(): ?Metric
    {
        return $this->metricDetails;
    }

    public function setMetricDetails(?Metric $metric): static
    {
        $this->metricDetails = $metric;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): static
    {
        $this->max = $max;

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
