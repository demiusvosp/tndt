<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 17:47
 */
declare(strict_types=1);

namespace App\Form\DTO\Doc;

use App\Entity\Doc;
use Symfony\Component\Validator\Constraints as Assert;

class EditDocDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private string $project;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255, maxMessage="doc.caption.to_long")
     */
    private string $caption = '';

    /**
     * @var string
     * @Assert\Length(max=1000, maxMessage="doc.abstract.to_long")
     */
    private string $abstract = '';

    /**
     * @var string
     * @Assert\Length(max=50000, maxMessage="doc.body.error.to_long")
     */
    private string $body = '';

    /**
     * @var int
     * @Assert\Choice(choices={0, 1, 2})
     */
    private int $state;

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }


    public function __construct(Doc $doc)
    {
        $this->project = $doc->getSuffix();
        $this->caption = $doc->getCaption();
        $this->abstract = $doc->getAbstract(true);
        $this->body = $doc->getBody();
        $this->state = $doc->getState();
    }

    /**
     * @param string $caption
     * @return EditDocDTO
     */
    public function setCaption(string $caption): EditDocDTO
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return EditDocDTO
     */
    public function setAbstract(string $abstract): EditDocDTO
    {
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return EditDocDTO
     */
    public function setBody(string $body): EditDocDTO
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return EditDocDTO
     */
    public function setState(int $state): EditDocDTO
    {
        $this->state = $state;
        return $this;
    }
}