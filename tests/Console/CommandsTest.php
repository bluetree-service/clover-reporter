<?php

namespace CloverReporterTest\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\TestCase;
use CloverReporter\Console\Commands;

class CommandsTest extends TestCase
{
    use \CloverReporterTest\Helper;

    public function setUp()
    {
        $this->copyFixedReports('clover_log.xml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIncorrectReportFile()
    {
        $this->prepareCommand();
    }

    public function testBasicExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Total coverage: 61.404%
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    public function testShowCoverageExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--show-coverage' => true,
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\Log
  - 100%        SimpleLog\LogStatic
  - 0%          SimpleLog\Message\DefaultJsonMessage

Total coverage: 61.404%
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    public function testShowShortReportExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\Log
93:         {
94:             if (\$this->defaultParams['storage'] instanceof StorageInterface) {
107:        {
108:            if (\$this->defaultParams['message'] instanceof MessageInterface) {

  - 100%        SimpleLog\LogStatic
  - 0%          SimpleLog\Message\DefaultJsonMessage
18:          * @param string|array|object \$message
21:          */
26:             list(\$date, \$time) = explode(';', strftime(self::DATE_FORMAT . ';' . self::TIME_FORMAT, time()));
28:             \$this->messageScheme['date'] = \$date;


Total coverage: 61.404%
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTime(
                    $commandTester->getDisplay()
                )
            )
        );
    }

    public function testShowShortReportExecuteWithErrors()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['base'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\\Log
[ERROR]              File don't exists: {\$path}log/src/Log.php
  - 100%        SimpleLog\\LogStatic
[ERROR]              File don't exists: {\$path}log/src/LogStatic.php
  - 0%          SimpleLog\\Message\\DefaultJsonMessage
[ERROR]              File don't exists: {\$path}log/src/DefaultJsonMessage.php

Total coverage: 61.404%
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTime(
                    $commandTester->getDisplay()
                )
            )
        );
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearExecutionTime($report)
    {
        return substr($report, 0, strrpos($report, " \n[Execution"));
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearSpaces($report)
    {
        return preg_replace('#[ ]+\n#', "\n", $report);
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
