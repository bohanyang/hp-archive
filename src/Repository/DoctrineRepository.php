<?php

declare(strict_types=1);

namespace App\Repository;

use App\ApiResource\Image;
use App\ApiResource\Record;
use App\Doctrine\SchemaProvider;
use DateTimeImmutable;
use Generator;

class DoctrineRepository
{
    public function __construct(
        private SchemaProvider $schemaProvider,
    ) {
    }

    public function getSchemaProvider(): SchemaProvider
    {
        return $this->schemaProvider;
    }

    public function createImage(Image|array $image): void
    {
        $this->schemaProvider->createQuery()
            ->insert('images', (array) $image)
            ->executeStatement();
    }

    public function updateImage(Image $image): void
    {
        $q = $this->schemaProvider->createQuery();
        $q->update('images', $image->getDataForUpdate())
            ->where($q->eq('images.name', $image->name))
            ->executeStatement();
    }

    public function createRecord(Record|array $record): void
    {
        $this->schemaProvider->createQuery()
            ->insert('records', (array) $record)
            ->executeStatement();
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom('images')
            ->where($q->eq('images.name', $name))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        return new Image(...$data['images']);
    }

    public function getRecord(string $market, DateTimeImmutable $date): ?Record
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom('records')
            ->join('records', 'images', 'images', 'records.image_id = images.id')
            ->where($q->eq('records.market', $market), $q->eq('records.date', $date))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        $record          = $data['records'];
        $record['image'] = new Image(...$data['images']);
        $record          = new Record(...$record);

        return $record;
    }

    /** @return array */
    public function exportImages(): Generator
    {
        $q = $this->schemaProvider
            ->createQuery()
            ->selectFrom('images')
            ->orderBy('id');

        while ($data = $q->fetchAssociative()) {
            yield $data['images'];
        }
    }

    /** @return array */
    public function exportRecords(): Generator
    {
        $q = $this->schemaProvider
            ->createQuery()
            ->selectFrom('records')
            ->orderBy('id');

        while ($data = $q->fetchAssociative()) {
            yield $data['records'];
        }
    }
}
