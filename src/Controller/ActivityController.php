<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:28
 */

namespace App\Controller;

use App\Exception\DomainException;
use App\Model\Enum\Activity\ActivitySubjectTypeEnum;
use App\Model\Enum\ErrorCodesEnum;
use App\Repository\ActivityRepository;
use App\ViewTransformer\ActivityTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnexpectedValueException;
use ValueError;
use function array_pop;
use function count;
use function min;

class ActivityController extends AbstractController
{
    private const DEFAULT_ACTIVITY_LIMIT = 25;

    private ActivityRepository $activityRepository;
    private ActivityTransformer $ajaxTransformer;
    private TranslatorInterface $translator;

    public function __construct(
        ActivityRepository $activityRepository,
        ActivityTransformer $ajaxTransformer,
        TranslatorInterface $translator
    )
    {
        $this->activityRepository = $activityRepository;
        $this->ajaxTransformer = $ajaxTransformer;
        $this->translator = $translator;
    }

    public function listBySubject(
        string $type,
        string $id,
        int $limit = self::DEFAULT_ACTIVITY_LIMIT
    ): JsonResponse {
        $limit = min($limit, self::DEFAULT_ACTIVITY_LIMIT);

        try { // @todo before tndt-135
            $activities = $this->activityRepository->findBySubject(
                ActivitySubjectTypeEnum::from($type),
                $id,
                $limit + 1
            );
            $hasMore = count($activities) > $limit;
            if ($hasMore) {
                array_pop($activities);
            }

            $items = [];
            foreach ($activities as $activity) {
                $items[] = $this->ajaxTransformer->transform($activity);
            }

            return new JsonResponse([
                'items' => $items,
                'hasMore' => $hasMore,
                'emptyMessage' => count($items) === 0 ? $this->translator->trans('activity.empty') : '',
            ]);
        } catch (DomainException | ValueError $e) {// @todo пока не будет решено всюду в рамках tndt-135
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