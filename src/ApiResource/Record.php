<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use DateTimeImmutable;

use function parse_str;
use function parse_url;
use function urldecode;
use function urlencode;

use const PHP_URL_QUERY;

#[ApiResource(
    provider: RecordProvider::class,
    processor: StateProcessor::class,
    operations: [
        new Get(
            '/records/{market}/{date}',
            uriVariables: [
                'market',
                'date' => new Link(fromClass: self::class, identifiers: ['dateString']),
            ],
            requirements: ['market' => '[a-z]{2}-[A-Z]{2}', 'date' => '\d{8}'],
        ),
        new Post('/records'),
    ],
)]
class Record
{
    public function __construct(
        public readonly string $id,
        #[ApiProperty(readableLink: true)]
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
            'keyword' => isset($data['link']) ? self::extractKeyword($data['link']) : null,
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

    /** Parse an URL of web search engine and extract keyword from its query string */
    private static function extractKeyword(string $url): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (! $query) {
            return null;
        }

        parse_str($query, $query);

        $fields = ['q', 'wd'];

        foreach ($fields as $field) {
            if (isset($query[$field]) && $query[$field] !== '') {
                return urldecode($query[$field]);
            }
        }

        return null;
    }
}
