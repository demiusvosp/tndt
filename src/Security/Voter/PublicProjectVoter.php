<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 21:48
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Project;
use App\Exception\NotInProjectContextException;
use App\Repository\ProjectRepository;
use App\Security\ProjectSecurityRegistry;
use App\Security\UserPermissionsEnum;
use App\Service\ProjectContext;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PublicProjectVoter implements VoterInterface, LoggerAwareInterface
{
    private ProjectContext $projectContext;
    private ProjectSecurityRegistry $projectSecurityRegistry;
    private LoggerInterface $logger;

    public function __construct(ProjectContext $projectContext, ProjectSecurityRegistry $projectSecurityRegistry)
    {
        $this->projectContext = $projectContext;
        $this->projectSecurityRegistry = $projectSecurityRegistry;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $isPublic = false;
        if (empty($subject)) {
            // если запрос в рамках текущего проекта, он считается subject и его можно не указывать
            $project = $this->projectContext->getProject();
            if ($project) {
                $isPublic = $project->isPublic();
                $subject = $project->getSuffix();
            }
        } else {
            if ($subject instanceof Project) {
                $isPublic = $subject->isPublic();
            } else {
                $isPublic = $this->projectSecurityRegistry->isPublic($subject);
            }
        }

        if (!$isPublic) {
            // Voter работает только для публичных проектов
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $publicPermissions = UserPermissionsEnum::getPublicProjectGuestPermissions();
        foreach ($attributes as $attribute) {
            if (in_array($attribute, $publicPermissions, true)) {
                $this->logger->debug(
                    'Public project {project} grant permission {attribute}',
                    ['project' => $subject, 'attribute' => $attribute]);
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}