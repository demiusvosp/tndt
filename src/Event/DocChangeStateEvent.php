<?php
/**
 * User: demius
 * Date: 21.01.2024
 * Time: 15:16
 */

namespace App\Event;

use App\Entity\Doc;
use App\Model\Enum\DocStateEnum;

class DocChangeStateEvent extends DocEvent
{
    private DocStateEnum $oldState;
    private DocStateEnum $newState;

    public function __construct(Doc $doc, DocStateEnum $oldState, DocStateEnum $newState)
    {
        parent::__construct($doc);
        $this->oldState = $oldState;
        $this->newState = $newState;
    }

    /**
     * @return bool Документ стал архивным
     */
    public function isBecameArchived(): bool
    {
        return $this->oldState !== DocStateEnum::Archived
            && $this->newState === DocStateEnum::Archived;
    }

    public function isObjectArchived(): bool
    {
        return $this->getProject()->isArchived() || ($this->getDoc()->isArchived() && !$this->isBecameArchived());
    }

    public function getOldState(): DocStateEnum
    {
        return $this->oldState;
    }

    public function getNewState(): DocStateEnum
    {
        return $this->newState;
    }
}