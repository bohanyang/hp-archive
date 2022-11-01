<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Repository\DoctrineRepository;
use App\LeanCloud;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Record;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function substr;

#[AsCommand(name: 'import:leancloud')]
class ImportFromLeanCloudCommand extends Command
{
    public function __construct(private LeanCloud $leancloud, private DoctrineRepository $repository)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('later-than', InputArgument::REQUIRED, 'Later than: 1970-01-01T00:00:00.000+00:00');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start images...');
        $this->readData('Image', function ($data) {
            $this->repository->createImage(Image::createFromLeanCloud($data));
        }, $input->getArgument('later-than'));

        $output->writeln('Start records...');
        $this->readData('Archive', function ($data) {
            try {
                $this->repository->createRecord(Record::createFromLeanCloud($data));
            } catch (UniqueConstraintViolationException $e) {
                if (substr($e->getMessage(), -8, 7) !== 'PRIMARY') {
                    throw $e;
                }
            }
        }, $input->getArgument('later-than'));

        return 0;
    }

    private function readData(string $className, callable $callback, string $laterThan)
    {
        $laterThan = new DateTimeImmutable($laterThan);

        $limit = 1000;
        $skip  = 0;
        do {
            $count = 0;

            $results = $this->leancloud->query(
                $className,
                query: ['where' => ['createdAt' => ['$gt' => ['__type' => 'Date', 'iso' => $laterThan->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.vp')]]]],
                body: ['order' => 'createdAt', 'limit' => $limit, 'skip' => $skip],
            );

            foreach ($results['results'] as $result) {
                $callback($result);
                $count++;
            }

            $skip += $count;
        } while ($count === $limit);
    }
}
