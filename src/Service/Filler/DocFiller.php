<?php
/**
 * User: demius
 * Date: 14.11.2021
 * Time: 0:27
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Doc;
use App\Exception\DomainException;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Repository\ProjectRepository;
use App\Service\DocService;

class DocFiller
{
    private ProjectRepository $projectRepository;
    private DocService $docService;

    public function __construct(ProjectRepository $projectRepository, DocService $docService)
    {
        $this->projectRepository = $projectRepository;
        $this->docService = $docService;
    }

    public function createFromForm(NewDocDTO $dto): Doc
    {
        $project = $this->projectRepository->find($dto->getProject());
        if (!$project) {
            throw new DomainException('Не найден проект к которому относится документ');
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
        if ($doc->getState() !== $dto->getState()) {
            $this->docService->changeState($doc, $dto->getState());
        }
    }
}