<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 0:56
 */
declare(strict_types=1);

namespace App\Controller;

use App\Exception\ErrorCodesEnum;
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
        if (ErrorCodesEnum::isValid($exception->getCode())) {
            // ошибки с собственными кодами
            $errorType = ErrorCodesEnum::from($exception->getCode());
            $statusCode = $exception->getCode();
        } elseif (ErrorCodesEnum::isValid($exception->getStatusCode())) {
            // ошибки с http кодами
            $errorType = ErrorCodesEnum::from($exception->getStatusCode());
            $statusCode = $exception->getStatusCode();
        } else {
            // прочая неспецифическая ошибка
            $errorType = ErrorCodesEnum::COMMON();
            $statusCode = 500;
        }

        $statusLabel = $this->translator->trans(
            $errorType->label(),
            [],
            'errors'
        );
        $statusDescription = $this->translator->trans(
            $errorType->description(),
            [],
            'errors'
        );

        return $this->render(
            'error/error.html.twig',
            [
                'status_code' => $statusCode,
                'status_label' => $statusLabel,
                'status_description' => $statusDescription,
            ]
        );
    }
}