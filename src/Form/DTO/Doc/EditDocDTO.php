<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 17:47
 */
declare(strict_types=1);

namespace App\Form\DTO\Doc;

use App\Entity\Doc;
use App\Model\Enum\DocStateEnum;
use Symfony\Component\Validator\Constraints as Assert;

class EditDocDTO
{
    #[Assert\NotBlank]
    private string $project;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255, maxMessage: "doc.caption.to_long")]
    private string $caption;

    #[Assert\Choice(choices: [DocStateEnum::Normal, DocStateEnum::Deprecated, DocStateEnum::Archived])]
    private DocStateEnum $state;

    #[Assert\Length(max: 1000, maxMessage: "doc.abstract.to_long")]
    private string $abstract;

    #[Assert\Length(min: 1, max: 50000, maxMessage: "doc.body.error.to_long")]
    private string $body;


    public function __construct(Doc $doc)
    {
        $this->project = $doc->getSuffix();
        $this->caption = $doc->getCaption();
        $this->state = $doc->getState();
        $this->abstract = $doc->getAbstract(true);
        $this->body = $doc->getBody();
    }

    public function getProject(): string
    {
        return $this->project;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): EditDocDTO
    {
        $this->caption = $caption;
        return $this;
    }

    public function getState(): DocStateEnum
    {
        return $this->state;
    }

    public function setState(DocStateEnum $state): EditDocDTO
    {
        $this->state = $state;
        return $this;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function setAbstract(string $abstract): EditDocDTO
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): EditDocDTO
    {
        $this->body = $body;
        return $this;
    }
}