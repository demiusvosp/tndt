<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 21:23
 */

namespace App\EventSubscriber;

use App\Contract\ActivityEventInterface;
use App\Model\Enum\AppEvents;
use App\Service\ActivityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivitySubscriber implements EventSubscriberInterface
{
    private ActivityFactory $activityFactory;
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(ActivityFactory $activityFactory, Security $security, EntityManagerInterface $entityManager)
    {
        $this->activityFactory = $activityFactory;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AppEvents::TASK_OPEN => ['addActivity'],
            AppEvents::TASK_EDIT => ['addActivity'],
            AppEvents::TASK_CHANGE_STAGE => ['addActivity'],
            AppEvents::TASK_CLOSE => ['addActivity'],

            AppEvents::DOC_CREATE => ['addActivity'],
            AppEvents::DOC_EDIT => ['addActivity'],
            AppEvents::DOC_CHANGE_STATE => ['addActivity'],

            AppEvents::COMMENT_ADD => ['addActivity'],
        ];
    }

    public function addActivity(ActivityEventInterface $event, string $eventName): void
    {
        /** @noinspection PhpParamsInspection */
        $activity = $this->activityFactory->createFromEvent(
            $eventName,
            $event,
            $this->security->getUser()
        );
        $this->entityManager->persist($activity);
    }
}