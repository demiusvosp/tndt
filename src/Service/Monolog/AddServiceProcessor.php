<?php
/**
 * User: demius
 * Date: 07.10.2024
 * Time: 22:42
 */

namespace App\Service\Monolog;

use Monolog\LogRecord;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function array_merge;

#[AutoconfigureTag("monolog.processor", ['handler' => 'graylog'])]
class AddServiceProcessor
{
    private string $tag;
    private ?string $tagName;

    public function __construct(string $tag, ?string $tagName)
    {
        $this->tag = $tag;
        $this->tagName = $tagName;
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $record['extra'] = array_merge(
            $record['extra'],
            [$this->tagName ?? 'tag' => $this->tag]
        );
        return $record;
    }
}