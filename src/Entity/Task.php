<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

//** Uso de api ApiPlatform para exponer endpoint para las tareas */
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

use App\Controller\TaskByUserController;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/{id}/tasks',
            controller: TaskByUserController::class,
            extraProperties: [
                'openapi_context' => [
                    'security' => [['JWT' => []]],
                    'summary' => 'Lista las tareas de un usuario con proyecto y valor'
                ]
            ]
        ),
    ],
)]

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt")]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false, onDelete: "RESTRICT")]
    private Project $project;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false, onDelete: "RESTRICT")]
    private ?User $user;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dueDate;

    #[ORM\Column(length: 50)]
    private string $status;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $hoursWorked;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $appliedHourlyRate = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $totalAmount = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        $this->project = $project;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getDueDate(): \DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getHoursWorked(): string
    {
        return $this->hoursWorked;
    }

    public function setHoursWorked(string $hoursWorked): static
    {
        $this->hoursWorked = $hoursWorked;
        return $this;
    }

    public function getAppliedHourlyRate(): ?string
    {
        return $this->appliedHourlyRate;
    }

    public function setAppliedHourlyRate(?string $appliedHourlyRate): static
    {
        $this->appliedHourlyRate = $appliedHourlyRate;
        return $this;
    }

    public function setTotalAmount(?string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    #[Groups(['task:read'])]
    public function getTotalAmount(): ?string
    {
        if ($this->totalAmount !== null) {
            return $this->totalAmount;
        }

        if ($this->appliedHourlyRate !== null && $this->hoursWorked !== null) {
            return bcmul($this->hoursWorked, $this->appliedHourlyRate, 2);
        }

        return null;
    }

    #[Groups(['task:read'])]
    public function getProjectName(): string
    {
        return $this->project->getName();
    }
}
