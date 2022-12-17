<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use GuzzleHttp\Promise\Utils;
use Manyou\BingHomepage\Image;
use Manyou\LeanStorage\Request\Batchable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_map;
use function dump;

#[AsCommand(name: 'export:to-leancloud')]
class ExportToLeanCloudCommand extends Command
{
    public function __construct(
        private DoctrineRepository $repository,
        private LeanCloudRepository $leancloud,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Batchable[] */
        $requests = array_map(
            fn ($data) => $this->leancloud->createImageRequest(new Image(...$data)),
            $this->repository->exportImagesWhere(),
        );

        $responses = Utils::unwrap($this->leancloud->getClient()->batch(...$requests));

        dump($responses);

        return 0;
    }
}
