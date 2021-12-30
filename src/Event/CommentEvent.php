<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 0:49
 */
declare(strict_types=1);

namespace App\Event;

use App\Entity\Comment;
use Symfony\Contracts\EventDispatcher\Event;

class CommentEvent extends Event
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
}