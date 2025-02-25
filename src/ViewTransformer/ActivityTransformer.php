<?php
/**
 * User: demius
 * Date: 11.01.2024
 * Time: 1:13
 */

namespace App\ViewTransformer;

use App\Entity\Activity;
use App\Model\Enum\Activity\ActivityTypeEnum;
use App\Model\Enum\DocStateEnum;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActivityTransformer
{
    private TranslatorInterface $translator;
    private TimeTransformer $timeTransformer;
    private UserTransformer $userTransformer;

    public function __construct(
        TranslatorInterface $translator,
        TimeTransformer $timeTransformer,
        UserTransformer $userTransformer
    ) {
        $this->translator = $translator;
        $this->timeTransformer = $timeTransformer;
        $this->userTransformer = $userTransformer;
    }

    public function transform(Activity $activity): array
    {
        $addInfo = $activity->getAddInfo();
        $typeLabelParams = match($activity->getType()) {
            ActivityTypeEnum::TaskChangeState, ActivityTypeEnum::TaskClose => [
                'old' => $addInfo['old']['name'] ?? '-',
                'new' => $addInfo['new']['name'] ?? '-',
            ],
            ActivityTypeEnum::DocChangeState => [
                'old' => $this->translator->trans(DocStateEnum::from($addInfo['old'])->label()),
                'new' => $this->translator->trans(DocStateEnum::from($addInfo['new'])->label()),
            ],
            default => [],
        };
        $typeLabel = $this->translator->trans($activity->getType()->label(), $typeLabelParams);

        return [
            'id' => $activity->getUuid(),
            'created' => $this->timeTransformer->ago($activity->getCreatedAt()),
            'type' => [
                'id' => $activity->getType()->value,
                'label' => $typeLabel
            ],
            'actor' => $this->userTransformer->transform($activity->getActor())
        ];
    }
}