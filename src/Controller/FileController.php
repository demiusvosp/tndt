<?php
/**
 * User: demius
 * Date: 11.02.2025
 * Time: 22:40
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use function dump;

class FileController extends AbstractController
{
    public function get(string $path, Request $request)
    {
dump($path);
        BinaryFileResponse::trustXSendfileTypeHeader();
        $response = new BinaryFileResponse('/data/private/'.$path);
        $request->headers->set('X-Accel-Limit-Rate', 10000);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $path);
        $response->headers->set('X-Accel-Redirect', 'private-area/'.$path);
dump($response); //die();
        return $response;
    }
}