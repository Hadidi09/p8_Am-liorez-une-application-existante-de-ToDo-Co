<?php

namespace App\Tests\unit;

use App\Entity\User;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetterAndSetterForEmail(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    public function testGetterAndSetterForRoles(): void
    {
        $roles = ['ROLE_ADMIN'];
        $this->user->setRoles($roles);
        $this->assertContains('ROLE_USER', $this->user->getRoles());
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testGetterAndSetterForPassword(): void
    {
        $password = 'password123';
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testGetterAndSetterForUsername(): void
    {
        $username = 'johndoe';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testAddAndRemoveTask(): void
    {
        $task = new Task();
        $this->user->addTask($task);
        $this->assertCount(1, $this->user->getTasks());
        $this->assertTrue($this->user->getTasks()->contains($task));

        $this->user->removeTask($task);
        $this->assertCount(0, $this->user->getTasks());
        $this->assertFalse($this->user->getTasks()->contains($task));
    }

    public function testEraseCredentials(): void
    {
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }
}
