<?php

namespace expack\Console\Command;

use Symfony\Component\Console\Command\Command as sfConsoleCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends sfConsoleCommand
{
    /**
     * Executes the shell command
     *
     * @param string $command
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execCommand($command, OutputInterface $output)
    {
        $output->writeln("<comment>{$command}</comment>");
        passthru($command, $code);

        return $code;
    }
}
