<?php
/**
 * User: demius
 * Date: 30.09.2021
 * Time: 22:34
 */
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\TestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    use TestHelperTrait;

    public function testGuestDashboard(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h3', 'pub');
        self::assertSelectorTextNotContains('h3', 'alice');
    }

    public function testUserDashboard(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'Alice',
            'PHP_AUTH_PW' => 'Alice'
        ]);
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertAnySelectorTextContains('pub', $crawler->filter('h3'), 'Пользователь не видит публичного проекта');
        self::assertAnySelectorTextContains('alice', $crawler->filter('h3'), 'Пользователь не видит своего проекта');
        self::assertAllSelectorTextNotContains('bob', $crawler->filter('h3'), 'Пользователь видит чужой приватный проект');
    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/about');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('section.content > h1', 'Tasks and Docs tracker');
    }
}