<?php
/**
 * User: demius
 * Date: 14.01.2024
 * Time: 22:41
 */

namespace App\ViewTransformer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserTransformer
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function transform(User $user): array
    {
        return [
            'username' => $user->getUsername(),
            'url' => $this->router->generate('user.index', ['username' => $user->getUsername()]),
            'isLocked' => $user->isLocked()
        ];
    }
}