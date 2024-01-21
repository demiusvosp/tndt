<?php
/**
 * User: demius
 * Date: 21.01.2024
 * Time: 15:16
 */

namespace App\Event;

use App\Entity\Doc;

class DocChangeStateEvent extends DocEvent
{
    private int $oldState;
    private int $newState;

    public function __construct(Doc $doc, int $oldState, int $newState, bool $isBecameArchived = false)
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
        return $this->oldState !== Doc::STATE_ARCHIVED
            && $this->newState === Doc::STATE_ARCHIVED;
    }

    public function getOldState(): int
    {
        return $this->oldState;
    }

    public function getNewState(): int
    {
        return $this->newState;
    }
}