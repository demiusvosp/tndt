<?php
/**
 * User: demius
 * Date: 14.11.2021
 * Time: 0:27
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Doc;
use App\Entity\User;
use App\Exception\DomainException;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Repository\ProjectRepository;
use App\Service\DocService;

class DocFiller
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function createFromForm(NewDocDTO $dto, ?User $author = null): Doc
    {
        $project = $this->projectRepository->find($dto->getProject());
        if (!$project) {
            throw new DomainException('Не найден проект к которому относится документ');
        }

        $doc = new Doc($project, $author);
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
