<?php
/**
 * User: demius
 * Date: 30.09.2021
 * Time: 22:34
 */
declare(strict_types=1);


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testGuestDashboard(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h3', 'pub');
        self::assertSelectorTextNotContains('h3', 'alice');
    }

//    public function testUserDashboard(): void
//    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/');
//
//    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/about');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('section.content > h1', 'Tasks and Docs tracker');
    }
}