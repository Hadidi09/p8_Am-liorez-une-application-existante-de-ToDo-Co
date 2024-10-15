<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{
    private $entityManager;
    private $client;
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testList()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('user@example.com');
        if (!$user) {
            $user = new User();
            $user->setEmail('user@example.com');
            $user->setPassword('password');
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $this->client->loginUser($user);
        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testCreateUser(): void
    {
        $crawler =  $this->client->request('GET',  '/users/create');
        $this->assertResponseIsSuccessful();

        $creationButton = $crawler->selectButton('Créer l\'utilisateur');
        $form = $creationButton->form([
            'user[email]' => 'doctor@yahoo.fr',
            'user[username]' => 'doctor',
            'user[password]' => 'doctor'
        ]);

        $this->client->submit($form);
    }

    public function testEditUser(): void
    {
        $testUser = $this->userRepository->findOneByEmail('user@example.com');
        $adminUser = $this->userRepository->findOneByEmail('admin@example.com');

        $this->client->loginUser($adminUser);
        $crawler = $this->client->request('GET', '/users/' . $testUser->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user[email]' => 'user@example.com',
            'user[username]' => 'docteur',
            'user[password]' => 'newpassword',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN']
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/users');

        $editUser = $this->userRepository->find($testUser->getId());
        $this->assertEquals('user@example.com', $editUser->getEmail());
        $this->assertEquals('docteur', $editUser->getUsername());
        $this->assertTrue(in_array('ROLE_ADMIN', $editUser->getRoles()));

        $crawler = $this->client->request('GET', '/users/' . $editUser->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[email]' => 'invalid-email',
            'user[username]' => 'docteur',
            'user[password]' => 'newpassword',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN']
        ]);
        $this->client->submit($form);
        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}
