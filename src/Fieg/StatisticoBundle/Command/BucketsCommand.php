<?php

namespace Fieg\StatisticoBundle\Command;

use Fieg\Statistico\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BucketsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('statistico:buckets')
       ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Reader $reader */
        $reader = $this->getContainer()->get('statistico.reader');

        $buckets = $reader->getBuckets();

        foreach ($buckets as $bucket) {
            $output->writeln(sprintf('<info>%s</info>', $bucket));
        }
    }
}
