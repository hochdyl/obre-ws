<?php

namespace App\Entity;

use App\Repository\ProtagonistMetricRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtagonistMetricRepository::class)]
class ProtagonistMetric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Protagonist $protagonist = null;

    #[ORM\ManyToOne(inversedBy: 'metricValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Metric $metric = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\Column(nullable: true)]
    private ?int $max = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getMetric(): ?Metric
    {
        return $this->metric;
    }

    public function setMetric(?Metric $metric): static
    {
        $this->metric = $metric;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
