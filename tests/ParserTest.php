<?php

namespace CloverReporterTest;

use PHPUnit\Framework\TestCase;
use CloverReporter\Parser;

class ParserTest extends TestCase
{
    use \CloverReporterTest\Helper;

    public function testCreateParserForNotExistingFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Parser('plik', ['skip-dir' => '']);
    }

    public function testCreateParserForNoneAccessibleFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Parser('tests/reports/no_accessible.xml', ['skip-dir' => '']);
    }

    public function testCreateParser()
    {
        $parser = new Parser(
            $this->reportPaths['base'] . 'clover_log.xml',
            [
                'skip-dir' => '',
                'show-coverage' => false,
            ]
        );

        $this->assertEquals(
            $this->infoList(),
            $parser->getInfoList()
        );
    }

    public function testCreateParserWithDirSkipping()
    {
        $parser = new Parser(
            $this->reportPaths['base'] . 'clover_log.xml',
            [
                'skip-dir' => 'src',
                'show-coverage' => false,
            ]
        );

        $this->assertEquals(
            [
                'files' => [
                    'package' => 'SimpleLog\Message',
                ],
                'log_file' => 'tests/reports/clover_log.xml',
            ],
            $parser->getInfoList()
        );
    }

    public function testCreateParserWithDirSkippingForNoneExistingDir()
    {
        $parser = new Parser(
            $this->reportPaths['base'] . 'clover_log.xml',
            [
                'skip-dir' => 'foo',
                'show-coverage' => false,
            ]
        );

        $this->assertEquals(
            $this->infoList(),
            $parser->getInfoList()
        );
    }

    protected function infoList()
    {
        return [
            'files' => [
                'package' => 'SimpleLog\Message',
                [
                    'path' => '{$path}log/src/Log.php',
                    'package' => 'SimpleLog',
                    'namespace' => 'SimpleLog\Log',
                    'percent' => 84.210526315789465,
                    'info' => [
                            45  => '9',
                            47  => '9',
                            49  => '9',
                            50  => '9',
                            52  => '9',
                            53  => '9',
                            54  => '9',
                            63  => '8',
                            65  => '8',
                            74  => '10',
                            76  => '10',
                            77  => '1',
                            80  => '9',
                            81  => '9',
                            82  => '8',
                            84  => '8',
                            85  => '8',
                            90  => '10',
                            92  => '10',
                            93  => '0',
                            94  => '0',
                            97  => '10',
                            98  => '10',
                            104 => '9',
                            106 => '9',
                            107 => '0',
                            108 => '0',
                            111 => '9',
                            112 => '9',
                            122 => '3',
                            124 => '3',
                            125 => '3',
                            134 => '3',
                            136 => '3',
                            137 => '1',
                            140 => '3',
                            146 => '1',
                            148 => '1',
                        ],
                ],
                [
                    'path' => '{$path}log/src/LogStatic.php',
                    'package' => 'SimpleLog',
                    'namespace' => 'SimpleLog\LogStatic',
                    'percent' => 100,
                    'info' => [
                            21 => '1',
                            23 => '1',
                            24 => '1',
                            35 => '1',
                            37 => '1',
                            38 => '1',
                            48 => '2',
                            50 => '2',
                            51 => '2',
                            60 => '2',
                            62 => '2',
                            63 => '2',
                            71 => '2',
                            73 => '2',
                            74 => '1',
                            76 => '2',
                        ],
                ],
                [
                    'path' => '{$path}log/src/DefaultJsonMessage.php',
                    'package' => 'SimpleLog\Message',
                    'namespace' => 'SimpleLog\Message\DefaultJsonMessage',
                    'percent' => 0,
                    'info' => [
                            18 => '0',
                            21 => '0',
                            26 => '0',
                            28 => '0',
                        ],
                ],
            ],
            'log_file' => 'tests/reports/clover_log.xml',
        ];
    }
}
