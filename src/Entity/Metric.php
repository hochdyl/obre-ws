<?php

namespace App\Entity;

use App\Repository\MetricRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MetricRepository::class)]
class Metric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['metric'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['metric', 'metric.create'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['metric', 'metric.create'])]
    private ?string $emoji = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: ProtagonistMetric::class, mappedBy: 'metricDetails', orphanRemoval: true)]
    private Collection $protagonistMetrics;

    #[ORM\ManyToOne(inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    public function __construct()
    {
        $this->protagonistMetrics = new ArrayCollection();
    }

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

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(?string $emoji): static
    {
        $this->emoji = $emoji;

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

    /**
     * @return Collection<int, ProtagonistMetric>
     */
    public function getProtagonistMetrics(): Collection
    {
        return $this->protagonistMetrics;
    }

    public function addProtagonistMetric(ProtagonistMetric $protagonistMetric): static
    {
        if (!$this->protagonistMetrics->contains($protagonistMetric)) {
            $this->protagonistMetrics->add($protagonistMetric);
            $protagonistMetric->setMetricDetails($this);
        }

        return $this;
    }

    public function removeProtagonistMetric(ProtagonistMetric $protagonistMetric): static
    {
        if ($this->protagonistMetrics->removeElement($protagonistMetric)) {
            // set the owning side to null (unless already changed)
            if ($protagonistMetric->getMetricDetails() === $this) {
                $protagonistMetric->setMetricDetails(null);
            }
        }

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
}
