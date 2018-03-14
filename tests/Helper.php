<?php

namespace CloverReporterTest;

use Symfony\Component\Filesystem\Filesystem;

trait Helper
{
    protected $reportPaths = [
        'base' => 'tests' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR,
        'fix' => 'tests' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'fixed' . DIRECTORY_SEPARATOR,
    ];
    protected $coverageClassesPath = 'tests' . DIRECTORY_SEPARATOR . 'CoverageClasses' . DIRECTORY_SEPARATOR;

    /**
     * @param string $report
     * @return string
     */
    public function currentPathFixer($report)
    {
        $basePath = getcwd();
        $newPath = $basePath . DIRECTORY_SEPARATOR . $this->coverageClassesPath;

        return str_replace('{$path}', $newPath, $report);
    }

    /**
     * @param string $reportName
     */
    public function copyFixedReports($reportName)
    {
        (new FileSystem)->remove($this->reportPaths['fix'] . $reportName);

        $report = file_get_contents($this->reportPaths['base'] . $reportName);
        file_put_contents($this->reportPaths['fix'] . $reportName, $this->currentPathFixer($report));
    }
}
