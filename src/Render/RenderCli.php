<?php

declare(strict_types=1);

namespace CloverReporter\Render;

use CloverReporter\Console\Style;
use Symfony\Component\Filesystem\Filesystem;

class RenderCli extends Common implements RenderInterface
{
    /**
     * @var array
     */
    protected array $infoList;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @var Style
     */
    protected Style $style;

    /**
     * @var string
     */
    public string $beerSymbol = "\xF0\x9F\x8D\xBA";

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
    public function displayCoverage(): void
    {
        $this->allFiles();

        foreach ($this->infoList['files'] as $fileData) {
            $this->style->formatCoverage(
                $fileData['percent'],
                $fileData['namespace'],
                $fileData['path']
            );
        }

        $this->style->newLine();
    }

    /**
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function shortReport(): self
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
                    $this->style->formatUncoveredLine($number + 1, $line);
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
    public function allFiles(): self
    {
        $files = \count($this->infoList['files']);
        $this->style->writeln("Found <info>$files</info> source files:");

        return $this;
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function fullReport(): self
    {
        $this->fileProcessor(function (array $fileData, array $lines) {
            foreach ($lines as $number => $line) {
                $lineCoverage = $fileData['info'][$number + 1] ?? '-';

                if ($lineCoverage === '0') {
                    $this->style->formatUncoveredLine($number + 1, $line, '0');
                } else {
                    $this->style->formatCoveredLine($number, $lineCoverage, $line);
                }
            }

            $this->style->newLine();
        });

        return $this;
    }

    /**
     * @param \Closure $lineProcessor
     * @throws \InvalidArgumentException
     */
    protected function fileProcessor(\Closure $lineProcessor): void
    {
        $filesystem = new Filesystem();

        foreach ($this->infoList['files'] as $fileData) {
            $this->style->formatCoverage(
                $fileData['percent'],
                $fileData['namespace'],
                $fileData['path']
            );

            $path = $fileData['path'];

            if (!$filesystem->exists($path)) {
                $this->style->errorMessage("File don't exists: <comment>$path</comment>");
                continue;
            }

            $content = \file_get_contents($path);
            $lines = \explode("\n", $content);

            $lineProcessor($fileData, $lines);
        }
    }

    /**
     * @param float $startTime
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function summary(float $startTime): self
    {
        $sum = 0;
        $count = 0;
        $coverVal = 0;
        $beer = '';

        foreach ($this->infoList['files'] as $fileData) {
            $sum += $fileData['percent'];
            $count++;
        }

        if ($count > 0) {
            $coverVal = \round($sum / $count, 3);
        }

        $coverage = $this->style->formatCoveragePercent($coverVal);

        if ($coverage === '<info>100</info>') {
            $beer = $this->beerSymbol;
        }

        $this->style->writeln("Total coverage: $coverage% $beer$beer$beer");

        $endTime = \microtime(true);
        $diff = \round($endTime - $startTime, 5);
        $this->style->formatSection('Execution time', $diff . ' sec');
        $this->style->formatSection(
            'Memory used',
            $this->bytes(\memory_get_usage(true))
        );

        return $this;
    }
}
