<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 23:57
 */
declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Object\Base\Dictionary;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

class JlobObjectToTextEditTransformer implements DataTransformerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @TODO это что, на каждый такой тип jlob-объекта свой трансформер, отличающийся типом? Ну или свое описание сервиса, сетящего тип.
     * @param Dictionary $value
     * @return string
     */
    public function transform($value): string
    {
        return $this->serializer->serialize($value, 'json');
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function reverseTransform($value): Dictionary
    {
        return $this->serializer->deserialize($value, Dictionary::class, 'json');
    }
}