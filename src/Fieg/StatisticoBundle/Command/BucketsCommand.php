<?php

namespace Fieg\StatisticoBundle\Command;

use Fieg\Statistico\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BucketsCommand extends Command
{
    /**
     * @var Reader
     */
    private $reader;

    protected function configure()
    {
        $this
            ->setName('statistico:buckets')
       ;
    }

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $buckets = $this->reader->getBuckets();

        foreach ($buckets as $bucket) {
            $output->writeln(sprintf('<info>%s</info>', $bucket));
        }
    }
}
