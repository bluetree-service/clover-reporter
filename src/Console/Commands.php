<?php

declare(strict_types=1);

namespace CloverReporter\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use CloverReporter\Parser;
use CloverReporter\Render;

class Commands extends Command
{
    protected function configure(): void
    {
        $this->setName('reporter')
            ->setDescription('Generate coverage report based on clover report file.')
            ->setHelp('');

        $this->addArgument(
            'report_file',
            InputArgument::OPTIONAL,
            'clover.xml report file',
            'build/logs/clover.xml'
        );

        $this->addOption('open-browser', 'b', null, 'automatically open default browser with html report');
        $this->addOption('html', 'H', null, 'generate html report version');
        $this->addOption('show-coverage', 'c', null, 'show only classes with coverage in percent');
        $this->addOption('short-report', 's', null, 'show coverage in percent per line with uncovered lines only');
        $this->addOption('full-report', 'f', null, 'show coverage in percent per line with complete script');
        $this->addOption(
            'skip-dir',
            'd',
            InputArgument::OPTIONAL,
            'allow to skip specified dirs in root path. Dir delimiter: ";"',
            'vendor;test;tests'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = \microtime(true);
        $style = new Style($input, $output, $this);

        $style->title('Clover report generator.');

        $style->formatSection('Coverage report file', $input->getArgument('report_file'));
        $output->writeln('');

        $parser = new Parser(
            $input->getArgument('report_file'),
            $input->getOptions()
        );

        $infoList = $parser->getInfoList();

        if ($input->getOption('html')) {
            $render = new Render\RenderHtml($input->getOptions(), $infoList, $style);
        } else {
            $render = new Render\RenderCli($input->getOptions(), $infoList, $style);
        }

        if ($input->getOption('short-report')) {
            $render->shortReport();
        }

        if ($input->getOption('full-report')) {
            $render->fullReport();
        }

        if ($input->getOption('show-coverage')) {
            $render->displayCoverage();
        }

        $render->summary($startTime);

        return Command::SUCCESS;
    }
}
