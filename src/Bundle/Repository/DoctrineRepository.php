<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use App\Bundle\ApiResource\RecordOperation;
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
use Manyou\Mango\Operation\Doctrine\Type\OperationStatusType;
use Symfony\Component\Uid\Ulid;

use function array_map;

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

    public function getRecordOperation(Ulid $id): ?RecordOperation
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([RecordOperationsTable::NAME, 'r'])
            ->join('r', OperationsTable::NAME, 'o', 'r.id = o.id', 'status')
            ->where($q->eq('r.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        $record    = $data['r'];
        $operation = $data['o'];

        $q = $this->schema->createQuery();

        $data = $q->selectFrom([OperationLogsTable::NAME, 'l'])
            ->where($q->eq('l.operation_id', $id))
            ->setMaxResults(10)
            ->fetchAllAssociative();

        $logs = array_map(static fn ($row) => $row['l'], $data);

        return new RecordOperation(...$record, ...$operation, logs: $logs);
    }

    public function listRecordOperations(): Generator
    {
        $q = $this->schema->createQuery();
        $q->selectFrom([RecordOperationsTable::NAME, 'r'])
            ->join('r', OperationsTable::NAME, 'o', 'r.id = o.id', 'status')
            ->where($q->eq('o.status', OperationStatusType::FAILED))
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(100);

        while ($data = $q->fetchAssociative()) {
            yield new RecordOperation(...$data['r'], ...$data['o']);
        }
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schema->createQuery();

        $q->selectFrom([ImagesTable::NAME, 'i'])
            ->where($q->eq('i.name', $name))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        return new Image(...$data['i']);
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

    /** @return Generator|array[] */
    public function exportImages(): Generator
    {
        $q = $this->schema
            ->createQuery()
            ->selectFrom([ImagesTable::NAME, 'i'])
            ->orderBy('id');

        while ($data = $q->fetchAssociative()) {
            yield $data['i'];
        }
    }

    /** @return Generator|array[] */
    public function exportRecords(): Generator
    {
        $q = $this->schema
            ->createQuery()
            ->selectFrom([RecordsTable::NAME, 'r'])
            ->orderBy('id');

        while ($data = $q->fetchAssociative()) {
            yield $data['r'];
        }
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
