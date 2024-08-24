<?php
/**
 * User: demius
 * Date: 24.08.2024
 * Time: 18:16
 */

namespace App\Event;

use App\Entity\Activity;

class ActivityEvent
{
    private Activity $activity;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }
}