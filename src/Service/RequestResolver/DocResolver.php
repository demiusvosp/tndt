<?php
/**
 * User: demius
 * Date: 13.10.2024
 * Time: 01:23
 */

namespace App\Service\RequestResolver;

use App\Entity\Doc;
use App\Exception\NotFoundException;
use App\Repository\DocRepository;
use App\Specification\Doc\ByDocIdSpec;
use App\Specification\Doc\BySlugSpec;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function dump;
use function is_subclass_of;


class DocResolver implements ValueResolverInterface
{
    private DocRepository $docRepository;

    public function __construct(DocRepository $docRepository)
    {
        $this->docRepository = $docRepository;
    }

    /**
     * @throws NotFoundException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Doc::class) {
            return [];
        }

        $suffix = $request->attributes->has('project') ? $request->attributes->get('project')->getSuffix()
            : $request->attributes->get('suffix');
        if (!$suffix) {
            return [];
        }

        try {
            $slug = $request->attributes->get('slug');
            if ($slug) {
                return [
                    $this->docRepository->matchSingleResult(new BySlugSpec($suffix, $slug))
                ];
            }
            $docId = $request->attributes->get('docId');
            if ($docId) {
                return [
                    $this->docRepository->matchSingleResult(new ByDocIdSpec($docId))
                ];
            }
            return [];
        } catch(NoResultException) {
            throw new NotFoundException();
        }
    }
}