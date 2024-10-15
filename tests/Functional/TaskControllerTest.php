<?php

namespace App\Tests\Functional;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class TaskControllerTest extends WebTestCase
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

    public function testTaskList()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Assurez-vous que cet utilisateur existe dans votre base de données de test
        $user = $userRepository->findOneByEmail('user@example.com');

        // Si l'utilisateur n'existe pas, vous devrez peut-être le créer ici
        if (!$user) {
            $user = new User();
            $user->setEmail('user@example.com');
            $user->setPassword('password');
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.card-title', 'Tâche 1');
    }

    public function testCreateTask(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $creationButton = $crawler->selectButton('Ajouter');
        $form = $creationButton->form();

        $form['task[title]'] = "new tasks";
        $form['task[content]'] = "my last training game";

        $this->client->submit($form);
    }

    public function testEditTask(): void
    {
        $testUser = $this->userRepository->findOneByEmail('user@example.com');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/' . $testUser->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'ma nouvelle tâche',
            'task[content]' => 'entrainement demain matin',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
    }

    public function testTaskToggle(): void
    {
        $testUser = $this->userRepository->findOneByEmail('user@example.com');
        $this->client->loginUser($testUser);

        $task = new Task();
        $task->setTitle('Tâche de test');
        $task->setContent('Contenu de test');
        $task->setDone(false);
        $task->setUser($testUser);
        $task->setCreatedAt(new \DateTimeImmutable);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $crawler = $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Décoder la réponse JSON
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('isDone', $responseData);
        $this->assertTrue($responseData['isDone']);
    }

    public function testDeleteTaskSuccessfully()
    {

        $session = new Session(new MockFileSessionStorage());
        $request = new Request();
        $request->setSession($session);
        $stack = $this->client->getContainer()->get(RequestStack::class);
        $stack->push($request);

        $user = $this->userRepository->findOneByEmail('user@example.com');
        $this->client->loginUser($user);
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setContent('Test Content');
        $task->setDone(false);
        $task->setUser($user);
        $task->setCreatedAt(new \DateTimeImmutable);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('delete-task-' . $task->getId());
        $this->client->request(
            'POST',
            '/tasks/' . $task->getId() . '/delete',
            ['_token' => $csrfToken->getValue()]
        );
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
