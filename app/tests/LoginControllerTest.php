<?php

namespace App\Tests;

use App\Controller\LoginController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
{

    public function testLoginPageIsAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }
    public function testLogin()
    {
        $client = static::createClient();
        $username = 'LoginUser';
        $password = 'testpassword';
        $user = new User();
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user->setLogin($username);
        $user->setPassword($userPasswordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user);
        $entityManager->flush();
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Sign in')->form();
        $form['_username'] = $username;
        $form['_password'] = $password;
        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }


}
