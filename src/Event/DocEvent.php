<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 23:13
 */
declare(strict_types=1);

namespace App\Event;

use App\Contract\Event\IsArchivedObjectInterface;
use App\Entity\Doc;
use App\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

class DocEvent extends InProjectEvent implements IsArchivedObjectInterface
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
        return $this->getDoc()->isArchived() && !$this->isBecameArchived();
    }
}