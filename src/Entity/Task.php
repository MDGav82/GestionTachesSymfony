<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column]
    private ?int $progress_percent = null;


    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $associated_project = null;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tasks')]
    private Collection $associated_user;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_date = null;

    public function __construct()
    {
        $this->associated_user = new ArrayCollection();
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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getProgressPercent(): ?int
    {
        return $this->progress_percent;
    }

    public function setProgressPercent(int $progress_percent): static
    {
        $this->progress_percent = $progress_percent;

        return $this;
    }

    public function getAssociatedProject(): ?project
    {
        return $this->associated_project;
    }

    public function setAssociatedProject(?project $associated_project): static
    {
        $this->associated_project = $associated_project;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getAssociatedUser(): Collection
    {
        return $this->associated_user;
    }

    public function addAssociatedUser(user $associatedUser): static
    {
        if (!$this->associated_user->contains($associatedUser)) {
            $this->associated_user->add($associatedUser);
        }

        return $this;
    }

    public function removeAssociatedUser(user $associatedUser): static
    {
        $this->associated_user->removeElement($associatedUser);

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }
}
