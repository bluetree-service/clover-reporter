<?php

declare(strict_types=1);

namespace CloverReporter\Render;

use CloverReporter\Console\Style;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class RenderHtml extends Common implements RenderInterface
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
    public const TEMPLATE_PATH = __DIR__ . '/../';

    /**
     * @var string
     */
    public const DEFAULT_PATH = __DIR__ . '/../../build/coverage_report.html';

    /**
     * @var Environment
     */
    protected Environment $twig;

    /**
     * @var array
     */
    protected const TEMPLATE_DATA = [
        'title' => 'Code Coverage Report',
        'report_file' => 'Coverage report file',
        'total_coverage' => 'Total coverage',
        'summary' => 'Summary',
        'file_coverage' => 'File coverage',
        'th_coverage' => 'Coverage',
        'th_class' => 'Class',
        'th_source_file' => 'Source File',
    ];

    /**
     * @var array
     */
    protected array $templateData = [];

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

        $loader = new FilesystemLoader(self::TEMPLATE_PATH);

        $this->options = $options;
        $this->infoList = $infoList;
        $this->style = $style;
        $this->twig = new Environment($loader);
    }

    /**
     * @param float $valueToCheck
     * @return string
     */
    protected function match(float $valueToCheck): string
    {
        return match (true) {
            $valueToCheck < 20 => 'uncovered-class',
            $valueToCheck >= 80 => 'covered excellent',
            $valueToCheck >= 60 => 'covered good',
            $valueToCheck >= 40 => 'covered medium',
            $valueToCheck >= 20 => 'covered bad',
        };
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function fullReport(): self
    {
        $this->report(false);

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function displayCoverage(): void
    {
        $coverVal = 0;
        $count = 0;
        $sum = 0;

        foreach ($this->infoList['files'] as $fileData) {
            $sum += $fileData['percent'];
            $count++;
        }

        if ($count > 0) {
            $coverVal = \round($sum / $count, 3);
        }

        $this->templateData['total_coverage_class'] = $this->match($coverVal);
        $this->templateData['total_coverage_percent'] = $coverVal;

        foreach ($this->infoList['files'] as $key => $fileData) {
            $coverage = \round($fileData['percent'], 3);
            $path = $fileData['path'];

            $this->templateData['coverages'][$key]['percent'] = $coverage;
            $this->templateData['coverages'][$key]['namespace'] = $fileData['namespace'];
            $this->templateData['coverages'][$key]['path'] = $path;
            $this->templateData['coverages'][$key]['class'] = $this->match($coverage);
        }
    }

    /**
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function shortReport(): self
    {
        $this->report(true);

        return $this;
    }

    /**
     * @param bool $isShortReport
     * @throws \InvalidArgumentException
     */
    protected function report(bool $isShortReport): void
    {
        $this->displayCoverage();
        $this->templateData['show_coverage'] = 1;

        foreach ($this->infoList['files'] as $key => $fileData) {
            $coverage = \round($fileData['percent'], 3);
            $path = $fileData['path'];

            $this->templateData['files'][$key]['percent'] = $coverage;
            $this->templateData['files'][$key]['namespace'] = $fileData['namespace'];
            $this->templateData['files'][$key]['path'] = $path;
            $this->templateData['files'][$key]['class'] = $this->match($coverage);

            $this->processFile($path, $key, $isShortReport, $fileData);
        }
    }

    /**
     * @param string $path
     * @param int $key
     * @param bool $isShortReport
     * @param array $fileData
     * @throws \InvalidArgumentException
     */
    protected function processFile(string $path, int $key, bool $isShortReport, array $fileData): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $this->templateData['files'][$key]['lines'][0]['content'] = "File don't exists: $path";
            $this->templateData['files'][$key]['lines'][0]['coverage'] = '';
            $this->templateData['files'][$key]['lines'][0]['class'] = 'uncovered';

            $this->style->errorMessage("File don't exists: <comment>$path</comment>");

            return;
        }

        $content = \file_get_contents($path);
        $lines = \explode("\n", $content);

        foreach ($lines as $number => $line) {
            $lineCoverage = $fileData['info'][$number + 1] ?? '-';

            if ($lineCoverage !== '0' && $isShortReport) {
                continue;
            }

            if ($lineCoverage === '0') {
                $class = 'uncovered';
            } elseif ($lineCoverage === '-') {
                $class = '';
            } else {
                $class = 'covered';
            }

            $this->templateData['files'][$key]['lines'][$number]['content'] = $line;
            $this->templateData['files'][$key]['lines'][$number]['coverage'] = $lineCoverage;
            $this->templateData['files'][$key]['lines'][$number]['class'] = $class;
            $this->templateData['files'][$key]['lines'][$number]['line'] = $number + 1;
        }
    }

    /**
     * @param float $startTime
     * @return $this
     *@throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax|\Twig_Error_Loader
     * @throws \InvalidArgumentException
     */
    public function summary(float $startTime): self
    {
        $this->templateData['title'] = self::TEMPLATE_DATA['title'];
        $this->templateData['report_file'] = self::TEMPLATE_DATA['report_file'];
        $this->templateData['total_coverage'] = self::TEMPLATE_DATA['total_coverage'];
        $this->templateData['summary'] = self::TEMPLATE_DATA['summary'];
        $this->templateData['file_coverage'] = self::TEMPLATE_DATA['file_coverage'];
        $this->templateData['th_coverage'] = self::TEMPLATE_DATA['th_coverage'];
        $this->templateData['th_class'] = self::TEMPLATE_DATA['th_class'];
        $this->templateData['th_source_file'] = self::TEMPLATE_DATA['th_source_file'];
        $this->templateData['log_file'] = $this->infoList['log_file'];

        $endTime = \microtime(true);
        $diff = \round($endTime - $startTime, 5);
        $this->templateData['exec_time'] = $diff;
        $this->templateData['memory'] = $this->bytes(\memory_get_usage(true));

        $content = $this->twig->render(
            'template.twig',
            $this->templateData
        );

        $path = !empty($this->options['html_path']) ? $this->options['html_path'] : self::DEFAULT_PATH;
        \file_put_contents($path, $content);

        $this->style->formatSection('Execution time', $diff . ' sec');
        $this->style->formatSection(
            'Memory used',
            $this->bytes(\memory_get_usage(true))
        );

        return $this;
    }
}
