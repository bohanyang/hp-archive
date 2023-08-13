<?php

declare(strict_types=1);

namespace App\Controller;

use App\Bundle\Repository\DoctrineRepository;
use DateTimeImmutable;
use League\Uri\UriString;
use Manyou\BingHomepage\CurrentTime;
use Manyou\BingHomepage\Market;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function array_map;
use function urlencode;

class MainController extends AbstractController
{
    public function __construct(
        private InertiaInterface $inertia,
        private DoctrineRepository $repository,
        private string $origin,
    ) {
    }

    public function record(string $market, ?string $date)
    {
        $date = $date !== null ? DateTimeImmutable::createFromFormat('!Ymd', $date) : null;

        if (
            (false === $date)
            || (null === $record = $this->repository->getRecord($market, $date))
        ) {
            throw $this->createNotFoundException();
        }

        return $this->inertia->render('Record', [
            'image_origin' => $this->origin,
            'image' => [
                'name' => $record->image->name,
                'urlbase' => $record->image->urlbase,
                'copyright' => $record->image->copyright,
                'wp' => $record->image->downloadable,
                'uhd' => true,
            ],
            'record' => [
                'description' => $record->title,
                'market' => $record->market,
                'link' => $this->getSearchLink($record->keyword),
                'coverstory' => $record->coverstory,
                'hotspots' => $record->hotspots,
                'messages' => $record->messages,
            ],
            'video' => $this->getVideoUrl($record->image->video),
            'formattedDate' => $record->date->format('Y/n/j'),
            'date' => $this->getDateInfo($record->date, new Market($record->market)),
        ]);
    }

    private function getSearchLink(?string $keyword)
    {
        return $keyword === null ? null : 'https://www.bing.com/search?q=' . urlencode($keyword);
    }

    private function getVideoUrl(?array $video): ?string
    {
        if (empty($video['sources'][1][2])) {
            return null;
        }

        $url          = UriString::parse($this->origin);
        $url['path'] .= UriString::parse($video['sources'][1][2])['path'];

        return UriString::build($url);
    }

    private function getDateInfo(DateTimeImmutable $date, Market $market)
    {
        return [
            'previous' => $date->modify('yesterday')->format('Ymd'),
            'current' => $date->format('Ymd'),
            'next' => $market->getToday() > $market->getDate($date) ? $date->modify('tomorrow')->format('Ymd') : null,
        ];
    }

    public function image(string $name)
    {
        if (null === $image = $this->repository->getImage($name)) {
            throw $this->createNotFoundException();
        }

        $records = $this->repository->getRecordsByImageId($image->id);

        return $this->inertia->render('Image', [
            'image_origin' => $this->origin,
            'image' => [
                'name' => $image->name,
                'urlbase' => $image->urlbase,
                'copyright' => $image->copyright,
                'wp' => $image->downloadable,
                'uhd' => true,
            ],
            'video' => $this->getVideoUrl($image->video),
            'records' => array_map(fn ($record) => [
                'description' => $record['title'],
                'market' => $record['market'],
                'link' => $this->getSearchLink($record['keyword']),
                'date' => $record['date']->format('Ymd'),
                'formattedDate' => $record['date']->format('Y/n/j'),
            ], $records),
        ]);
    }

    public function date(string $date)
    {
        $date = DateTimeImmutable::createFromFormat('!Ymd', $date);

        if (
            (false === $date)
            || ([] === $images = $this->repository->getImagesByDate($date))
        ) {
            throw $this->createNotFoundException();
        }

        return $this->inertia->render('DatePage', [
            'image_origin' => $this->origin,
            'formattedDate' => $date->format('Y/n/j'),
            'date' => $this->getDateInfo($date, new Market('ja-JP')),
            'images' => $images,
        ]);
    }

    private function getFirstCursor(): DateTimeImmutable
    {
        return (new CurrentTime())->getTheLaterDate()->modify('-7 days');
    }

    public function browse(?string $cursor)
    {
        $firstCursor = $this->getFirstCursor();
        $cursor      = $cursor === null ? $firstCursor : DateTimeImmutable::createFromFormat('!Ymde', $cursor . 'UTC');
        $prevCursor  = $cursor->modify('+7 days');
        $nextCursor  = $cursor->modify('-7 days');

        if (
            (false === $cursor)
            || ([] === $images = $this->repository->browse($cursor, $prevCursor))
        ) {
            throw $this->createNotFoundException();
        }

        return $this->inertia->render('Browse', [
            'image_origin' => $this->origin,
            'images' => $images,
            'prevCursor' => $cursor < $firstCursor ? $prevCursor->format('Ymd') : null,
            'nextCursor' => $nextCursor->format('Ymd'),
        ]);
    }
}
