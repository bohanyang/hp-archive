<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use App\Bundle\ApiResource\ImageTask;
use App\Bundle\ApiResource\RecordTask;
use App\Bundle\Doctrine\Table\ImagesTable;
use App\Bundle\Doctrine\Table\ImageTasksTable;
use App\Bundle\Doctrine\Table\RecordsTable;
use App\Bundle\Doctrine\Table\RecordTasksTable;
use DateTimeImmutable;
use Generator;
use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Record;
use Manyou\Mango\Doctrine\SchemaProvider;
use Manyou\Mango\TaskQueue\Doctrine\Table\TaskLogsTable;
use Manyou\Mango\TaskQueue\Doctrine\Table\TasksTable;
use Symfony\Component\Uid\Ulid;

use function array_keys;
use function array_values;

class DoctrineRepository
{
    public function __construct(private SchemaProvider $schema)
    {
    }

    public function getSchemaProvider(): SchemaProvider
    {
        return $this->schema;
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

    public function updateImage(Image $image): void
    {
        $q = $this->schema->createQuery();
        $q
            ->update(ImagesTable::NAME, [
                'copyright' => $image->copyright,
                'downloadable' => $image->downloadable,
            ])
            ->where($q->eq('name', $image->name))
            ->executeStatement();
    }

    public function getRecordsByImageId(string $id): array
    {
        $q = $this->schema->createQuery();
        $q->selectFrom(RecordsTable::NAME, 'title', 'market', 'date', 'keyword')
            ->where($q->eq('image_id', $id))
            ->orderBy('date');

        return $q->fetchAllAssociativeFlat();
    }

    public function createRecord(Record $record): void
    {
        $this->schema->createQuery()
            ->insert(RecordsTable::NAME, (array) $record)
            ->executeStatement();
    }

    public function createRecordTask(Ulid $id, Record $record): void
    {
        $this->schema->createQuery()->insert(RecordTasksTable::NAME, [
            'id' => $id,
            'date' => $record->date,
            'market' => $record->market,
        ])->executeStatement();
    }

    public function createImageTask(Ulid $id, Image $image): void
    {
        $this->schema->createQuery()->insert(ImageTasksTable::NAME, [
            'id' => $id,
            'image_id' => $image->id,
        ])->executeStatement();
    }

    public function getRecordTask(Ulid $id): ?RecordTask
    {
        $q = $this->schema->createQuery();
        $q->selectFrom(RecordTasksTable::NAME)
            ->where($q->eq('id', $id))
            ->joinOn(TasksTable::NAME, 'id', 'id', 'status')
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        $q = $this->schema->createQuery();

        $logs = $q->selectFrom(TaskLogsTable::NAME)
            ->where($q->eq('task_id', $id))
            ->setMaxResults(10)
            ->fetchAllAssociativeFlat();

        return new RecordTask(...$data, logs: $logs);
    }

    public function getImageTask(Ulid $id): ?ImageTask
    {
        $q = $this->schema->createQuery();
        $q->selectFrom(ImageTasksTable::NAME, 'id')
            ->where($q->eq('id', $id))
            ->joinOn(TasksTable::NAME, 'id', 'id', 'status')
            ->joinOn(ImagesTable::NAME, 'id', 'image_id', 'name', 'urlbase', 'video')
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        $q = $this->schema->createQuery();

        $logs = $q->selectFrom(TaskLogsTable::NAME)
            ->where($q->eq('task_id', $id))
            ->setMaxResults(10)
            ->fetchAllAssociativeFlat();

        return new ImageTask(...$data, logs: $logs);
    }

    public function getImagesByDate(DateTimeImmutable $date): array
    {
        $q = $this->schema->createQuery();

        $records = $q->selectFrom(RecordsTable::NAME, 'image_id', 'market')
            ->where($q->eq('date', $date))
            ->fetchColumnGrouped();

        if ($records === []) {
            return [];
        }

        $imageIds = array_keys($records);

        $q = $this->schema->createQuery();

        $images = $q->selectFrom(ImagesTable::NAME, 'id', 'name', 'urlbase')
            ->where($q->in('id', $imageIds))
            ->fetchAllAssociativeIndexed();

        foreach ($records as $imageId => $markets) {
            $images[$imageId]['markets'] = $markets;
        }

        return array_values($images);
    }

    public function listRecordOperations(): Generator
    {
        $q = $this->schema->createQuery();
        $q->selectFrom(RecordTasksTable::NAME)
            ->orderBy('id', 'DESC')
            ->joinOn(TasksTable::NAME, 'id', 'id', 'status')
            ->setMaxResults(100);

        while ($data = $q->fetchAssociativeFlat()) {
            yield new RecordTask(...$data);
        }
    }

    public function listImageOperations(): Generator
    {
        $q = $this->schema->createQuery();
        $q->selectFrom(ImageTasksTable::NAME, 'id')
            ->orderBy('id', 'DESC')
            ->joinOn(TasksTable::NAME, 'id', 'id', 'status')
            ->joinOn(ImagesTable::NAME, 'id', 'image_id', 'name', 'urlbase', 'video')
            ->setMaxResults(100);

        while ($data = $q->fetchAssociativeFlat()) {
            yield new ImageTask(...$data);
        }
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom(ImagesTable::NAME)
            ->where($q->eq('name', $name))
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

        $q->selectFrom(ImagesTable::NAME, 'name', 'urlbase')
            ->where($q->gt('debutOn', $cursor), $q->lte('debutOn', $prevCursor))
            ->addOrderBy('debutOn', 'DESC')
            ->addOrderBy('id', 'DESC');

        return $q->fetchAllAssociativeFlat();
    }

    public function getImageByOperationId(Ulid $id): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom(ImagesTable::NAME)
            ->joinOn(ImageTasksTable::NAME, 'image_id', 'id', null)
            ->where($q->eq('id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        return new Image(...$data);
    }

    public function getImageById(string $id): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom(ImagesTable::NAME)
            ->where($q->eq('id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        return new Image(...$data);
    }

    public function getRecord(string $market, DateTimeImmutable $date): ?Record
    {
        $q = $this->schema->createQuery();

        $q->selectFrom([RecordsTable::NAME, 'r'])
            ->where($q->eq('market', $market), $q->eq('date', $date))
            ->joinOn([ImagesTable::NAME, 'i'], 'id', 'image_id')
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
            ->selectFrom(ImagesTable::NAME)
            ->orderBy('id')
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportImagesWhere(): array
    {
        $q = $this->schema
            ->createQuery();

        return $q->selectFrom(ImagesTable::NAME)
            ->orderBy('id')
            ->where($q->eq('debut_on', new DateTimeImmutable('2022-11-20T00:00:00.000000Z')))
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportRecordsWhere(): array
    {
        $q = $this->schema
            ->createQuery();

        return $q->selectFrom(RecordsTable::NAME)
            ->orderBy('id')
            ->where($q->eq('date', new DateTimeImmutable('2022-11-19T00:00:00.000000Z')))
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportRecords(): array
    {
        return $this->schema
            ->createQuery()
            ->selectFrom(RecordsTable::NAME)
            ->orderBy('id')
            ->fetchAllAssociativeFlat();
    }

    public function getMarketsPendingOrExisting(DateTimeImmutable $date): array
    {
        $recordQuery = $q = $this->schema->createQuery();
        $q
            ->selectFrom(RecordsTable::NAME, 'market')
            ->where($q->eq('date', $date));

        $taskQuery = $q = $this->schema->createQuery();
        $q
            ->selectFrom(RecordTasksTable::NAME, 'market')
            ->where($q->eq('date', $date));

        return $this->schema
            ->executeMergedQuery($recordQuery, ' UNION ', $taskQuery)
            ->fetchFirstColumn();
    }
}
