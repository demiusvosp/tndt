<?php
/**
 * User: demius
 * Date: 23.02.2024
 * Time: 13:53
 */

namespace App\ViewModel;

use App\Model\Enum\FlashMessageTypeEnum;

class FlashMessage
{
    private FlashMessageTypeEnum $type;
    private string $message;

    public function __construct(FlashMessageTypeEnum $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function getType(): FlashMessageTypeEnum
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}