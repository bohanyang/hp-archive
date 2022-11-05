<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Parser\Utils;
use Manyou\BingHomepage\Record;
use Manyou\LeanStorage\CollectionIterator;
use Manyou\LeanStorage\Denormalize;
use Manyou\LeanStorage\LeanStorageClient;
use Manyou\LeanStorage\Normalize;
use Manyou\LeanStorage\Request\CreateObject;
use Manyou\LeanStorage\Request\UpdateObject;

use function urlencode;

class LeanCloudRepository
{
    public function __construct(
        private LeanStorageClient $client,
        private string $imageClass = 'Image',
        private string $recordClass = 'Archive',
    ) {
    }

    public function getClient(): LeanStorageClient
    {
        return $this->client;
    }

    public function createImageRequest(Image $image): CreateObject
    {
        return new CreateObject($this->imageClass, $this->normalizeImage($image));
    }

    public function createRecordRequest(Record $record): CreateObject
    {
        return new CreateObject($this->recordClass, $this->normalizeRecord($record));
    }

    public function updateImageRequest(Image $image): UpdateObject
    {
        return new UpdateObject($this->imageClass, $image->id, [
            'copyright' => $image->copyright,
            'wp' => $image->downloadable,
        ]);
    }

    public function exportImages(?DateTimeInterface $createdLaterThan = null): Generator
    {
        $iterator = (new CollectionIterator($this->client))(
            $this->imageClass,
            $this->getExportWhere($createdLaterThan),
        );

        foreach ($iterator as $image) {
            yield $this->denormalizeImage($image);
        }
    }

    public function exportRecords(?DateTimeInterface $createdLaterThan = null): Generator
    {
        $iterator = (new CollectionIterator($this->client))(
            $this->recordClass,
            $this->getExportWhere($createdLaterThan),
        );

        foreach ($iterator as $record) {
            yield $this->denormalizeRecord($record);
        }
    }

    private function getExportWhere(?DateTimeInterface $createdLaterThan)
    {
        $where = [];

        if ($createdLaterThan !== null) {
            $where['createdAt'] = ['$gt' => Normalize::date($createdLaterThan)];
        }

        return $where;
    }

    private function normalizeImage(Image $image): array
    {
        return self::deleteNullFields([
            'objectId' => $image->id,
            'name' => $image->name,
            'firstAppearedOn' => Normalize::date($image->debutOn),
            'urlbase' => $image->urlbase,
            'copyright' => $image->copyright,
            'wp' => $image->downloadable,
            'vid' => $image->video,
        ]);
    }

    private function denormalizeImage(array $image): array
    {
        return (array) (new Image(
            $image['objectId'],
            $image['name'],
            Denormalize::date($image['firstAppearedOn']),
            $image['urlbase'],
            $image['copyright'],
            $image['wp'],
            $image['vid'] ?? null,
        ));
    }

    private static function deleteNullFields(array $data): array
    {
        foreach ($data as $k => $v) {
            if (! isset($v)) {
                unset($data[$k]);
            }
        }

        return $data;
    }

    private function normalizeRecord(Record $record): array
    {
        return self::deleteNullFields([
            'objectId' => $record->id,
            'image' => Normalize::pointer($this->imageClass, $record->image->id),
            'date' => $record->date->format('Ymd'),
            'market' => $record->market,
            'info' => $record->title,
            'link' => null === $record->keyword ? null : 'https://www.bing.com/search?q=' . urlencode($record->keyword),
            'hs' => $record->hotspots,
            'msg' => $record->messages,
            'cs' => $record->coverstory,
        ]);
    }

    private function denormalizeRecord(array $record): array
    {
        return [
            'id' => $record['objectId'],
            'imageId' => $record['image']['objectId'],
            'date' => DateTimeImmutable::createFromFormat('YmdGp', $record['date'] . '0Z'),
            'market' => $record['market'],
            'title' => $record['info'],
            'keyword' => isset($record['link']) ? Utils::extractKeyword($record['link']) : null,
            'hotspots' => ($record['hs'] ?? []) === [] ? null : $record['hs'],
            'messages' => ($record['msg'] ?? []) === [] ? null : $record['msg'],
            'coverstory' => ($record['cs'] ?? []) === [] ? null : $record['cs'],
        ];
    }
}
