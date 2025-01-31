<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\CollectRecords;
use DateTimeImmutable;
use InvalidArgumentException;
use Manyou\BingHomepage\Market;
use Psr\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

use function array_diff;
use function array_map;
use function implode;
use function sprintf;

#[AsCommand(name: 'app:collect-records')]
class CollectRecordsCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ClockInterface $clock,
        #[Autowire('%app.enabled_markets%')]
        private array $enabledMarkets,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('date', InputArgument::REQUIRED, 'Date of the records to collect');
        $this->addOption('market', 'k', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Market codes of the records to collect', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new DateTimeImmutable($input->getArgument('date'));

        $markets = $input->getOption('market');
        if ([] === $markets) {
            $markets = $this->enabledMarkets;
        }

        if ([] !== $disabledMarkets = array_diff($markets, $this->enabledMarkets)) {
            throw new InvalidArgumentException(sprintf('The following markets are not enabled: %s', implode(', ', $disabledMarkets)));
        }

        $this->messageBus->dispatch(new CollectRecords($date, array_map(static fn ($market) => new Market($market), $markets)));

        return 0;
    }
}
