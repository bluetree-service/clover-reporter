<?php

namespace CloverReporter;

use BlueData\Calculation\Math;

class Parser
{
    /**
     * @var array
     */
    protected $infoList = [];

    protected $options = [];

    public function __construct($file, $options)
    {
        $this->options = $this->excludeDirs($options);
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;
        $currentDir = getcwd() . '/';
        $cloverFile = $currentDir . $file;

        if (!$filesystem->exists($cloverFile)) {
            throw new \InvalidArgumentException('File don\'t exists: ' . $cloverFile);
        }

        $xml = file_get_contents($cloverFile);

        if (!$xml) {
            throw new \InvalidArgumentException('Unable to access file: ' . $cloverFile);
        }

        $simpleXMLElement = simplexml_load_string($xml);

        $this->infoList = $this->processPackages($simpleXMLElement);
    }

    protected function excludeDirs($options)
    {
        $options['skip-dir'] = explode(';', $options['skip-dir']);
        return $options;
    }

//    protected function processSingleFiles($xml)
//    {
//        if (isset($xml->project->file)) {
//            foreach ($xml->project->file as $item) {
//                var_dump($item);
//            }
//        }
//    }
    /**
     * @return array
     */
    public function getInfoList()
    {
        return $this->infoList;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function processPackages(\SimpleXMLElement $xml)
    {
        $list = [
            'files' => []
        ];
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
    public function processFile(\SimpleXMLElement $package, &$key, array $list)
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

                $key ++;
            }
        }
        return $list;
    }

    protected function checkDir($filePath)
    {
        foreach ($this->options['skip-dir'] as $dir) {
            if (preg_match("#$dir#", $filePath)) {
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
    protected function processLine(\SimpleXMLElement $file, $key, array $list)
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
     * @return int
     */
    protected function calculatePercent($all, $percent)
    {
        return Math::numberToPercent($percent, $all) ?: 0;
    }
}
