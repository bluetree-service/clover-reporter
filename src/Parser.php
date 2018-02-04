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
        $this->options = $options;
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


//        var_dump($list);
//
//        $path = $list['files'][0]['path'];
//        
//        if (!$filesystem->exists('/home/chajr/Dropbox/C')) {
//            $path = str_replace(
//                '/home/chajr/Dropbox/C',
//                '/Users/michal/projects/c',
//                $list['files'][0]['path']
//            );
//        }
//        
//        $content = file_get_contents($path);
//
//        $lines = explode("\n", $content);
//
//        echo($list['files'][0]['namespace']);
//        echo PHP_EOL;
//        echo($list['files'][0]['all']);
//        echo PHP_EOL;
//        echo($list['files'][0]['covered']);
//        echo PHP_EOL;
//
//        foreach ($lines as $number => $line) {
//            $lineCoverage = '';
//
//            if (isset($list['files'][0]['info'][$number +1])) {
//                $lineCoverage = $list['files'][0]['info'][$number +1];
//            }
//
//            echo $lineCoverage . $line . PHP_EOL;
//        }

//        $dir = new Directory($list['files']);
//        $this->processSingleFiles($xml);
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

                $list['files'][$key]['package'] = $list['files']['package'];
                $list['files'][$key]['path'] = $file->attributes()['name']->__toString();
                $list['files'][$key]['namespace'] = $file->class->attributes()['name']->__toString();
                $list['files'][$key]['percent'] = $this->calculatePercent(
                    (int)$metrics->attributes()['elements']->__toString(),
                    (int)$metrics->attributes()['coveredelements']->__toString()
                );

                $list = $this->processLine($file, $key, $list);

                $key ++;
            }
        }

        return $list;
    }

    /**
     * @param \SimpleXMLElement $file
     * @param int $key
     * @param array $list
     * @return array
     */
    protected function processLine(\SimpleXMLElement $file, $key, array $list)
    {
        if (isset($file->line) && !$this->options['short-report']) {
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