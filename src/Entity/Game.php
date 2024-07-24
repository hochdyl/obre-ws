<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_SLUG', fields: ['slug'])]
#[UniqueEntity('slug')]
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
    private ?User $gameMaster = null;

    #[ORM\Column()]
    #[Assert\NotBlank]
    #[Groups(['game', 'game.create'])]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['game', 'game.create'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Regex(
        pattern: '/^[a-zA-Z0-9\- ]+$/',
        message: 'Slug is invalid',
        match: true
    )]
    #[Groups(['game', 'game.create'])]
    private ?string $slug = null;

    #[ORM\OneToMany(targetEntity: Protagonist::class, mappedBy: 'game')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Groups(['game.play'])]
    private Collection $protagonists;

    #[ORM\Column]
    #[Groups(['game'])]
    private ?bool $closed = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Metric::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $metrics;

    public function __construct()
    {
        $this->protagonists = new ArrayCollection();
        $this->metrics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameMaster(): ?User
    {
        return $this->gameMaster;
    }

    public function setGameMaster(?User $gameMaster): static
    {
        $this->gameMaster = $gameMaster;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Protagonist>
     */
    public function getProtagonists(): Collection
    {
        return $this->protagonists;
    }

    public function filterProtagonistsAvailableByUser(?User $user): Game
    {
        // If user is game master, return everything
        if ($this->getGameMaster()->getUserIdentifier() === $user->getUserIdentifier()) return $this;

        foreach ($this->protagonists as $protagonist) {
            if ($protagonist->getOwner() && $protagonist->getOwner()->getUserIdentifier() !== $user->getUserIdentifier()) {
                $this->removeProtagonist($protagonist);
            }
        }

        $this->protagonists = new ArrayCollection($this->protagonists->getValues());

        return $this;
    }

    public function getProtagonistsOwnedByUser(?User $user): Collection
    {
        return $this->protagonists->filter(
            function (Protagonist $protagonist) use ($user) {
                if (!$protagonist->getOwner()) return false;

                return $protagonist->getOwner()->getUserIdentifier() === $user->getUserIdentifier();
            }
        );
    }

    public function addProtagonist(Protagonist $protagonist): static
    {
        if (!$this->protagonists->contains($protagonist)) {
            $this->protagonists->add($protagonist);
            $protagonist->setGame($this);
        }

        return $this;
    }

    public function removeProtagonist(Protagonist $protagonist): static
    {
        if ($this->protagonists->removeElement($protagonist)) {
            // set the owning side to null (unless already changed)
            if ($protagonist->getGame() === $this) {
                $protagonist->setGame(null);
            }
        }

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

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
     * @return Collection<int, Metric>
     */
    public function getMetrics(): Collection
    {
        return $this->metrics;
    }

    public function addMetric(Metric $metric): static
    {
        if (!$this->metrics->contains($metric)) {
            $this->metrics->add($metric);
            $metric->setGame($this);
        }

        return $this;
    }

    public function removeMetric(Metric $metric): static
    {
        if ($this->metrics->removeElement($metric)) {
            // set the owning side to null (unless already changed)
            if ($metric->getGame() === $this) {
                $metric->setGame(null);
            }
        }

        return $this;
    }
}
