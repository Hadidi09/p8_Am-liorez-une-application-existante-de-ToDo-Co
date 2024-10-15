<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskEntityTest extends TestCase
{
    private Task $task;

    protected function  setUp(): void
    {
        $this->task = new Task();
    }

    public function testGetterSetterTitle(): void
    {
        $title = "title";
        $this->task->setTitle($title);
        $this->assertEquals($title, $this->task->getTitle());
    }
    public function testGetterSetterContent(): void
    {
        $content = "This is the task content";
        $this->task->setContent($content);
        $this->assertEquals($content, $this->task->getContent());
    }

    public function testGetterSetterCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable();
        $this->task->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $this->task->getCreatedAt());
    }

    public function testGetterSetterIsDone(): void
    {
        $this->task->setDone(true);
        $this->assertTrue($this->task->isDone());

        $this->task->setDone(false);
        $this->assertFalse($this->task->isDone());
    }

    public function testGetterSetterUser(): void
    {
        $user = new User();
        $this->task->setUser($user);
        $this->assertSame($user, $this->task->getUser());
    }

    public function testInitialValues(): void
    {
        $newTask = new Task();
        $this->assertNull($newTask->getId());
        $this->assertNull($newTask->getCreatedAt());
        $this->assertNull($newTask->getTitle());
        $this->assertNull($newTask->getContent());
        $this->assertNull($newTask->isDone());
        $this->assertNull($newTask->getUser());
    }
}
