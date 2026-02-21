<?php
namespace App\Service;
use App\Entity\Task;

class TaskAmountCalculator
{
    public function resolve(Task $task): ?string
    {
        if ($task->getTotalAmount() !== null) {
            return $task->getTotalAmount();
        }

        if ($task->getAppliedHourlyRate() !== null && $task->getHoursWorked() !== null) {
            return number_format(
                (float)$task->getHoursWorked() * (float)$task->getAppliedHourlyRate(),
                2, '.', ''
            );
        }

        return null;
    }
}