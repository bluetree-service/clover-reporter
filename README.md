Bluetree Service Clover Reporter
============

[![Latest Stable Version](https://poser.pugx.org/bluetree-service/clover-reporter/v/stable.svg)](https://packagist.org/packages/bluetree-service/clover-reporter)
[![Total Downloads](https://poser.pugx.org/bluetree-service/clover-reporter/downloads.svg)](https://packagist.org/packages/bluetree-service/clover-reporter)
[![License](https://poser.pugx.org/bluetree-service/clover-reporter/license.svg)](https://packagist.org/packages/bluetree-service/clover-reporter)
[![Dependency Status](https://www.versioneye.com/user/projects/5a8db6560fb24f45b85068bb/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5a8db6560fb24f45b85068bb)

##### Builds
| Travis | Scrutinizer |
|:---:|:---:|
| [![Build Status](https://app.travis-ci.org/bluetree-service/clover-reporter.svg)](https://app.travis-ci.com/github/bluetree-service/clover-reporter) | [![Build Status](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/build-status/master) |

##### Coverage
| Coveralls | Scrutinizer |
|:---:|:---:|
| [![Coverage Status](https://coveralls.io/repos/github/bluetree-service/clover-reporter/badge.svg?branch=master)](https://coveralls.io/github/bluetree-service/clover-reporter?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/?branch=master) |

##### Quality
| Code Climate | SymfonyInsight |
|:---:|:---:|
| [![Code Climate](https://codeclimate.com/github/bluetree-service/clover-reporter/badges/gpa.svg)](https://codeclimate.com/github/bluetree-service/clover-reporter) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/?branch=master) | [![SymfonyInsight](https://insight.symfony.com/projects/fa301fd1-ad6e-401e-b660-62fc3612c9e3/mini.svg)](https://insight.symfony.com/projects/fa301fd1-ad6e-401e-b660-62fc3612c9e3) |
|  | [![Code Intelligence Status](https://scrutinizer-ci.com/g/bluetree-service/clover-reporter/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence) |  |

Documentation
--------------

#### Usage
To use _clover-reporter_ use command `./bin/clover_reporter` with specified parameters.

Most of usage information is available on help `./bin/clover_reporter --help`

##### Basic arguments
*  **report_file**              clover.xml report file [default: "build/logs/clover.xml"]
*  **output**                   destination of html report files [default: ".../output"]

##### Basic options
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
Suggested command: `php vendor/bin/clover_reporter` with optional arguments.  
To execute from other places than project root dir, as firs argument use clover report file path.

Project description
--------------

### Used conventions

* **Namespaces** - each library use namespaces
* **PSR-4** - [PSR-4](http://www.php-fig.org/psr/psr-4/) coding standard
* **Composer** - [Composer](https://getcomposer.org/) usage to load/update libraries

### Requirements

* PHP 8.2 or higher

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
