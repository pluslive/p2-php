<?php

namespace expack\Console\Command;

use Symfony\Component\Console\Command\Command as sfConsoleCommand;
use Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class Archive extends sfConsoleCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('archive')
        ->setDescription('Makes an archive')
        ->setDefinition(array(
            new InputOption('revision', null, null, 'Revision'),
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }
}
