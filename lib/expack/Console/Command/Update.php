<?php

namespace expack\Console\Command;

use Symfony\Component\Console\Command\Command as sfConsoleCommand;
use Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class Update extends sfConsoleCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('update')
        ->setDescription('Updates rep2 expack')
        ->setDefinition(array(
            new InputOption('no-rep2',  null, null, 'Don\'t update rep2'),
            new InputOption('alldeps',  null, null, 'Update all depenencies'),
            new InputOption('composer', null, null, 'Update composer.phar'),
            new InputOption('pear',     null, null, 'Update PEAR libraries'),
            new InputOption('vendor',   null, null, 'Update vendor libraries'),
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('no-rep2')) {
            if (!$this->updateSelf($output)) {
                return 1;
            }
        }

        $updateAllDeps = (bool)$input->getOption('alldeps');
        foreach (array('pear', 'composer', 'vendor') as $dep) {
            if ($updateAllDeps || $input->getOption($dep)) {
                $method = 'update' . ucfirst($dep);
                if (!$this->$method($output)) {
                    return 1;
                }
            }
        }

        return 0;
    }

    /**
     * Update rep2
     *
     * @var OutputInterface $output
     *
     * @return bool
     */
    private function updateSelf(OutputInterface $output)
    {
        $command = 'git pull --quiet';
        return $this->runShellCommand($command, $output) === 0;
    }

    /**
     * Update PEAR libraries
     *
     * @var OutputInterface $output
     *
     * @return bool
     */
    private function updatePear(OutputInterface $output)
    {
        $command = 'git submodule update --quiet';
        return $this->runShellCommand($command, $output) === 0;
    }

    /**
     * Update composer.phar
     *
     * @var OutputInterface $output
     *
     * @return bool
     */
    private function updateComposer(OutputInterface $output)
    {
        $command = escapeshellarg(PHP_BINARY) . ' composer.phar selfupdate';
        return $this->runShellCommand($command, $output) === 0;
    }

    /**
     * Update vendor libraries
     *
     * @var OutputInterface $output
     *
     * @return bool
     */
    private function updateVendor(OutputInterface $output)
    {
        $command = escapeshellarg(PHP_BINARY) . ' composer.phar update';
        return $this->runShellCommand($command, $output) === 0;
    }

    /**
     * Execute command
     *
     * @param string $command
     * @param OutputInterface $output
     *
     * @return int
     */
    private function runShellCommand($command, $output)
    {
        $output->writeln("<comment>{$command}</comment>");
        exec($command, $lines, $code);
        if ($lines) {
            array_map(array($output, 'writeln'), $lines);
        }
        return $code;
    }
}
