<?php

declare(strict_types=1);

namespace App\Repository;

use App\ApiResource\Image;
use App\ApiResource\Record;
use App\Doctrine\SchemaProvider;
use DateTimeImmutable;

class DoctrineRepository
{
    public function __construct(
        private SchemaProvider $schemaProvider,
    ) {
    }

    public function createImage(Image $image): void
    {
        $this->schemaProvider->createQuery()
            ->insert('images', (array) $image)
            ->executeStatement();
    }

    public function createRecord(Record $record): void
    {
        $this->schemaProvider->createQuery()
            ->insert('records', (array) $record)
            ->executeStatement();
    }

    public function getImage(string $name): ?Image
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom('images', 'id', 'name', 'urlbase', 'copyright', 'downloadable', 'video', 'debutOn')
            ->where($q->eq('images.name', $name))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        return new Image(...$data['images']);
    }

    public function getImageById(string $id): ?Image
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom('images', 'id', 'name', 'urlbase', 'copyright', 'downloadable', 'video', 'debutOn')
            ->where($q->eq('images.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        return new Image(...$data['images']);
    }

    public function getRecord(string $market, DateTimeImmutable $date): ?Record
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom(
            'records',
            'id',
            'date',
            'market',
            'title',
            'keyword',
            'headline',
            'description',
            'quickfact',
            'hotspots',
            'messages',
            'coverstory',
        )
        ->join(
            'records',
            'images',
            'images',
            'records.image_id = images.id',
            'id',
            'name',
            'urlbase',
            'copyright',
            'downloadable',
            'video',
            'debutOn',
        )
        ->where(
            $q->eq('records.market', $market),
            $q->eq('records.date', $date),
        )
        ->setMaxResults(1);

        if (false === $data = $q->fetchAssociative()) {
            return null;
        }

        $record          = $data['records'];
        $record['image'] = new Image(...$data['images']);
        $record          = new Record(...$record);

        return $record;
    }
}
