<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 0:49
 */
declare(strict_types=1);

namespace App\Event;

use App\Contract\ActivityEventInterface;
use App\Contract\ActivitySubjectInterface;
use App\Contract\WithProjectInterface;
use App\Entity\Comment;
use App\Entity\Project;
use App\Exception\DomainException;

class CommentEvent extends InProjectEvent implements ActivityEventInterface
{
    private $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getProject(): Project
    {
        $owner = $this->comment->getOwnerEntity();
        if (!$owner instanceof WithProjectInterface) {
            throw new DomainException('Comment in not project\'s entity. Need to update CommentEvent');
        }

        return $owner->getProject();
    }

    public function getActivitySubject(): ?ActivitySubjectInterface
    {
        $commentOwner = $this->comment->getOwnerEntity();
        if ($commentOwner instanceof ActivitySubjectInterface) {
            return $commentOwner;
        }
        return null;
    }
}