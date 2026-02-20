<?php

namespace App\Entity;

use App\Repository\UserProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProjectRepository::class)]
#[ORM\Table(name: 'user_projects')]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt")]
class UserProject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false, onDelete: "RESTRICT")]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false, onDelete: "RESTRICT")]
    private Project $project;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $hourlyRate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;
}