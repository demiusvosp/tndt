<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 23:13
 */
declare(strict_types=1);

namespace App\Event;

use App\Contract\ActivityEventInterface;
use App\Contract\ActivitySubjectInterface;
use App\Entity\Doc;
use App\Entity\Project;

class DocEvent extends InProjectEvent implements ActivityEventInterface
{
    private Doc $doc;

    public function __construct(Doc $doc, bool $isBecameArchived = false)
    {
        $this->doc = $doc;
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

    public function getActivitySubject(): ActivitySubjectInterface
    {
        return $this->doc;
    }
}