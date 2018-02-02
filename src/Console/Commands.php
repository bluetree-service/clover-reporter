<?php

namespace CloverReporter\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use CloverReporter\Parser;
use CloverReporter\Render;

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
        $this->addOption('show-coverage', 'c');     //show only coverage in percent
        $this->addOption('short-report', 'r');      //display only uncovered lines
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

        $parser = new Parser(
            $input->getArgument('report_file'),
            $input->getOptions()
        );

        $infoList = $parser->getInfoList();

        new Render($input->getOptions(), $infoList);

        if ($input->getOption('open-browser')) {
            $url = $input->getArgument('output') . '/index.html';
            `x-www-browser $url`;
        }

        if ($input->getOption('short-report')) {
            //display basic coverage info
        } else {
            
        }
    }
}
