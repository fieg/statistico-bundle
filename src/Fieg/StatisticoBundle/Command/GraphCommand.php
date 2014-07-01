<?php

namespace Fieg\StatisticoBundle\Command;

use Fieg\Statistico\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GraphCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('statistico:graph')
            ->addArgument('bucket')
       ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            /** @var Reader $reader */
            $reader = $this->getContainer()->get('stats.reader');

            $from = new \DateTime('-1 minute');
            $to = new \DateTime();

            $buckets = $reader->getBuckets();
            $bucketArgument = $input->getArgument('bucket');

            $buckets = array_filter(
                $buckets,
                function ($bucket) use ($bucketArgument) {
                    return preg_match('/'.preg_quote($bucketArgument, '/') . '/', $bucket);
                }
            );

            $maxLen = 0;
            foreach ($buckets as $bucket) {
                if (strlen($bucket) > $maxLen) {
                    $maxLen = strlen($bucket);
                }
            }

            $out = [];

            foreach ($buckets as $bucket) {
                $counts = $reader->queryCounts($bucket, $from, $to);
                $rpm = $reader->queryRPM($bucket, $to);

                $minX = $from->getTimestamp();
                $maxX = $to->getTimestamp();

                $data = [];

                for ($i = $minX; $i <= $maxX; $i++) {
                    $data[$i] = (isset($counts[$i]) ? $counts[$i] : 0);
                }

                $out[] = sprintf("<info>%-".$maxLen."s</info> %s %3d rpm", $bucket, $this->drawGraph($data), $rpm);
            }

            $this->overwrite($output, implode($out, "\n"));

            usleep(1000);
        }
    }

    protected function drawGraph($data)
    {
        $chars = 60;

        if (count($data) > $chars) {
            $scaleX = floor(count($data) / $chars);

            $scaledData = [];

            for ($i = 0; $i < $chars; $i++) {
                $segment = array_slice($data, 1 + ($scaleX * $i) * -1, $scaleX);

                $total = 0;
                foreach ($segment as $value) {
                    $total += $value;
                }

                $value = $total;

                $scaledData[] = round($value);
            }

            $data = array_reverse($scaledData);
        }

        $minY = min(array_values($data));
        $maxY = max(array_values($data));

        $graph = '';

        foreach ($data as $time => $value) {
            if ($maxY > 0) {
                $percent = round($value * 100 / $maxY);
            } else {
                $percent = 0;
            }

            $char = '▁';

            if ($percent > 75 && $percent <= 100) {
                $char = '▇';
            } else if ($percent > 50 && $percent <= 75) {
                $char = '▅';
            } else if ($percent > 25 && $percent <= 50) {
                $char = '▃';
            } else if ($percent > 0 && $percent <= 25) {
                $char = '▂';
            }

            $graph .= $char;
        }

        return $graph;
    }

    protected $lastMessageLength;
    protected $lastMessage;

    /**
     * Overwrites a previous message to the output.
     *
     * @param OutputInterface $output   An Output instance
     * @param string          $message  The message
     */
    protected function overwrite(OutputInterface $output, $message)
    {
        $length = $this->strlen($message);

        // append whitespace to match the last line's length
        if (null !== $this->lastMessageLength && $this->lastMessageLength > $length) {
            $message = str_pad($message, $this->lastMessageLength, "\x20", STR_PAD_RIGHT);
        }

        // clear previous lines
        if (null !== $this->lastMessage) {
            $count = mb_substr_count($this->lastMessage, "\n");
            if ($count >= 1) {
                $output->write(chr(27) . '['.$count.'A');
            }
        }

        // carriage return
        $output->write("\x0D");
        $output->write($message);

        $this->lastMessageLength = $this->strlen($message);
        $this->lastMessage = $message;
    }

    /**
     * Returns the length of a string, using mb_strlen if it is available.
     *
     * @param string $string The string to check its length
     *
     * @return integer The length of the string
     */
    protected function strlen($string)
    {
        if (!function_exists('mb_strlen')) {
            return strlen($string);
        }

        if (false === $encoding = mb_detect_encoding($string)) {
            return strlen($string);
        }

        return mb_strlen($string, $encoding);
    }
}
