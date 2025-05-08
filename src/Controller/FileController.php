<?php
/**
 * User: demius
 * Date: 11.02.2025
 * Time: 22:40
 */

namespace App\Controller;

use App\Entity\User;
use App\Exception\DomainException;
use App\Exception\NotInProjectContextException;
use App\Model\Enum\ErrorCodesEnum;
use App\Model\Enum\File\AttachmentEntityEnum;
use App\Model\Enum\File\FileTargetEnum;
use App\Repository\ProjectRepository;
use App\Service\File\FileManager;
use App\Service\File\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnexpectedValueException;

class FileController extends AbstractController
{
    private FileUploader $fileService;
    private ProjectRepository $projectRepository;
    private TranslatorInterface $translator;

    public function __construct(FileUploader $fileService, ProjectRepository $projectRepository, TranslatorInterface $translator)
    {
        $this->fileService = $fileService;
        $this->projectRepository = $projectRepository;
        $this->translator = $translator;
    }

    public function get(string $path, Request $request)
    {
        BinaryFileResponse::trustXSendfileTypeHeader();
        $response = new BinaryFileResponse(FileManager::PRIVATE_DIR_BASE_PATH . $path);
        $request->headers->set('X-Accel-Limit-Rate', 10000);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $path);
        $response->headers->set('X-Accel-Redirect', 'private-area/' . $path);

        return $response;
    }

    public function upload(Request $request): Response
    {
        try { // @todo before tndt-135
            /** @var User $owner */
            $owner = $this->getUser();
            $project = $this->projectRepository->findBySuffix($request->request->get('project', ''));
            if (!$project) {
                throw new NotInProjectContextException();
            }
            $this->fileService->uploadAttachment(
                $request->files->get('file'),
                FileTargetEnum::from($request->request->get('target')),
                $owner,
                $project,
                AttachmentEntityEnum::from($request->request->get('entityType')),
                (int)$request->request->get('entityId')
            );
            return new Response();
        } catch (DomainException $e) { // @todo пока не будет решено всюду в рамках tndt-135
            try {
                $errorType = ErrorCodesEnum::from($e->getCode());
            } catch (UnexpectedValueException $e) {
                $errorType = ErrorCodesEnum::COMMON();
            }
            return new JsonResponse(
                [
                    'error' => $errorType->getValue(),
                    'message' => $this->translator->trans($errorType->label(), domain: 'errors')
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}