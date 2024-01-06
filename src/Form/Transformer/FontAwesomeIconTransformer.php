<?php
/**
 * User: demius
 * Date: 15.04.2023
 * Time: 20:14
 */

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class FontAwesomeIconTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        return $value;
    }

    public function reverseTransform(mixed $value): string
    {
        return preg_replace('/<\w+ class="([\w -]+)">\w*<\/\w+>/', '${1}', (string) $value);
    }
}