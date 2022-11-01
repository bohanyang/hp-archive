<?php

declare(strict_types=1);

namespace Manyou\BingHomepage;

use DateTimeImmutable;
use Manyou\BingHomepage\Parser\Utils;

use function urlencode;

class Record
{
    public function __construct(
        public readonly string $id,
        public Image $image,
        public readonly DateTimeImmutable $date,
        public readonly string $market,
        public readonly string $title,
        public readonly ?string $keyword = null,
        public readonly ?string $headline = null,
        public readonly ?string $description = null,
        public readonly ?string $quickfact = null,
        public readonly ?array $hotspots = null,
        public readonly ?array $messages = null,
        public readonly ?array $coverstory = null,
        ...$args,
    ) {
    }

    public function getDateString(): string
    {
        return $this->date->format('Ymd');
    }

    public static function createFromLeanCloud(array $data): array
    {
        return [
            'id' => $data['objectId'],
            'image_id' => $data['image']['objectId'],
            'date' => DateTimeImmutable::createFromFormat('Ymd\TH:i:s.vp', $data['date'] . 'T00:00:00.000Z'),
            'market' => $data['market'],
            'title' => $data['info'],
            'keyword' => isset($data['link']) ? Utils::extractKeyword($data['link']) : null,
            'hotspots' => ($data['hs'] ?? []) === [] ? null : $data['hs'],
            'messages' => ($data['msg'] ?? []) === [] ? null : $data['msg'],
            'coverstory' => ($data['cs'] ?? []) === [] ? null : $data['cs'],
        ];
    }

    public function toLeanCloud(): array
    {
        return [
            'objectId' => $this->id,
            'image' => ['__type' => 'Pointer', 'className' => 'Image', 'objectId' => $this->image->id],
            'date' => $this->date->format('Ymd'),
            'market' => $this->market,
            'info' => $this->title,
            'link' => null === $this->keyword ? null : 'https://www.bing.com/search?q=' . urlencode($this->keyword),
            'hs' => $this->hotspots,
            'msg' => $this->messages,
            'cs' => $this->coverstory,
        ];
    }
}
