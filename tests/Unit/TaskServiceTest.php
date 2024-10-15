<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskServiceTest extends KernelTestCase
{
    private TaskService $taskService;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->taskService = $container->get(TaskService::class);
    }

    public function testCanDeleteAsAdmin(): void
    {
        $admin = (new User())->setRoles(['ROLE_ADMIN']);
        $task = (new Task())->setUser($admin);

        $this->assertTrue($this->taskService->canDelete($task, $admin));
    }

    public function testCanDeleteAsTaskOwner(): void
    {
        $user = (new User())->setRoles(['ROLE_USER']);
        $task = (new Task())->setUser($user);

        $this->assertTrue($this->taskService->canDelete($task, $user));
    }

    public function testCanDeleteAnonymousTaskAsAdmin(): void
    {
        $admin = (new User())->setRoles(['ROLE_ADMIN']);
        $task = (new Task())->setUser(null);

        $this->assertTrue($this->taskService->canDelete($task, $admin));
    }

    public function testCannotDeleteAsNonOwnerNonAdmin(): void
    {
        $user = (new User())->setRoles(['ROLE_USER']);
        $otherUser = (new User())->setRoles(['ROLE_USER']);
        $task = (new Task())->setUser($otherUser);

        $this->assertFalse($this->taskService->canDelete($task, $user));
    }

    public function testCannotDeleteAnonymousTaskAsNonAdmin(): void
    {
        $user = (new User())->setRoles(['ROLE_USER']);
        $task = (new Task())->setUser(null);


        $this->assertFalse($this->taskService->canDelete($task, $user));
    }
}
