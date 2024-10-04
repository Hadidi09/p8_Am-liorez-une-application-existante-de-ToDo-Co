<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 20; $i++) {
            $task = new Task();
            $task->setTitle('Tâche ' . $i);
            $task->setContent('Contenu de la tâche ' . $i);
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setDone(rand(0, 1) == 1);

            // Attribuer la tâche à un utilisateur aléatoire
            $randomUser = $users[array_rand($users)];
            $task->setUser($randomUser);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
