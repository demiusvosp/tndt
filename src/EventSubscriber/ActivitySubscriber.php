<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 21:23
 */

namespace App\EventSubscriber;

use App\Contract\ActivityEventInterface;
use App\Event\ActivityEvent;
use App\Model\Enum\AppEvents;
use App\Service\ActivityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActivitySubscriber implements EventSubscriberInterface
{
    private ActivityFactory $activityFactory;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ActivityFactory $activityFactory,
        Security $security,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->activityFactory = $activityFactory;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AppEvents::TASK_OPEN => ['addActivity', -10],
            AppEvents::TASK_EDIT => ['addActivity', -10],
            AppEvents::TASK_CHANGE_STAGE => ['addActivity', -10],
            AppEvents::TASK_CLOSE => ['addActivity', -10],

            AppEvents::DOC_CREATE => ['addActivity', -10],
            AppEvents::DOC_EDIT => ['addActivity', -10],
            AppEvents::DOC_CHANGE_STATE => ['addActivity', -10],

            AppEvents::COMMENT_ADD => ['addActivity', -10],

            AppEvents::USER_CREATE => ['addActivity', -10],
            AppEvents::USER_EDIT => ['addActivity', -10],
            AppEvents::USER_SELF_EDIT  => ['addActivity', -10],
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
        $this->eventDispatcher->dispatch(new ActivityEvent($activity), AppEvents::ACTIVITY_ADD);
    }
}