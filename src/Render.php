<?php

namespace CloverReporter;

use CloverReporter\Console\Style;

class Render
{
    /**
     * @var array
     */
    protected $infoList;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Style
     */
    protected $style;

    /**
     * Render constructor.
     *
     * @param array $options
     * @param array $infoList
     * @param Style $style
     */
    public function __construct(array $options, array $infoList, Style $style)
    {
        $this->options = $options;
        $this->infoList = $infoList;
        $this->style = $style;
    }

    public function displayCoverage()
    {
        foreach ($this->infoList['files'] as $key => $fileData) {
            if ($key === 'package') {
                continue;
            }

            $this->style->formatCoverage(
                $fileData['percent'],
                $fileData['package'] . '\\' . $fileData['namespace']
            );
        }
    }

    public function shortReport()
    {
        $this->fileProcessor(function (array $fileData, array $lines) {
            foreach ($lines as $number => $line) {
                if (isset($fileData['info'][$number + 1])) {
                    $lineCoverage = $fileData['info'][$number + 1];

                    if ($lineCoverage !== '0') {
                        continue;
                    }

                    echo ($number +1) . ': ' . $line . PHP_EOL;
                }

            }
        });
    }

    /**
     *
     * @throws \InvalidArgumentException
     */
    public function fullReport()
    {
        $this->fileProcessor(function (array $fileData, array $lines) {
            foreach ($lines as $number => $line) {
                $lineCoverage = '-';

                if (isset($fileData['info'][$number + 1])) {
                    $lineCoverage = $fileData['info'][$number + 1];
                }

                echo ($number + 1) . ':' . $lineCoverage . ': ' . $line . PHP_EOL;
            }
        });
    }

    public function htmlReport()
    {
        
    }

    /**
     * @param \Closure $lineProcessor
     * @throws \InvalidArgumentException
     */
    protected function fileProcessor(\Closure $lineProcessor)
    {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;

        foreach ($this->infoList['files'] as $key => $fileData) {
            if ($key === 'package') {
                continue;
            }

            $path = $fileData['path'];

            if (!$filesystem->exists('/home/chajr/Dropbox/C')) {
                $path = str_replace(
                    '/home/chajr/Dropbox/C',
                    '/Users/michal/projects/c',
                    $fileData['path']
                );
            }

            if (!$filesystem->exists($path)) {
                throw new \InvalidArgumentException('File don\'t exists: ' . $path);
                
                //@todo add error style and continue;
            }

            $content = file_get_contents($path);

            $lines = explode("\n", $content);

            echo $fileData['package'] . '\\' . $fileData['namespace'];
            echo PHP_EOL;
            echo $fileData['percent'] . '%';
            echo PHP_EOL;

            $lineProcessor($fileData, $lines);

            echo PHP_EOL;
        }
    }
}
