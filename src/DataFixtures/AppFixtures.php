<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\UserProject;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Gedmo\Mapping\Annotation as Gedmo; 

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** Usuarios para pruebas de datos */
        $usuarioUno = new User();
        $usuarioUno->setName('David Perez');
        $usuarioUno->setUsername('david.perez');
        $usuarioUno->setRoles(['ROLE_DEV_JR']);
        $manager->persist($usuarioUno);

        $usuarioDos = new User();
        $usuarioDos->setName('Lorena Martinez');
        $usuarioDos->setUsername('Lorena.Martinez');
        $usuarioDos->setRoles(['ROLE_DEV_MID']);
        $manager->persist($usuarioDos);

        $projectsData = [
            'CRM',
            'POS VENTAS',
            'SEGUROS GLOBALES'
        ];

        $projects = [];

        foreach ($projectsData as $name) {
            $project = new Project();
            $project->setName($name);
            $project->setStartDate(new \DateTime());
            $project->setStatus('active');
            $project->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($project);
            $projects[] = $project;
        }

        /** Creacion de relacion de usuarios por proyectos */
        foreach ($projects as $project) {

            $usuarioUnoMembership = new UserProject();
            $usuarioUnoMembership->setUser($usuarioUno);
            $usuarioUnoMembership->setProject($project);
            $usuarioUnoMembership->setHourlyRate('50.00');
            $usuarioUnoMembership->setStartedAt(new \DateTimeImmutable());
            $manager->persist($usuarioUnoMembership);

            $usuarioDosMembership = new UserProject();
            $usuarioDosMembership->setUser($usuarioDos);
            $usuarioDosMembership->setProject($project);
            $usuarioDosMembership->setHourlyRate('40.00');
            $usuarioDosMembership->setStartedAt(new \DateTimeImmutable());
            $manager->persist($usuarioDosMembership);
        }

        /** Tareas de pruebas */
        foreach ($projects as $project) {

            /** Creo dos tareas por proyecto para probar, asignadas a cada usuario */

            for ($i = 1; $i <= 2; $i++) {
                $task = new Task();
                $task->setUser($usuarioUno);
                $task->setProject($project);
                $task->setTitle("Criterio de aceptacion David Tarea Nr $i - {$project->getName()}");
                $task->setStartDate(new \DateTime());
                $task->setDueDate(new \DateTime('+5 days'));
                $task->setStatus('completed');
                $task->setHoursWorked('10.00');
                $task->setAppliedHourlyRate('50.00');
                $task->setTotalAmount('500.00');
                $task->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($task);
            }

            for ($i = 1; $i <= 2; $i++) {
                $task = new Task();
                $task->setUser($usuarioDos);
                $task->setProject($project);
                $task->setTitle("Criterio de aceptacion Lorena Tarea Nr $i - {$project->getName()}");
                $task->setStartDate(new \DateTime());
                $task->setDueDate(new \DateTime('+3 days'));
                $task->setStatus('completed');
                $task->setHoursWorked('8.00');
                $task->setAppliedHourlyRate('40.00');
                $task->setTotalAmount('320.00');
                $task->setCreatedAt(new \DateTimeImmutable());

                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}
