<?php

namespace CloverReporterTest\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\TestCase;
use CloverReporter\Console\Commands;

class HtmlTest extends TestCase
{
    use \CloverReporterTest\Helper;

    public function setUp(): void
    {
        $this->copyFixedReports('clover_log.xml');
        $this->copyFixedReports('clover_100_percent.xml');
    }
    
    public function tearDown(): void
    {
        @unlink(__DIR__ . '/../../build/coverage_report.html');
    }

    public function testBasicExecute(): void
    {
        $commandTester = $this->prepareCommand([
            '--html' => true,
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTimeCli($commandTester->getDisplay())
        );
    }

    public function testShowCoverageExecute(): void
    {
        $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--show-coverage' => true,
            '--html' => true,
        ]);

        $content = $this->clearExecutionTimeHtml(\file_get_contents(__DIR__ . '/../../build/coverage_report.html'));

        $this->assertStringEqualsFile(
            __DIR__ . '/../reports/expected_html/coverage.html', $content
        );
    }

    public function testShowShortReportExecute(): void
    {
        $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true,
            '--html' => true,
        ]);

        $content = $this->clearExecutionTimeHtml(\file_get_contents(__DIR__ . '/../../build/coverage_report.html'));

        $this->assertStringEqualsFile(
            __DIR__ . '/../reports/expected_html/coverage_short.html', $content
        );
    }

    public function testShowShortReportExecuteWithErrors(): void
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['base'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true,
            '--html' => true,
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/clover_log.xml

[ERROR]              File don't exists: {\$path}log/src/Log.php
[ERROR]              File don't exists: {\$path}log/src/LogStatic.php
[ERROR]              File don't exists: {\$path}log/src/DefaultJsonMessage.php
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTimeCli(
                    $commandTester->getDisplay()
                )
            )
        );

        $content = $this->clearExecutionTimeHtml(\file_get_contents(__DIR__ . '/../../build/coverage_report.html'));

        $this->assertStringEqualsFile(
            __DIR__ . '/../reports/expected_html/coverage_short_error.html', $content
        );
    }

    public function testFullReport(): void
    {
        $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--full-report' => true,
            '--html' => true,
        ]);

        $content = $this->clearExecutionTimeHtml(\file_get_contents(__DIR__ . '/../../build/coverage_report.html'));

        $this->assertStringEqualsFile(
            __DIR__ . '/../reports/expected_html/coverage_full.html', $content
        );
    }

    public function testFullCovered(): void
    {
        $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_100_percent.xml',
            '--skip-dir' => '',
            '--show-coverage' => true,
            '--html' => true,
        ]);

        $content = $this->clearExecutionTimeHtml(\file_get_contents(__DIR__ . '/../../build/coverage_report.html'));

        $this->assertStringEqualsFile(
            __DIR__ . '/../reports/expected_html/coverage_full_covered.html', $content
        );
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearExecutionTimeCli(string $report): string
    {
        return \substr($report, 0, \strrpos($report, "\n[Execution"));
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearExecutionTimeHtml(string $report): string
    {
        $report = \preg_replace('#<p>Execution time: [\d.]+ sec</p>#', '', $report);
        $report = \preg_replace('#<p>Memory used: [\d.]+ MB</p>#', '', $report);

        return $report;
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearSpaces(string $report): string
    {
        return \preg_replace('#[ ]+\n#', "\n", $report);
    }

    /**
     * @param array $parameters
     * @return CommandTester
     */
    protected function prepareCommand(array $parameters = []): CommandTester
    {
        $application = new Application();

        $application->addCommand(new Commands());

        $command = $application->find('reporter');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            \array_merge(
                ['command' => $command->getName()],
                $parameters
            ),
            ['decorated' => false]
        );

        return $commandTester;
    }
}
