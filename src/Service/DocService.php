<?php
/**
 * User: demius
 * Date: 12.02.2022
 * Time: 23:05
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Doc;
use App\Event\AppEvents;
use App\Event\DocEvent;
use App\Exception\BadRequestException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DocService
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function changeState(Doc $doc, int $newState): void
    {
        $isBecameArchived = $doc->isArchived();
        $doc->setState($newState);
        $this->eventDispatcher->dispatch(new DocEvent($doc, $isBecameArchived), AppEvents::DOC_CHANGE_STATE);
    }
}