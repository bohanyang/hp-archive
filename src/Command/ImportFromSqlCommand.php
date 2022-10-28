<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\DoctrineRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import:sql')]
class ImportFromSqlCommand extends Command
{
    public function __construct(private DoctrineRepository $source, private DoctrineRepository $destination)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $icounter = 0;
        foreach ($this->source->exportImages() as $data) {
            $output->writeln('Image No.' . ++$icounter);
            $this->destination->createImage($data);
        }

        $rcounter = 0;
        foreach ($this->source->exportRecords() as $data) {
            $output->writeln('Record No.' . ++$rcounter);
            $this->destination->createRecord($data);
        }

        $output->writeln('Image Tl.' . $icounter);
        $output->writeln('Record Tl.' . $rcounter);

        return 0;
    }
}
