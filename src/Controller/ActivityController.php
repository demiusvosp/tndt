<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:28
 */

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\TaskRepository;
use App\Security\UserPermissionsEnum;
use App\Service\InProjectContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use function dump;

class ActivityController extends AbstractController
{
    private ActivityRepository $activityRepository;
    private TranslatorInterface $translator;

    public function __construct(ActivityRepository $activityRepository, TranslatorInterface $translator)
    {
        $this->activityRepository = $activityRepository;
        $this->translator = $translator;
    }

    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_TASK_VIEW)]
    public function taskActivity(Request $request, TaskRepository $taskRepository)
    {
        $task = $taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        $activities = [];
        foreach ($this->activityRepository->findByTask($task) as $activity) {
            $activities[] = [
                'type' => $activity->getType()->value,
                'createdAt' => $activity->getCreatedAt(),
                'actor' => $activity->getActor()?->getUsername(), // вот это бы оптимизировать
            ];
        }

        return new JsonResponse($activities);
    }
}