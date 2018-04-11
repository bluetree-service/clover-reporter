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
        unset($infoList['files']['package']);

        $this->options = $options;
        $this->infoList = $infoList;
        $this->style = $style;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function displayCoverage()
    {
        $this->allFiles();

        foreach ($this->infoList['files'] as $fileData) {
            $this->style->formatCoverage(
                $fileData['percent'],
                $fileData['package'] . '\\' . $fileData['namespace']
            );
        }

        $this->style->newLine();
    }

    /**
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function shortReport()
    {
        $this->allFiles()->fileProcessor(function (array $fileData, array $lines) {
            $newLine = false;

            foreach ($lines as $number => $line) {
                if (isset($fileData['info'][$number + 1])) {
                    $lineCoverage = $fileData['info'][$number + 1];

                    if ($lineCoverage !== '0') {
                        continue;
                    }

                    $newLine = true;
                    $this->style->formatUncoveredLine($number +1, $line);
                }
            }

            if ($newLine) {
                $this->style->newLine();
            }
        });

        $this->style->newLine();

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function allFiles()
    {
        $files = count($this->infoList['files']);
        $this->style->writeln("Found <info>$files</info> source files:");

        return $this;
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function fullReport()
    {
        $this->fileProcessor(function (array $fileData, array $lines) {
            foreach ($lines as $number => $line) {
                $lineCoverage = '-';

                if (isset($fileData['info'][$number + 1])) {
                    $lineCoverage = $fileData['info'][$number + 1];
                }

                if ($lineCoverage === '0') {
                    $this->style->formatUncoveredLine($number +1, $line, 0);
                } else {
                    $this->style->formatCoveredLine($number, $lineCoverage, $line);
                }
            }

            $this->style->newLine();
        });

        return $this;
    }

//    public function htmlReport()
//    {
//        
//    }

    /**
     * @param \Closure $lineProcessor
     * @throws \InvalidArgumentException
     */
    protected function fileProcessor(\Closure $lineProcessor)
    {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;

        foreach ($this->infoList['files'] as $fileData) {
            $this->style->formatCoverage(
                $fileData['percent'],
                $fileData['package'] . '\\' . $fileData['namespace']
            );

            $path = $fileData['path'];

            if (!$filesystem->exists($path)) {
                $this->style->errorMessage("File don't exists: <comment>$path</comment>");
                continue;
            }

            $content = file_get_contents($path);

            $lines = explode("\n", $content);

            $lineProcessor($fileData, $lines);
        }
    }

    /**
     * @param int $startTime
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function summary($startTime)
    {
        //@todo count warnings, errors & ok
        $sum = 0;
        $count = 0;
        $coverVal = 0;
        $beer = '';

        foreach ($this->infoList['files'] as $fileData) {
            $sum += $fileData['percent'];
            $count++;
        }

        if ($count > 0) {
            $coverVal = round($sum / $count, 3);
        }

        $coverage = $this->style->formatCoveragePercent($coverVal);

        if ($coverage === '<info>100</info>') {
            $beer = "\xF0\x9F\x8D\xBA";
        }

        $this->style->writeln("Total coverage: $coverage% $beer$beer$beer");

        $endTime = microtime(true);
        $diff = round($endTime - $startTime, 5);
        $this->style->formatSection('Execution time', $diff . ' sec');

        return $this;
    }
}
