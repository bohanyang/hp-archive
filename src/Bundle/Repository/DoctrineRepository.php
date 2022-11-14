<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use App\Bundle\ApiResource\ImageOperation;
use App\Bundle\ApiResource\RecordOperation;
use App\Bundle\Doctrine\TableProvider\ImageOperationsTable;
use App\Bundle\Doctrine\TableProvider\ImagesTable;
use App\Bundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\Doctrine\TableProvider\RecordsTable;
use DateTimeImmutable;
use Generator;
use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Record;
use Manyou\Mango\Doctrine\SchemaProvider;
use Manyou\Mango\Operation\Doctrine\TableProvider\OperationLogsTable;
use Manyou\Mango\Operation\Doctrine\TableProvider\OperationsTable;
use Manyou\Mango\Operation\Enum\OperationStatus;
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
            ->update([ImagesTable::NAME, 'i'], [
                'copyright' => $image->copyright,
                'downloadable' => $image->downloadable,
            ])
            ->where($q->eq('i.name', $image->name))
            ->executeStatement();
    }

    public function getRecordsByImageId(string $id): array
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([RecordsTable::NAME, 't'], 'title', 'market', 'date', 'keyword')
            ->where($q->eq('t.image_id', $id))
            ->orderBy('t.date');

        return $q->fetchAllAssociativeFlat();
    }

    public function createRecord(Record $record): void
    {
        $this->schema->createQuery()
            ->insert(RecordsTable::NAME, (array) $record)
            ->executeStatement();
    }

    public function createRecordOperation(Ulid $id, Record $record): void
    {
        $this->schema->createQuery()->insert(RecordOperationsTable::NAME, [
            'id' => $id,
            'date' => $record->date,
            'market' => $record->market,
        ])->executeStatement();
    }

    public function createImageOperation(Ulid $id, Image $image): void
    {
        $this->schema->createQuery()->insert(ImageOperationsTable::NAME, [
            'id' => $id,
            'image_id' => $image->id,
        ])->executeStatement();
    }

    public function getRecordOperation(Ulid $id): ?RecordOperation
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([RecordOperationsTable::NAME, 'r'])
            ->join('r', OperationsTable::NAME, 'o', 'r.id = o.id', 'status')
            ->where($q->eq('r.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        $q = $this->schema->createQuery();

        $logs = $q->selectFrom([OperationLogsTable::NAME, 'l'])
            ->where($q->eq('l.operation_id', $id))
            ->setMaxResults(10)
            ->fetchAllAssociativeFlat();

        return new RecordOperation(...$data, logs: $logs);
    }

    public function getImageOperation(Ulid $id): ?ImageOperation
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([ImageOperationsTable::NAME, 't'], 'id')
            ->join('t', OperationsTable::NAME, 'o', 't.id = o.id', 'status')
            ->join('t', ImagesTable::NAME, 'i', 't.image_id = i.id', 'name', 'urlbase', 'video')
            ->where($q->eq('t.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        $q = $this->schema->createQuery();

        $logs = $q->selectFrom([OperationLogsTable::NAME, 'l'])
            ->where($q->eq('l.operation_id', $id))
            ->setMaxResults(10)
            ->fetchAllAssociativeFlat();

        return new ImageOperation(...$data, logs: $logs);
    }

    public function getImagesByDate(DateTimeImmutable $date): array
    {
        $q = $this->schema->createQuery();

        $records = $q->selectFrom([RecordsTable::NAME, 'r'], 'image_id', 'market')
            ->where($q->eq('r.date', $date))
            ->fetchColumnGrouped();

        if ($records === []) {
            return [];
        }

        $imageIds = array_keys($records);

        $q = $this->schema->createQuery();

        $images = $q->selectFrom([ImagesTable::NAME, 'i'], 'id', 'name', 'urlbase')
            ->where($q->in('i.id', $imageIds))
            ->fetchAllAssociativeIndexed();

        foreach ($records as $imageId => $markets) {
            $images[$imageId]['markets'] = $markets;
        }

        return array_values($images);
    }

    public function listRecordOperations(): Generator
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([RecordOperationsTable::NAME, 'r'])
            ->join('r', OperationsTable::NAME, 'o', 'r.id = o.id', 'status')
            ->where($q->eq('o.status', OperationStatus::FAILED))
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(100);

        while ($data = $q->fetchAssociativeFlat()) {
            yield new RecordOperation(...$data);
        }
    }

    public function listImageOperations(): Generator
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([ImageOperationsTable::NAME, 't'], 'id')
            ->join('t', OperationsTable::NAME, 'o', 't.id = o.id', 'status')
            ->join('t', ImagesTable::NAME, 'i', 't.image_id = i.id', 'name', 'urlbase', 'video')
            ->where($q->eq('o.status', OperationStatus::FAILED))
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(100);

        while ($data = $q->fetchAssociativeFlat()) {
            yield new ImageOperation(...$data);
        }
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom([ImagesTable::NAME, 'i'])
            ->where($q->eq('i.name', $name))
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

        $q->selectFrom([ImagesTable::NAME, 't'], 'name', 'urlbase')
            ->where($q->gt('t.debutOn', $cursor), $q->lte('t.debutOn', $prevCursor))
            ->addOrderBy('t.debutOn', 'DESC')
            ->addOrderBy('t.id', 'DESC');

        return $q->fetchAllAssociativeFlat();
    }

    public function getImageByOperationId(Ulid $id): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom([ImagesTable::NAME, 'i'])
            ->join('i', ImageOperationsTable::NAME, 'o', 'i.id = o.image_id', null)
            ->where($q->eq('o.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociativeFlat()) {
            return null;
        }

        return new Image(...$data);
    }

    public function getImageById(string $id): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom([ImagesTable::NAME, 'i'])
            ->where($q->eq('i.id', $id))
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
            ->join('r', ImagesTable::NAME, 'i', 'r.image_id = i.id')
            ->where($q->eq('r.market', $market), $q->eq('r.date', $date))
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
            ->selectFrom([ImagesTable::NAME, 'i'])
            ->orderBy('i.id')
            ->fetchAllAssociativeFlat();
    }

    /** @return array[] */
    public function exportRecords(): array
    {
        return $this->schema
            ->createQuery()
            ->selectFrom([RecordsTable::NAME, 'r'])
            ->orderBy('r.id')
            ->fetchAllAssociativeFlat();
    }

    public function getMarketsPendingOrExisting(DateTimeImmutable $date): array
    {
        $recordQuery = $q = $this->schema->createQuery();
        $q
            ->selectFrom([RecordsTable::NAME, 'r'], 'market')
            ->where($q->eq('r.date', $date));

        $operationQuery = $q = $this->schema->createQuery();
        $q
            ->selectFrom([RecordOperationsTable::NAME, 'o'], 'market')
            ->where($q->eq('o.date', $date));

        return $this->schema
            ->executeMergedQuery($recordQuery, ' UNION ', $operationQuery)
            ->fetchFirstColumn();
    }
}
