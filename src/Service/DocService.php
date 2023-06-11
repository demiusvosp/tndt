<?php
/**
 * User: demius
 * Date: 12.02.2022
 * Time: 23:05
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Doc;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\DocEvent;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Service\Filler\DocFiller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DocService
{
    private DocFiller $docFiller;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        DocFiller $docFiller,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->docFiller = $docFiller;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createDoc(NewDocDTO $request, User $author): Doc
    {
        $doc = $this->docFiller->createFromForm($request);
        $doc->setCreatedBy($author);
        $this->entityManager->persist($doc);

        $this->eventDispatcher->dispatch(new DocEvent($doc), AppEvents::DOC_CREATE);
        $this->entityManager->flush();
        return $doc;
    }

    public function editDoc(EditDocDTO $request, Doc $doc): Doc
    {
        $this->docFiller->fillFromEditForm($request, $doc);
        if ($doc->getState() !== $request->getState()) {
            $this->changeState($doc, $request->getState());
        }

        $this->eventDispatcher->dispatch(new DocEvent($doc), AppEvents::DOC_EDIT);
        $this->entityManager->flush();
        return $doc;
    }

    public function changeState(Doc $doc, int $newState): void
    {
        $isBecameArchived = !$doc->isArchived() && $newState===Doc::STATE_ARCHIVED;
        $doc->setState($newState);
        $this->eventDispatcher->dispatch(new DocEvent($doc, $isBecameArchived), AppEvents::DOC_CHANGE_STATE);
        $this->entityManager->flush();
    }
}