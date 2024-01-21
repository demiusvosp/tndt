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

    public function __construct(Doc $doc, DocStateEnum $oldState, DocStateEnum $newState, bool $isBecameArchived = false)
    {
        parent::__construct($doc, $isBecameArchived);
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

    public function getOldState(): DocStateEnum
    {
        return $this->oldState;
    }

    public function getNewState(): DocStateEnum
    {
        return $this->newState;
    }
}