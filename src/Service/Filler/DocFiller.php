<?php
/**
 * User: demius
 * Date: 14.11.2021
 * Time: 0:27
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Doc;
use App\Entity\Project;
use App\Exception\BadRequestException;
use App\Exception\DomainException;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Repository\ProjectRepository;

class DocFiller
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function createFromForm(NewDocDTO $dto): Doc
    {
        $project = $this->projectRepository->find($dto->getProject());
        if (!$project) {
            throw new DomainException('Не найден проект к которому относится задача');
        }

        $doc = new Doc($project);
        $doc->setCaption($dto->getCaption());
        $doc->setAbstract($dto->getAbstract());
        $doc->setBody($dto->getBody());

        return $doc;
    }

    public function fillFromEditForm(EditDocDTO $dto, Doc $doc): void
    {
        $doc->setCaption($dto->getCaption());
        $doc->setAbstract($dto->getAbstract());
        $doc->setBody($dto->getBody());
    }
}