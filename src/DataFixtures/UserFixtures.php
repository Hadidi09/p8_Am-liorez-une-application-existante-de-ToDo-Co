<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Créer un utilisateur anonyme
        $anonymousUser = new User();
        $anonymousUser->setUsername('Anonyme');
        $anonymousUser->setEmail('anonyme@example.com');
        $anonymousUser->setPassword($this->passwordHasher->hashPassword($anonymousUser, 'password_anonyme'));
        $anonymousUser->setRoles(['ROLE_USER']);
        $manager->persist($anonymousUser);

        // Créer un utilisateur normal
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password_user'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        // Créer un administrateur
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password_admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
