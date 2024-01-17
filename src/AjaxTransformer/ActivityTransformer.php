<?php
/**
 * User: demius
 * Date: 11.01.2024
 * Time: 1:13
 */

namespace App\AjaxTransformer;

use App\Entity\Activity;
use DateTimeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActivityTransformer
{
    private TranslatorInterface $translator;
    private UserTransformer $userTransformer;

    public function __construct(TranslatorInterface $translator, UserTransformer $userTransformer)
    {
        $this->translator = $translator;
        $this->userTransformer = $userTransformer;
    }

    public function transform(Activity $activity): array
    {
        return [
            'id' => $activity->getUuid(),
            'created' => $activity->getCreatedAt()->format(DateTimeInterface::W3C),
            'type' => [
                'id' => $activity->getType()->value,
                'label' => $this->translator->trans($activity->getType()->label())
            ],
            'actor' => $this->userTransformer->transform($activity->getActor())
        ];
    }
}