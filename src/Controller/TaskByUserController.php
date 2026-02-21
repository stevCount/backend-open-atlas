<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskByUserController extends AbstractController
{
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
        foreach ($tasks as &$task) {
            if ($task['totalAmount'] === null && $task['appliedHourlyRate'] !== null) {
                $task['totalAmount'] = bcmul($task['hoursWorked'], $task['appliedHourlyRate'], 2);
            }
            $task['startDate'] = $task['startDate']->format('Y-m-d');
            $task['dueDate']   = $task['dueDate']->format('Y-m-d');
        }

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'count' => count($tasks),
            'data' => $tasks,
        ]);
    }
}