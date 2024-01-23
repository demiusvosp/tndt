<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 23:13
 */
declare(strict_types=1);

namespace App\Event;

use App\Entity\Doc;
use App\Entity\Project;

class DocEvent extends InProjectEvent
{
    private Doc $doc;
    /**
     * @var bool Документ был архивным ранее, а не стал таковым
     */
    private bool $isBecameArchived;

    public function __construct(Doc $doc, bool $isBecameArchived = false)
    {
        $this->doc = $doc;
        $this->isBecameArchived = $isBecameArchived;
    }

    /**
     * @return Doc
     */
    public function getDoc(): Doc
    {
        return $this->doc;
    }

    public function getProject(): Project
    {
        return $this->doc->getProject();
    }

    public function isBecameArchived(): bool
    {
        return $this->isBecameArchived;
    }

    public function isObjectArchived(): bool
    {
        return parent::isObjectArchived() || ($this->getDoc()->isArchived() && !$this->isBecameArchived());
    }
}