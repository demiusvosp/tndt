<?php
/**
 * User: demius
 * Date: 08.10.2025
 * Time: 00:28
 */

namespace App\Service\Twig;

use App\Contract\WithFilesInterface;
use App\Model\Enum\File\AttachmentEntityEnum;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class FileExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'file_gallery_widget',
                [$this, 'fileGalleryWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            )
        ];
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function fileGalleryWidget(Environment $environment, WithFilesInterface $ownerObject): string
    {
        return $environment->render(
            'file/file_gallery_widget.html.twig',
            [
                'project' => $ownerObject->getSuffix(),
                'entityType' => AttachmentEntityEnum::fromOwner($ownerObject::class)->value,
                'entityId' => $ownerObject->getNo(),
            ]
        );
    }
}