<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 23:57
 */
declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Object\Dictionary\Dictionary;
use App\Object\JlobObjectInterface;
use JsonException;
use Symfony\Component\Form\DataTransformerInterface;

class JlobObjectToTextEditTransformer implements DataTransformerInterface
{
    /**
     * @param Dictionary $value
     * @return string
     * @throws JsonException
     */
    public function transform($value): string
    {
        if (!$value instanceof JlobObjectInterface) {
            throw new \InvalidArgumentException('"' . get_class($value) . '" must be implement JlobObjectInterface');
        }

        return $this->beautifyJson($value->jsonSerialize());
    }

    /**
     * @throws JsonException
     */
    public function reverseTransform($value): JlobObjectInterface
    {
        return new Dictionary(json_decode($value, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Отформатировать JSON красиво и удобно для редактирования.
     * @param array $array
     * @return string
     * @throws JsonException
     */
    private function beautifyJson(array $array): string
    {
        return json_encode($array, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}