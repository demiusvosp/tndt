<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 18:32
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\NotInProjectContextException;
use App\Service\ProjectContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InProjectContextRequestSubscriber implements EventSubscriberInterface
{
    private ProjectContext $projectContext;

    public function __construct(ProjectContext $projectContext)
    {
        $this->projectContext = $projectContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController' , -2],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        $configuration = $request->attributes->get('_in_project_context');
        if (!$configuration) {
            return;
        }

        if (!$this->projectContext->isProjectContext()) {
            throw new NotInProjectContextException();
        }

        $request = $event->getRequest();
        $request->attributes->set('project', $this->projectContext->getProject());
    }
}