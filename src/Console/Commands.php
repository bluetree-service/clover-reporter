<?php

namespace CloverReporter\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use CloverReporter\Parser;

class Commands extends Command
{
    protected function configure()
    {
        $this->setName('reporter')
            ->setDescription('Generate coverage report.')
            ->setHelp('');

        $this->addArgument('report_file', InputArgument::REQUIRED);
        $this->addArgument('output', InputArgument::OPTIONAL, '', __DIR__ . '/output');

        $this->addOption('open-browser', 'b');
        $this->addOption('short-report', 'r');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'generator',
            '============',
            '',
        ]);

        $output->writeln('file: ' . $input->getArgument('report_file'));
        $output->writeln('output: ' . $input->getArgument('output'));

        new Parser($input->getArgument('report_file'));

        $url = $input->getArgument('output') . '/index.html';

        if ($input->getOption('open-browser')) {
            `x-www-browser $url`;
        }

        if ($input->getOption('short-report')) {
            //display basic coverage info
        }
    }
}
