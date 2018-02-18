Bluetree Service Clover Reporter
============

Documentation
--------------

#### Usage
To use _clover-reporter_ use command `./bin/generate` with specified parameters.

Most of usage information is available on help `./bin/generate --help`

##### Basic arguments
*  **report_file**              clover.xml report file [default: "build/logs/clover.xml"]
*  **output**                   destination of html report files [default: ".../output"]

##### Basic options
*  **-b, --open-browser**       automatically open default browser with html report
*  **-H, --html**               generate html report version
*  **-c, --show-coverage**      show only classes with coverage in percent
*  **-s, --short-report**       show coverage in percent per line with uncovered lines only
*  **-f, --full-report**        show coverage in percent per line with complete script
*  **-d, --skip-dir**  allow to skip specified dirs in root path. Dir delimiter: ";" [default: "vendor;test;tests"]

Install via Composer
--------------
To use packages you can just download package and pace it in your code. But recommended
way to use _CloverReporter_ is install it via Composer. To include _CloverReporter_
libraries paste into composer json:

```json
{
    "require": {
        "bluetree-service/clover-reporter": "version_number"
    }
}
```

#### Usage in project
By default clover reporter will search report file inside of `build/logs` directory in project root dir. And project root
dir should be place to execute report generation.  
Suggested command: `php vendor/bin/generate` with optional arguments.  
To execute from other places than project root dir, as firs argument use clover report file path.

Project description
--------------

### Used conventions

* **Namespaces** - each library use namespaces
* **PSR-4** - [PSR-4](http://www.php-fig.org/psr/psr-4/) coding standard
* **Composer** - [Composer](https://getcomposer.org/) usage to load/update libraries

### Requirements

* PHP 5.6 or higher

Change log
--------------
All release version changes:  
[Change log](https://github.com/bluetree-service/clover-reporter/doc/changelog.md "Change log")

License
--------------
This bundle is released under the Apache license.  
[Apache license](https://github.com/bluetree-service/clover-reporter/LICENSE "Apache license")

Travis Information
--------------
[Travis CI Build Info](https://travis-ci.org/bluetree-service/clover-reporter)
