<?php

namespace CloverReporter;

class Parser
{
    public function __construct($file)
    {
        $xml = simplexml_load_file($file);

        $list = $this->processPackages($xml);

//        var_dump($list);
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;

        $path = $list['files'][0]['path'];
        
        if (!$filesystem->exists('/home/chajr/Dropbox/C')) {
            $path = str_replace(
                '/home/chajr/Dropbox/C',
                '/Users/michal/projects/c',
                $list['files'][0]['path']
            );
        }
        
        $content = file_get_contents($path);

        $lines = explode("\n", $content);

        foreach ($lines as $number => $line) {
            $lineCoverage = '';

            if (isset($list['files'][0]['info'][$number +1])) {
                $lineCoverage = $list['files'][0]['info'][$number +1];
            }

            echo $lineCoverage . $line . PHP_EOL;
        }

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

    protected function processPackages($xml)
    {
        //package -> file -> line (num, count - increase color depends of count)
        $list = [
            'files' => []
        ];

        if (isset($xml->project->package)) {
            /** @var \SimpleXMLElement $package */
            foreach ($xml->project->package as $package) {
                var_dump($package->attributes()['name']->__toString());

                if (isset($package->file)) {
                    $key = 0;

                    /** @var \SimpleXMLElement $file */
                    foreach ($package->file as $file) {
                        $list['files'][$key]['path'] = $file->attributes()['name']->__toString();

                        if (isset($file->line)) {
                            /** @var \SimpleXMLElement $line */
                            foreach ($file->line as $line) {
                                $attr = $line->attributes();
                                $list['files'][$key]['info'][$attr['num']->__toString()] = $attr['count']->__toString();
                            }
                        }

                        $key ++;
//                        break;
                    }
                }
//                break;
            }
        }

        return $list;
    }
}
