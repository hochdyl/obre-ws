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
    #[Groups(['metric'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['metric'])]
    private ?string $emoji = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: ProtagonistMetric::class, mappedBy: 'metric', orphanRemoval: true)]
    private Collection $metricValues;

    public function __construct()
    {
        $this->metricValues = new ArrayCollection();
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
    public function getProtagonistMetric(): Collection
    {
        return $this->metricValues;
    }

    public function addProtagonistMetric(ProtagonistMetric $protagonistMetric): static
    {
        if (!$this->metricValues->contains($protagonistMetric)) {
            $this->metricValues->add($protagonistMetric);
            $protagonistMetric->setMetric($this);
        }

        return $this;
    }

    public function removeProtagonistMetric(ProtagonistMetric $protagonistMetric): static
    {
        if ($this->metricValues->removeElement($protagonistMetric)) {
            // set the owning side to null (unless already changed)
            if ($protagonistMetric->getMetric() === $this) {
                $protagonistMetric->setMetric(null);
            }
        }

        return $this;
    }
}