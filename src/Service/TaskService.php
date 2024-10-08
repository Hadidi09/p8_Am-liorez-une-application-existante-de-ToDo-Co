<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class TaskService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function canDelete(Task $task, User $user): bool
    {
        // Si l'utilisateur est un admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Si l'utilisateur est l'auteur de la tâche
        if ($task->getUser() === $user) {
            return true;
        }

        // Si la tâche est anonyme et l'utilisateur est admin 
        if ($task->getUser() === null) {
            return in_array('ROLE_ADMIN', $user->getRoles());
        }

        return false;
    }
}
