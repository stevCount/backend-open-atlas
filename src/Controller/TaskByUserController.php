<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Service\TaskAmountCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Task;

class TaskByUserController extends AbstractController
{
    public function __construct(
        private readonly TaskAmountCalculator $taskAmountCalculator
    ) {}
    #[IsGranted('ROLE_USER')]
    public function __invoke(int $id, TaskRepository $taskRepository, Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $page = max((int) $request->query->get('page', 1), 1);
        $limit = max((int) $request->query->get('limit', 10), 1);

        $startDateFrom = $request->query->get('startDateFrom')
            ? new \DateTime($request->query->get('startDateFrom'))
            : null;

        $startDateTo = $request->query->get('startDateTo')
            ? new \DateTime($request->query->get('startDateTo'))
            : null;

        $tasks = $taskRepository->findTasksByUserWithProjectAndAmount(
            $id,
            $status,
            $startDateFrom,
            $startDateTo,
            $page,
            $limit
        );

        $data = array_map(function (Task $task) {
            return [
                'id'                => $task->getId(),
                'title'             => $task->getTitle(),
                'projectName'       => $task->getProjectName(),
                'status'            => $task->getStatus(),
                'hoursWorked'       => $task->getHoursWorked(),
                'appliedHourlyRate' => $task->getAppliedHourlyRate(),
                'totalAmount'       => $this->taskAmountCalculator->resolve($task),
                'startDate'         => $task->getStartDate()->format('Y-m-d'),
                'dueDate'           => $task->getDueDate()->format('Y-m-d'),
            ];
        }, $tasks);

        return new JsonResponse([
            'page' => $page,
            'limit' => $limit,
            'count' => count($data),
            'data' => $data,
        ]);
    }
}