<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 21:48
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\UserPermissionsEnum;
use App\Service\ProjectManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PublicProjectVoter implements VoterInterface, LoggerAwareInterface
{
    private ProjectManager $projectManager;
    private LoggerInterface $logger;

    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $project = $this->projectManager->getProject();
        if (!$project) {
            // Voter работает только в контексте проекта
            return VoterInterface::ACCESS_ABSTAIN;
        }
        if (!$project->isPublic()) {
            $this->logger->debug('Its not public project - abstain');
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $publicPermissions = UserPermissionsEnum::getPublicProjectGuestPermissions();
        foreach ($attributes as $attribute) {
            if (in_array($attribute, $publicPermissions, true)) {
                $this->logger->debug('Public project grant this permission', ['permission' => $attribute]);
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}