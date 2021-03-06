<?php
/**
 * User: demius
 * Date: 29.08.2021
 * Time: 11:34
 */
declare(strict_types=1);

namespace App\Form\DTO\Doc;

use App\Entity\Doc;
use App\Entity\Project;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class NewDocDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @EntityExist(entity="App\Entity\Project", property="suffix")
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
     * @Assert\Length(max=50000, maxMessage="doc.body.to_long")
     */
    private string $body = '';


    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     * @return NewDocDTO
     */
    public function setProject(string $project): NewDocDTO
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     * @return NewDocDTO
     */
    public function setCaption(string $caption): NewDocDTO
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
     * @return NewDocDTO
     */
    public function setAbstract(string $abstract): NewDocDTO
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
     * @return NewDocDTO
     */
    public function setBody(string $body): NewDocDTO
    {
        $this->body = $body;
        return $this;
    }
}