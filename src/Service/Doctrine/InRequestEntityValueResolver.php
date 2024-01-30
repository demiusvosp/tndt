<?php
/**
 * User: demius
 * Date: 30.01.2024
 * Time: 21:07
 */

namespace App\Service\Doctrine;

use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;


/**
 * Декоратор ValueResolver'а добавляющий сущность, найденную при резолве аргументов конструктора в Request, так
 *   как это делал раньше ParamConverter. Нам необходим, для того, чтобы события зависящие от Request, например билдеры
 *   меню не были вынуждены заново загружать уже загруженные контроллером сущности
 */
#[AsDecorator('doctrine.orm.entity_value_resolver')]
class InRequestEntityValueResolver implements ValueResolverInterface
{
    public function __construct(
        #[AutowireDecorated] private readonly EntityValueResolver $inner
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $object = $this->inner->resolve($request, $argument);

        if (!empty($object)) {
            $request->attributes->set($argument->getName(), $object[0]);
        }

        return $object;
    }
}