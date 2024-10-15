<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
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

    public function testLoginPage()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Connexion');

        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testLoginWithValidCredentials()
    {
        $testUser = $this->userRepository->findOneByEmail('admin@example.com');

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, [
            '_username' => $testUser->getEmail(),
            '_password' => 'password_admin',
        ]);

        $this->assertResponseRedirects('/tasks');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.btn-danger', 'Se déconnecter');
    }

    public function testLoginWithInvalidCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, [
            '_username' => 'invalid@example.com',
            '_password' => 'mauvais_password',
        ]);

        $this->assertResponseRedirects('/login');

        $crawler = $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithError()
    {

        $this->client->request('GET', '/login?error=1');

        $this->assertResponseIsSuccessful();
    }

    public function testLogout()
    {

        $testUser = $this->userRepository->findOneByEmail('admin@example.com');
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Connexion');
    }
}
