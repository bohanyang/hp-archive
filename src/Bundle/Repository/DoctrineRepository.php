<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use App\Bundle\Doctrine\Table\ImagesTable;
use App\Bundle\Doctrine\Table\RecordsTable;
use DateTimeImmutable;
use Mango\Doctrine\SchemaProvider;
use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Record;

use function array_keys;
use function array_values;

class DoctrineRepository
{
    public function __construct(private SchemaProvider $schema)
    {
    }

    public function createImage(Image $image): void
    {
        $this->schema->createQuery()
            ->insert(ImagesTable::NAME, (array) $image)
            ->executeStatement();
    }

    public function importImages(array $images): void
    {
        $this->schema->createQuery()->bulkInsert(ImagesTable::NAME, ...$images);
    }

    public function importRecords(array $records): void
    {
        $this->schema->createQuery()->bulkInsert(RecordsTable::NAME, ...$records);
    }

    public function updateImage(Image $image, bool $baseUrl = false): void
    {
        $q = $this->schema->createQuery();
        $q->update(ImagesTable::NAME, [
            'copyright' => $image->copyright,
            'downloadable' => $image->downloadable,
        ] + ($baseUrl ? ['urlbase' => $image->urlbase] : []))
            ->where(name: $image->name)
            ->executeStatement();
    }

    public function getRecordsByImageId(string $id): array
    {
        $q = $this->schema->createQuery();
        $q->from(RecordsTable::NAME)
            ->select('title', 'market', 'date', 'keyword')
            ->where(image_id: $id)
            ->orderBy('date');

        return $q->fetchAllAssociativeFlat();
    }

    public function createRecord(Record $record): void
    {
        $this->schema->createQuery()
            ->insert(RecordsTable::NAME, (array) $record)
            ->executeStatement();
    }

    public function updateRecord(Record $record): void
    {
        $this->schema->createQuery()
            ->update(RecordsTable::NAME, (array) $record)
            ->where(id: $record->id, date: $record->date, market: $record->market)
            ->executeStatement(1);
    }

    public function getImagesByDate(DateTimeImmutable $date): array
    {
        $q = $this->schema->createQuery();

        $records = $q->from(RecordsTable::NAME, 'image_id', 'market')
            ->select()
            ->where(date: $date)
            ->fetchColumnGrouped();

        if ($records === []) {
            return [];
        }

        $imageIds = array_keys($records);

        $q = $this->schema->createQuery();

        $images = $q->from(ImagesTable::NAME, 'id', 'name', 'urlbase')
            ->select()
            ->where($q->in('id', $imageIds))
            ->fetchAllAssociativeIndexed();

        foreach ($records as $imageId => $markets) {
            $images[$imageId]['markets'] = $markets;
        }

        return array_values($images);
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schema->createQuery();

        $q->from(ImagesTable::NAME)
            ->select()
            ->where(name: $name)
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        return new Image(...$data);
    }

    /** @return Image[] */
    public function browse(DateTimeImmutable $cursor, DateTimeImmutable $prevCursor): array
    {
        $q = $this->schema->createQuery();

        $q->from(ImagesTable::NAME, 'name', 'urlbase')
            ->select()
            ->where($q->gt('debutOn', $cursor), $q->lte('debutOn', $prevCursor))
            ->addOrderBy('debutOn', 'DESC')
            ->addOrderBy('id', 'DESC');

        return $q->fetchAllAssociativeFlat();
    }

    public function getImageById(string $id): ?Image
    {
        $q = $this->schema->createQuery();

        $q->from(ImagesTable::NAME)
            ->select()
            ->where(id: $id)
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        return new Image(...$data);
    }

    public function getRecord(string $market, ?DateTimeImmutable $date = null): ?Record
    {
        $q = $this->schema->createQuery();

        $q->from(RecordsTable::NAME, 'r')
            ->select()
            ->where(market: $market)
            ->orderBy('date', 'DESC');

        if ($date !== null) {
            $q->andWhere(date: $date);
        }

        $q->joinOn([ImagesTable::NAME, 'i'], 'id', 'image_id')
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        $record          = $data['r'];
        $record['image'] = new Image(...$data['i']);
        $record          = new Record(...$record);

        return $record;
    }

    /** @return array[] */
    public function exportImages(): array
    {
        return $this->schema
            ->createQuery()
            ->from(ImagesTable::NAME)
            ->select()
            ->orderBy('id')
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportImagesWhere(): array
    {
        $q = $this->schema
            ->createQuery();

        return $q->from(ImagesTable::NAME)
            ->select()
            ->orderBy('id')
            ->where(debutOn: new DateTimeImmutable('2022-11-20T00:00:00.000000Z'))
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportRecordsWhere(): array
    {
        $q = $this->schema
            ->createQuery();

        return $q->from(RecordsTable::NAME)
            ->select()
            ->orderBy('id')
            ->where(date: new DateTimeImmutable('2022-11-19T00:00:00.000000Z'))
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportRecords(): array
    {
        return $this->schema
            ->createQuery()
            ->from(RecordsTable::NAME)
            ->select()
            ->orderBy('id')
            ->fetchAllAssociativeFlat();
    }

    public function getMarketsPendingOrExisting(DateTimeImmutable $date): array
    {
        return $this->schema->createQuery()
            ->from(RecordsTable::NAME, 'r')
            ->select('market')
            ->where(date: $date)
            ->fetchFirstColumn();
    }
}
