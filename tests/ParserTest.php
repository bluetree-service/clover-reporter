<?php

namespace SimpleLog\Test;

use PHPUnit\Framework\TestCase;
use CloverReporter\Parser;

class ParserTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateParserForNotExistingFile()
    {
        new Parser('plik', ['skip-dir' => '']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateParserForNoneAccessibleFile()
    {
        new Parser('tests/reports/no_accessible.xml', ['skip-dir' => '']);
    }

    public function testCreateParser()
    {
        $parser = new Parser(
            'tests/reports/clover_cache.xml',
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
            'tests/reports/clover_cache.xml',
            [
                'skip-dir' => 'src',
                'show-coverage' => false,
            ]
        );

        $this->assertEquals(
            [
                'files' => [
                    'package' => 'BlueCache\\Storage',
                ],
            ],
            $parser->getInfoList()
        );
    }

    public function testCreateParserWithDirSkippingForNoneExistingDir()
    {
        $parser = new Parser(
            'tests/reports/clover_cache.xml',
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
                'package' => 'BlueCache\\Storage',
                0 => [
                    'path' => '/home/chajr/Dropbox/C/cache/src/Cache.php',
                    'package' => 'BlueCache',
                    'namespace' => 'Cache',
                    'percent' => 100,
                    'info' => [
                        33 => '2',
                        35 => '2',
                        36 => '1',
                        39 => '1',
                        46 => '4',
                        48 => '4',
                        50 => '4',
                        51 => '1',
                        54 => '4',
                        61 => '8',
                        63 => '8',
                        70 => '1',
                        72 => '1',
                        80 => '1',
                        82 => '1',
                        89 => '1',
                        91 => '1',
                        99 => '8',
                        101 => '8',
                        110 => '3',
                        112 => '3',
                        113 => '3',
                        122 => '12',
                        124 => '12',
                        125 => '12',
                        127 => '12',
                        128 => '3',
                        129 => '3',
                        133 => '12',
                        134 => '1',
                        135 => '1',
                        139 => '11',
                        141 => '11',
                        148 => '3',
                        151 => '3',
                        152 => '1',
                        153 => '1',
                        155 => '1',
                        162 => '12',
                        164 => '12',
                        165 => '11',
                    ],
                ],
                1 => [
                    'path' => '/home/chajr/Dropbox/C/cache/src/CacheItem.php',
                    'package' => 'BlueCache',
                    'namespace' => 'CacheItem',
                    'percent' => 95.34883720930233,
                    'info' => [
                        40 => '36',
                        42 => '36',
                        43 => '1',
                        46 => '35',
                        47 => '35',
                        48 => '35',
                        54 => '36',
                        56 => '36',
                        62 => '31',
                        64 => '31',
                        70 => '8',
                        72 => '8',
                        80 => '30',
                        82 => '30',
                        83 => '2',
                        86 => '29',
                        87 => '28',
                        90 => '4',
                        97 => '31',
                        99 => '31',
                        100 => '31',
                        102 => '31',
                        110 => '1',
                        112 => '1',
                        120 => '6',
                        123 => '6',
                        124 => '1',
                        125 => '1',
                        127 => '5',
                        128 => '4',
                        129 => '4',
                        131 => '0',
                        132 => '1',
                        133 => '1',
                        135 => '1',
                        136 => '1',
                        137 => '1',
                        138 => '1',
                        139 => '1',
                        142 => '1',
                        145 => '5',
                        153 => '5',
                        155 => '5',
                    ],
                ],
                2 => [
                    'path' => '/home/chajr/Dropbox/C/cache/src/Common.php',
                    'package' => 'BlueCache',
                    'namespace' => 'Common',
                    'percent' => 100,
                    'info' => [
                        35 => '28',
                        37 => '28',
                        39 => '28',
                        40 => '26',
                        46 => '28',
                        49 => '28',
                        50 => '2',
                        51 => '2',
                        53 => '26',
                        54 => '4',
                        55 => '24',
                        56 => '24',
                        59 => '2',
                        62 => '26',
                        69 => '24',
                        71 => '24',
                        72 => '24',
                        74 => '24',
                        75 => '2',
                        78 => '24',
                    ],
                ],
                3 => [
                    'path' => '/home/chajr/Dropbox/C/cache/src/SimpleCache.php',
                    'package' => 'BlueCache',
                    'namespace' => 'SimpleCache',
                    'percent' => 100,
                    'info' => [
                        21 => '3',
                        23 => '3',
                        25 => '3',
                        26 => '1',
                        29 => '2',
                        39 => '10',
                        41 => '10',
                        43 => '10',
                        44 => '1',
                        47 => '10',
                        55 => '1',
                        57 => '1',
                        64 => '1',
                        66 => '1',
                        74 => '3',
                        76 => '3',
                        78 => '3',
                        79 => '3',
                        82 => '3',
                        90 => '6',
                        92 => '6',
                        94 => '6',
                        96 => '6',
                        97 => '1',
                        98 => '1',
                        99 => '6',
                        103 => '6',
                        110 => '1',
                        112 => '1',
                        119 => '10',
                        121 => '10',
                        127 => '1',
                        129 => '1',
                    ],
                ],
                4 => [
                    'path' => '/home/chajr/Dropbox/C/cache/src/Storage/File.php',
                    'package' => 'BlueCache\\Storage',
                    'namespace' => 'File',
                    'percent' => 100,
                    'info' => [
                        27 => '39',
                        29 => '39',
                        30 => '39',
                        37 => '30',
                        39 => '30',
                        40 => '30',
                        42 => '30',
                        43 => '30',
                        45 => '30',
                        46 => '1',
                        49 => '29',
                        50 => '3',
                        53 => '27',
                        60 => '11',
                        62 => '11',
                        63 => '5',
                        66 => '6',
                        73 => '5',
                        75 => '5',
                        77 => '5',
                        78 => '5',
                        81 => '5',
                        89 => '6',
                        92 => '6',
                        93 => '3',
                        94 => '3',
                        96 => '3',
                        98 => '1',
                        99 => '3',
                        102 => '1',
                        111 => '27',
                        114 => '27',
                        116 => '27',
                        117 => '21',
                        120 => '23',
                        127 => '11',
                        129 => '11',
                        130 => '9',
                        133 => '2',
                        140 => '27',
                        142 => '27',
                        143 => '27',
                        144 => '24',
                        147 => '20',
                        150 => '6',
                        157 => '24',
                        160 => '24',
                        162 => '24',
                        163 => '1',
                        164 => '1',
                        167 => '23',
                        168 => '23',
                        175 => '24',
                        177 => '24',
                        185 => '7',
                        187 => '7',
                        189 => '7',
                        190 => '7',
                        192 => '7',
                        193 => '7',
                        197 => '7',
                        205 => '9',
                        207 => '9',
                        208 => '7',
                        209 => '7',
                        212 => '9',
                        219 => '31',
                        221 => '31',
                    ],
                ],
            ],
        ];
    }
}
