<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 0:56
 */
declare(strict_types=1);

namespace App\Controller;

use App\Model\Enum\ErrorCodesEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErrorController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(FlattenException $exception): Response
    {
        $response = new Response();
        $errorParams = [];
        if (ErrorCodesEnum::isValid($exception->getCode())) {
            // ошибки с собственными кодами
            $errorType = ErrorCodesEnum::from($exception->getCode());
            $errorCode = $exception->getCode();
            if (ErrorCodesEnum::hasCustomMessage($exception->getCode())) {
                $errorParams['{message}'] = $exception->getMessage();
            }

        } elseif (ErrorCodesEnum::isValid($exception->getStatusCode())) {
            // ошибки с http кодами
            $errorType = ErrorCodesEnum::from($exception->getStatusCode());
            $errorCode = $exception->getStatusCode();

        } else {
            // прочая неспецифическая ошибка
            $errorType = ErrorCodesEnum::COMMON();
            $errorCode = 500;
        }
        $response->setStatusCode($exception->getStatusCode());

        $statusLabel = $this->translator->trans(
            $errorType->label(),
            [],
            'errors'
        );
        $statusDescription = $this->translator->trans(
            $errorType->description(),
            $errorParams,
            'errors'
        );

        return $this->render(
            'error/error.html.twig',
            [
                'status_code' => $errorCode,
                'status_label' => $statusLabel,
                'status_description' => $statusDescription,
            ],
            $response
        );
    }
}