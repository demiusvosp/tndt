<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\TestAgentAuthenticator;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class AppContext extends MinkContext
{
    /** @var KernelInterface */
    private $kernel;

    private SessionInterface $symfonySession;
    private UserRepository $userRepository;


    public function __construct(KernelInterface $kernel, SessionInterface $symfonySession, UserRepository $userRepository)
    {
        $this->kernel = $kernel;
        $this->symfonySession = $symfonySession;
        $this->userRepository = $userRepository;
    }

    /**
     * @Given I am a guest
     */
    public function iAmAGuest(): void
    {
        $this->getSession()->restart();
    }

    /**
     * @Given I logged as :username
     */
    public function iLoggedAs($username): void
    {
        $this->getSession()->setRequestHeader(
            TestAgentAuthenticator::TEST_AGENT_FEATURE,
            TestAgentAuthenticator::getEncriptedToken($this->kernel->getContainer()->getParameter('app.test.agent_token'))
        );
        $this->getSession()->setRequestHeader(
            TestAgentAuthenticator::TEST_AGENT_USER,
            $username
        );
    }

// фейковый шаг, выводящий содержимое текущей страницы в консоль, чтобы проверить почему не срабатывают те или иные шаги
//    /**
//     * @Given /^Dump page$/
//     */
//    public function dumpPage()
//    {
//        dump($this->getSession()->getPage()->getContent());
//    }

}
