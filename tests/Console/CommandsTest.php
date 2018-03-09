<?php

namespace SimpleLog\Test\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\TestCase;
use CloverReporter\Console\Commands;

class CreateUserCommandTest extends TestCase
{
    public function testBasicExecute()
    {
        $commandTester = $this->prepareCommand();

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] build/logs/clover.xml

Total coverage: 19.333%
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    public function testShowCoverageExecute()
    {
        $commandTester = $this->prepareCommand(['--show-coverage' => true]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] build/logs/clover.xml

Found 5 source files:
  - 0%          CloverReporter\Console\Commands
  - 0%          CloverReporter\Console\Style
  - 0%          CloverReporter\Directory
  - 96.667%     CloverReporter\Parser
  - 0%          CloverReporter\Render

Total coverage: 19.333%
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

//    public function testShowShortReportExecute()
//    {
//        $commandTester = $this->prepareCommand(['--short-report' => true]);
//
////        var_dump($commandTester->getDisplay());
////        var_dump($this->clearExecutionTime($commandTester->getDisplay()));
////        exit;
//
//        $output = <<<EOT
//
//Clover report generator.
//========================
//
//[Coverage report file] build/logs/clover.xml
//
//Found 5 source files:
//  - 0%          CloverReporter\Console\Commands
//[ERROR]              File don't exists: /home/chajr/Dropbox/C/clover-reporter/src/Console/Commands.php
//  - 0%          CloverReporter\Console\Style
//[ERROR]              File don't exists: /home/chajr/Dropbox/C/clover-reporter/src/Console/Style.php
//  - 0%          CloverReporter\Directory
//[ERROR]              File don't exists: /home/chajr/Dro
//EOT;
//
//        $this->assertEquals(
//            $output,
//            $this->clearExecutionTime($commandTester->getDisplay())
//        );
//    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearExecutionTime($report)
    {
        return substr($report, 0, strrpos($report, " \n[Execution"));
    }

    /**
     * @param array $parameters
     * @return CommandTester
     */
    protected function prepareCommand(array $parameters = [])
    {
        $application = new Application;

        $application->add(new Commands);

        $command = $application->find('reporter');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array_merge(['command' => $command->getName()], $parameters),
            ['decorated' => false]
        );

        return $commandTester;
    }
}
