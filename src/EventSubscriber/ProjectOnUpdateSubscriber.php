<?php
/**
 * User: demius
 * Date: 04.06.2023
 * Time: 22:42
 */

namespace App\EventSubscriber;

use App\Event\InProjectEvent;
use App\Model\Enum\AppEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectOnUpdateSubscriber implements EventSubscriberInterface
{
    use CurrentUserTrait;

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // AppEvents::PROJECT_EDIT_SETTINGS => 'onUpdateProject', настройка проекта не является работой над ним
            AppEvents::PROJECT_ARCHIVE => 'onUpdateProject',

            AppEvents::TASK_OPEN => 'onUpdateProject',
            AppEvents::TASK_EDIT => 'onUpdateProject',
            AppEvents::TASK_CHANGE_STAGE => 'onUpdateProject',
            AppEvents::TASK_CLOSE => 'onUpdateProject',

            AppEvents::DOC_CREATE => 'onUpdateProject',
            AppEvents::DOC_EDIT => 'onUpdateProject',
            AppEvents::DOC_CHANGE_STATE => 'onUpdateProject',

            AppEvents::COMMENT_ADD => 'onUpdateProject',
        ];
    }

    public function onUpdateProject(InProjectEvent $event): void
    {
        if ($this->isServiceUser()) {
            return;
        }
        if ($event->isObjectArchived()) {
            return;
        }

        $project = $event->getProject();
        $project->setUpdatedAt(new \DateTime());
    }
}