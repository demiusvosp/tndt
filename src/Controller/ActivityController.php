<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:28
 */

namespace App\Controller;

use App\AjaxTransformer\ActivityTransformer;
use App\Model\Enum\ActivitySubjectTypeEnum;
use App\Repository\ActivityRepository;
use App\Repository\TaskRepository;
use App\Security\UserPermissionsEnum;
use App\Service\InProjectContext;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_pop;
use function count;
use function dump;
use function min;

class ActivityController extends AbstractController
{
    private const DEFAULT_ACTIVITY_LIMIT = 25;
    private const DATE_FORMAT = DateTimeInterface::W3C;

    private ActivityRepository $activityRepository;
    private ActivityTransformer $ajaxTransformer;

    public function __construct(ActivityRepository $activityRepository, ActivityTransformer $ajaxTransformer)
    {
        $this->activityRepository = $activityRepository;
        $this->ajaxTransformer = $ajaxTransformer;
    }

    public function listBySubject(
        string $type,
        string $id,
        int $limit = self::DEFAULT_ACTIVITY_LIMIT
    ): JsonResponse {
        $limit = min($limit, self::DEFAULT_ACTIVITY_LIMIT);

        $activities = $this->activityRepository->findBySubject(
            $type,
            $id,
            $limit + 1
        );
        $hasMore = count($activities) > $limit;
        array_pop($activities);

        $items = [];
        foreach ($activities as $activity) {
            $items[] = $this->ajaxTransformer->transform($activity);
        }

        return new JsonResponse([
            'items' => $items,
            'hasMore' => $hasMore
        ]);
    }
}