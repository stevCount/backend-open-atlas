<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param int $userId
     * @param string|null $status
     * @param \DateTimeInterface|null $startDateFrom
     * @param \DateTimeInterface|null $startDateTo
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function findTasksByUserWithProjectAndAmount(
        int $userId,
        ?string $status = null,
        ?\DateTimeInterface $startDateFrom = null,
        ?\DateTimeInterface $startDateTo = null,
        int $page = 1,
        int $limit = 10
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->select(
                't.id',
                't.title',
                'p.name AS projectName',
                't.hoursWorked',
                't.appliedHourlyRate',
                't.totalAmount',
                't.status',
                't.startDate',
                't.dueDate'
            )
            ->innerJoin('t.project', 'p')
            ->innerJoin('t.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId);

        if ($status) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }
        if ($startDateFrom) {
            $qb->andWhere('t.startDate >= :startDateFrom')
                ->setParameter('startDateFrom', $startDateFrom);
        }
        if ($startDateTo) {
            $qb->andWhere('t.startDate <= :startDateTo')
                ->setParameter('startDateTo', $startDateTo);
        }
        $qb->orderBy('t.startDate', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }
}
