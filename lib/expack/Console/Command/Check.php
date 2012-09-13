<?php

namespace expack\Console\Command;

use Symfony\Component\Console\Command\Command as sfConsoleCommand;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class Check extends sfConsoleCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('check')
        ->setDescription('Checks the environment');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        include \P2_CONFIG_DIR . '/setup_info.php';

        $status = 0;

        $php_version = phpversion();

        if (version_compare($php_version, '5.4.0-dev', '>=')) {
            $required_version = $p2_required_version_5_4;
        } else {
            $required_version = $p2_required_version_5_3;
        }

        // PHPのバージョン
        $output->writeln('PHP Version:');
        $message = "  <info>{$php_version}</info>: ";
        if (version_compare($php_version, $required_version, '>=')) {
            $message .= '<comment>OK</comment>';
        } else {
            $message .= "<error>Requires PHP {$required_version} or later</error>";
            $status = 1;
        }
        $output->writeln($message);

        // 必須拡張モジュール
        $output->writeln('PHP Extensions:');
        foreach ($p2_required_extensions as $ext) {
            $message = "  <info>{$ext}</info>: ";
            if (extension_loaded($ext)) {
                $message .= '<comment>OK</comment>';
            } else {
                $message .= '<error>Not loaded</error>';
                $status = 1;
            }
            $output->writeln($message);
        }

        // 有効だと動作しないphp.iniディレクティブ
        $output->writeln('php.ini directives:');
        foreach ($p2_incompatible_ini_directives as $directive) {
            $value = ini_get($directive);
            $message = "  <info>{$directive} = {$value}</info>: ";
            if ($value) {
                $message .= '<error>Please turn off</error>';
                $status = 1;
            } else {
                $message .= '<comment>OK</comment>';
            }
            $output->writeln($message);
        }

        return $status;
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
