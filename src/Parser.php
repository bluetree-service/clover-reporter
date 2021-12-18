<?php

declare(strict_types=1);

namespace CloverReporter;

use BlueData\Calculation\Math;
use Symfony\Component\Filesystem\Filesystem;

class Parser
{
    /**
     * @var array
     */
    protected $infoList = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $file
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(string $file, array $options)
    {
        $this->options = $this->excludeDirs($options);
        $filesystem = new Filesystem();
        $currentDir = \getcwd() . '/';
        $cloverFile = $currentDir . $file;

        if (!$filesystem->exists($cloverFile)) {
            throw new \InvalidArgumentException('File don\'t exists: ' . $cloverFile);
        }

        $xml = \file_get_contents($cloverFile);

        if (!$xml) {
            throw new \InvalidArgumentException('Unable to access file: ' . $cloverFile);
        }

        $simpleXMLElement = \simplexml_load_string($xml);

        $this->infoList = $this->processPackages($simpleXMLElement);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function excludeDirs(array $options): array
    {
        if (empty($options['skip-dir'])) {
            $options['skip-dir'] = [];
        } else {
            $options['skip-dir'] = \explode(';', $options['skip-dir']);
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getInfoList(): array
    {
        return $this->infoList;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function processPackages(\SimpleXMLElement $xml): array
    {
        $list = ['files' => []];
        $key = 0;

        if (isset($xml->project->package)) {
            /** @var \SimpleXMLElement $package */
            foreach ($xml->project->package as $package) {
                $list['files']['package'] = $package->attributes()['name']->__toString();

                $list = $this->processFile($package, $key, $list);
            }
        }

        return $list;
    }

    /**
     * @param \SimpleXMLElement $package
     * @param int $key
     * @param array $list
     * @return array
     */
    public function processFile(\SimpleXMLElement $package, int &$key, array $list): array
    {
        if (isset($package->file)) {
            /** @var \SimpleXMLElement $file */
            foreach ($package->file as $file) {
                $metrics = $file->class->metrics;
                $elements = $metrics->attributes()['elements']->__toString();
                $path = $file->attributes()['name']->__toString();

                if ($elements === '0' || $this->checkDir($path)) {
                    continue;
                }

                $list['files'][$key]['path'] = $path;
                $list['files'][$key]['package'] = $list['files']['package'];
                $list['files'][$key]['namespace'] = $file->class->attributes()['name']->__toString();
                $list['files'][$key]['percent'] = $this->calculatePercent(
                    (int)$elements,
                    (int)$metrics->attributes()['coveredelements']->__toString()
                );

                $list = $this->processLine($file, $key, $list);

                $key++;
            }
        }

        return $list;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function checkDir(string $filePath): bool
    {
        if (empty($this->options['skip-dir'])) {
            return false;
        }

        foreach ($this->options['skip-dir'] as $dir) {
            if (\preg_match("#$dir#", $filePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \SimpleXMLElement $file
     * @param int $key
     * @param array $list
     * @return array
     */
    protected function processLine(\SimpleXMLElement $file, int $key, array $list): array
    {
        if (isset($file->line) && !$this->options['show-coverage']) {
            /** @var \SimpleXMLElement $line */
            foreach ($file->line as $line) {
                $attr = $line->attributes();
                $list['files'][$key]['info'][$attr['num']->__toString()] = $attr['count']->__toString();
            }
        }

        return $list;
    }

    /**
     * @param int $all
     * @param int $percent
     * @return float
     */
    protected function calculatePercent(int $all, int $percent): float
    {
        return Math::numberToPercent($percent, $all) ?: 0;
    }
}
