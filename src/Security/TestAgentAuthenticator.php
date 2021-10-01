<?php
/**
 * User: demius
 * Date: 01.10.2021
 * Time: 18:28
 */
declare(strict_types=1);

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


/**
 * TestAgentAuthenticator - аутенфикатор тестовых пользователей, которые в процессе тестирования ходят по системе.
 * Чтобы им не приходилось для этого заходить на реальную форму логина, они аутенфицируются по токену, переданному в
 *   заголовках. Сейчас токен лежит в параметрах контейнера, снаружи передается его хеш.
 * @TODO при публикации кода, необходимо переложить его в секрет
 */
class TestAgentAuthenticator extends AbstractGuardAuthenticator
{
    public const TEST_AGENT_FEATURE = 'TAF_TOKEN';
    public const TEST_AGENT_USER = 'TAF_USER';

    private const TOKEN_HASH_ALGO = 'sha256';

    private string $testAgentToken = '';
    private Security $security;
    private LoggerInterface $logger;

    public function __construct(string $testAgentToken, Security $security, LoggerInterface $logger)
    {
        $this->testAgentToken = $testAgentToken;
        $this->security = $security;
        $this->logger = $logger;
    }

    public static function getEncriptedToken(string $token): string
    {
        return hash(self::TOKEN_HASH_ALGO, $token);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Auth header required', 401);
    }

    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        $testAgentFeature = $request->headers->get(self::TEST_AGENT_FEATURE);
        $this->logger->info(' Checking support on TestAgentAuthenticator with token: {token}', ['token' => $testAgentFeature]);
        if (!$testAgentFeature) {
            return false;
        }

        return true;
    }

    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->headers->get(self::TEST_AGENT_USER),
            'token' => $request->headers->get(self::TEST_AGENT_FEATURE)
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials === null) {
            return null;
        }
        $user = $userProvider->loadUserByUsername($credentials['username']);
        if (!$user) {
            return null;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $this->logger->info('Check credentials {credentials}', ['credentials' => $credentials]);
        return $credentials['username'] === $user->getUsername()
            && $credentials['token'] === self::getEncriptedToken($this->testAgentToken);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->logger->info('Authenticator TestAgent failure');
        return new Response('Auth error', 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $this->logger->info('Authenticator TestAgent success');
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}