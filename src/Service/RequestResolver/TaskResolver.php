<?php
/**
 * User: demius
 * Date: 13.10.2024
 * Time: 19:25
 */

namespace App\Service\RequestResolver;
use App\Entity\Task;
use App\Exception\NotFoundException;
use App\Repository\TaskRepository;
use App\Specification\Task\ByTaskIdSpec;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TaskResolver implements ValueResolverInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @throws NotFoundException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Task::class) {
            return [];
        }

        $taskId = $request->attributes->get('taskId');
        if (!$taskId) {
            return [];
        }

        try {
            return [
                $this->taskRepository->matchSingleResult(new ByTaskIdSpec($taskId))
            ];
        } catch (NoResultException) {
            throw new NotFoundException();
        }
    }
}