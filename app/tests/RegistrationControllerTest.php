<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{

    /**
     * Тест регистрации
     * @return void
     */
    public function testRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('form')->form([
            'registration_form[login]' => 'testuser',
            'registration_form[plainPassword]' => 'testpassword',
            'registration_form[agreeTerms]' => 1
        ]);
        $client->submit($form);
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['login' => 'testuser']);
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->getLogin());
    }
}
