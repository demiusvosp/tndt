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
use Symfony\Contracts\EventDispatcher\Event;

class DocEvent extends InProjectEvent
{
    private Doc $doc;

    public function __construct(Doc $doc)
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
}