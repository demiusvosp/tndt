<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 0:16
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Contract\WithJLOBFieldsInterface;
use App\Entity\Project;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class JSONBDeserializerListener
{
    private PropertyAccessorInterface $propertyAccessor;
    private SerializerInterface $serializer;

    public function __construct(PropertyAccessorInterface $propertyAccessor, SerializerInterface $serializer)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->serializer = $serializer;
    }

    public function postLoad(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof WithJLOBFieldsInterface) {
            return;
        }

        foreach ($entity->getJSLOBFields() as $field => $jlobClass) {
            $jlobData = $this->propertyAccessor->getValue($entity, $field);

            if (!$jlobData instanceof $jlobClass) {
                if (empty($jlobData)) {
                    $jlobObject = new $jlobClass();
                } else {
                    $jlobObject = $this->serializer->deserialize($jlobData, $jlobClass, 'json');
                }

                $this->propertyAccessor->setValue($entity, $field, $jlobObject);
            }
        }

    }

}