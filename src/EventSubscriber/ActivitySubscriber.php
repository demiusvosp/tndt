<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 21:23
 */

namespace App\EventSubscriber;

use App\Contract\ActivityEventInterface;
use App\Entity\Activity;
use App\Event\AppEvents;
use App\Model\Enum\ActivityTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivitySubscriber implements EventSubscriberInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
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
        $activity = new Activity(ActivityTypeEnum::fromEventName($eventName));

        /** @noinspection PhpParamsInspection */
        $activity->setActor($this->security->getUser());
        $activity->setActivitySubject($event->getActivitySubject());

        $this->entityManager->persist($activity);
    }
}