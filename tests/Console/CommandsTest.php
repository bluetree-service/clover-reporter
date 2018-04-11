<?php

namespace CloverReporterTest\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\TestCase;
use CloverReporter\Console\Commands;

class CommandsTest extends TestCase
{
    use \CloverReporterTest\Helper;

    public function setUp()
    {
        $this->copyFixedReports('clover_log.xml');
        $this->copyFixedReports('clover_100_percent.xml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIncorrectReportFile()
    {
        $this->prepareCommand();
    }

    public function testBasicExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Total coverage: 61.404% 
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    public function testShowCoverageExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--show-coverage' => true,
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\Log
  - 100%        SimpleLog\LogStatic
  - 0%          SimpleLog\Message\DefaultJsonMessage

Total coverage: 61.404% 
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    public function testShowShortReportExecute()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\Log
93:         {
94:             if (\$this->defaultParams['storage'] instanceof StorageInterface) {
107:        {
108:            if (\$this->defaultParams['message'] instanceof MessageInterface) {

  - 100%        SimpleLog\LogStatic
  - 0%          SimpleLog\Message\DefaultJsonMessage
18:          * @param string|array|object \$message
21:          */
26:             list(\$date, \$time) = explode(';', strftime(self::DATE_FORMAT . ';' . self::TIME_FORMAT, time()));
28:             \$this->messageScheme['date'] = \$date;


Total coverage: 61.404% 
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTime(
                    $commandTester->getDisplay()
                )
            )
        );
    }

    public function testShowShortReportExecuteWithErrors()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['base'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--short-report' => true
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/clover_log.xml

Found 3 source files:
  - 84.211%     SimpleLog\\Log
[ERROR]              File don't exists: {\$path}log/src/Log.php
  - 100%        SimpleLog\\LogStatic
[ERROR]              File don't exists: {\$path}log/src/LogStatic.php
  - 0%          SimpleLog\\Message\\DefaultJsonMessage
[ERROR]              File don't exists: {\$path}log/src/DefaultJsonMessage.php

Total coverage: 61.404% 
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTime(
                    $commandTester->getDisplay()
                )
            )
        );
    }

    public function testFullReport()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_log.xml',
            '--skip-dir' => '',
            '--full-report' => true
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_log.xml

  - 84.211%     SimpleLog\\Log
0:-:      <?php
1:-:
2:-:      namespace SimpleLog;
3:-:
4:-:      use Psr\\Log\\LoggerInterface;
5:-:      use Psr\\Log\\LoggerTrait;
6:-:      use Psr\\Log\\LogLevel;
7:-:      use Psr\\Log\\InvalidArgumentException;
8:-:      use SimpleLog\\Storage\\StorageInterface;
9:-:      use SimpleLog\\Message\\MessageInterface;
10:-:
11:-:     class Log implements LogInterface, LoggerInterface
12:-:     {
13:-:         use LoggerTrait;
14:-:
15:-:         /**
16:-:          * @var array
17:-:          */
18:-:         protected \$defaultParams = [
19:-:             'log_path' => './log',
20:-:             'level' => 'notice',
21:-:             'storage' => \\SimpleLog\\Storage\\File::class,
22:-:             'message' => \\SimpleLog\\Message\\DefaultMessage::class,
23:-:         ];
24:-:
25:-:         /**
26:-:          * @var \\SimpleLog\\Storage\\StorageInterface
27:-:          */
28:-:         protected \$storage;
29:-:
30:-:         /**
31:-:          * @var array
32:-:          */
33:-:         protected \$levels = [];
34:-:
35:-:         /**
36:-:          * @var \\SimpleLog\\Message\\MessageInterface
37:-:          */
38:-:         protected \$message;
39:-:
40:-:         /**
41:-:          * @param array \$params
42:-:          * @throws \\ReflectionException
43:-:          */
44:9:         public function __construct(array \$params = [])
45:-:         {
46:9:             \$this->defaultParams = array_merge(\$this->defaultParams, \$params);
47:-:
48:9:             \$levels = new \\ReflectionClass(new LogLevel);
49:9:             \$this->levels = \$levels->getConstants();
50:-:
51:9:             \$this->reloadStorage();
52:9:             \$this->reloadMessage();
53:9:         }
54:-:
55:-:         /**
56:-:          * log event information into file
57:-:          *
58:-:          * @param array|string|object \$message
59:-:          * @param array \$context
60:-:          * @return \$this
61:-:          */
62:8:         public function makeLog(\$message, array \$context = [])
63:-:         {
64:8:             \$this->log(\$this->defaultParams['level'], \$message, \$context);
65:-:
66:-:             return \$this;
67:-:         }
68:-:
69:-:         /**
70:-:          * @param string \$level
71:-:          * @param string|array|object \$message
72:-:          * @param array \$context
73:10:          * @throws \\Psr\\Log\\InvalidArgumentException
74:-:          */
75:10:         public function log(\$level, \$message, array \$context = [])
76:1:         {
77:-:             if (!in_array(\$level, \$this->levels, true)) {
78:-:                 throw new InvalidArgumentException('Level not defined: ' . \$level);
79:9:             }
80:9:
81:8:             \$newMessage = \$this->message
82:-:                 ->createMessage(\$message, \$context)
83:8:                 ->getMessage();
84:8:
85:-:             \$this->storage->store(\$newMessage, \$level);
86:-:         }
87:-:
88:-:         /**
89:10:          * @return \$this
90:-:          */
91:10:         protected function reloadStorage()
93:0:         {
94:0:             if (\$this->defaultParams['storage'] instanceof StorageInterface) {
94:-:                 \$this->storage = \$this->defaultParams['storage'];
95:-:                 return \$this;
96:10:             }
97:10:
98:-:             \$this->storage = new \$this->defaultParams['storage'](\$this->defaultParams);
99:-:             return \$this;
100:-:        }
101:-:
102:-:        /**
103:9:         * @return \$this
104:-:         */
105:9:        protected function reloadMessage()
107:0:        {
108:0:            if (\$this->defaultParams['message'] instanceof MessageInterface) {
108:-:                \$this->message = \$this->defaultParams['message'];
109:-:                return \$this;
110:9:            }
111:9:
112:-:            \$this->message = new \$this->defaultParams['message'](\$this->defaultParams);
113:-:            return \$this;
114:-:        }
115:-:
116:-:        /**
117:-:         * set log option for all future executions of makeLog
118:-:         *
119:-:         * @param string \$key
120:-:         * @param mixed \$val
121:3:         * @return \$this
122:-:         */
123:3:        public function setOption(\$key, \$val)
124:3:        {
125:-:            \$this->defaultParams[\$key] = \$val;
126:-:            return \$this->reloadStorage();
127:-:        }
128:-:
129:-:        /**
130:-:         * return all configuration or only given key value
131:-:         *
132:-:         * @param null|string \$key
133:3:         * @return array|mixed
134:-:         */
135:3:        public function getOption(\$key = null)
136:1:        {
137:-:            if (is_null(\$key)) {
138:-:                return \$this->defaultParams;
139:3:            }
140:-:
141:-:            return \$this->defaultParams[\$key];
142:-:        }
143:-:
144:-:        /**
145:1:         * @return string
146:-:         */
147:1:        public function getLastMessage()
148:-:        {
149:-:            return \$this->message->getMessage();
150:-:        }
151:-:    }
152:-:

  - 100%        SimpleLog\\LogStatic
0:-:      <?php
1:-:
2:-:      namespace SimpleLog;
3:-:
4:-:      class LogStatic
5:-:      {
6:-:          /**
7:-:           * @var Log
8:-:           */
9:-:          protected static \$instance;
10:-:
11:-:         /**
12:-:          * log event information into file
13:-:          *
14:-:          * @param string \$level
15:-:          * @param array|string \$message
16:-:          * @param array \$context
17:-:          * @param array \$params
18:-:          */
19:-:         public static function log(\$level, \$message, array \$context = [], array \$params = [])
20:1:         {
21:-:             self::init(\$params);
22:1:             self::\$instance->log(\$level, \$message, \$context);
23:1:         }
24:-:
25:-:         /**
26:-:          * log event information into file
27:-:          *
28:-:          * @param array|string \$message
29:-:          * @param array \$context
30:-:          * @param array \$params
31:-:          */
32:-:         public static function makeLog(\$message, array \$context = [], array \$params = [])
33:-:         {
34:1:             self::init(\$params);
35:-:             self::\$instance->makeLog(\$message, \$context);
36:1:         }
37:1:
38:-:         /**
39:-:          * set log option for all future executions of makeLog
40:-:          *
41:-:          * @param string \$key
42:-:          * @param mixed \$val
43:-:          * @return Log
44:-:          */
45:-:         public static function setOption(\$key, \$val)
46:-:         {
47:2:             self::init();
48:-:             return self::\$instance->setOption(\$key, \$val);
49:2:         }
50:2:
51:-:         /**
52:-:          * return all configuration or only given key value
53:-:          *
54:-:          * @param null|string \$key
55:-:          * @return array|mixed
56:-:          */
57:-:         public static function getOption(\$key = null)
58:-:         {
59:2:             self::init();
60:-:             return self::\$instance->getOption(\$key);
61:2:         }
62:2:
63:-:         /**
64:-:          * create Log object if not exists
65:-:          *
66:-:          * @param array \$params
67:-:          */
68:-:         protected static function init(array \$params = [])
69:-:         {
70:2:             if (is_null(self::\$instance)) {
71:-:                 self::\$instance = new Log(\$params);
72:2:             }
73:1:         }
74:-:     }
75:2:

  - 0%          SimpleLog\\Message\\DefaultJsonMessage
0:-:      <?php
1:-:
2:-:      namespace SimpleLog\\Message;
3:-:
4:-:      class DefaultJsonMessage extends DefaultMessage
5:-:      {
6:-:          /**
7:-:           * @var array
8:-:           */
9:-:          protected \$messageScheme = [];
10:-:
11:-:         /**
12:-:          * @var array
13:-:          */
14:-:         protected \$context = [];
15:-:
16:-:         /**
18:0:          * @param string|array|object \$message
18:-:          * @param array \$context
19:-:          * @return \$this
21:0:          */
21:-:         public function createMessage(\$message, array \$context)
22:-:         {
23:-:             \$this->context = \$context;
24:-:
26:0:             list(\$date, \$time) = explode(';', strftime(self::DATE_FORMAT . ';' . self::TIME_FORMAT, time()));
26:-:
28:0:             \$this->messageScheme['date'] = \$date;
28:-:             \$this->messageScheme['time'] = \$time;
29:-:
30:-:             if (method_exists(\$message, '__toString')) {
31:-:                 \$message = (string)\$message;
32:-:             }
33:-:
34:-:             \$this->messageScheme['data'] = \$message;
35:-:
36:-:             return \$this;
37:-:         }
38:-:
39:-:         /**
40:-:          * @return string
41:-:          */
42:-:         public function getMessage()
43:-:         {
44:-:             \$this->message = json_encode(\$this->messageScheme);
45:-:             \$this->buildContext(\$this->context);
46:-:
47:-:             return \$this->message;
48:-:         }
49:-:     }
50:-:

Total coverage: 61.404% 
EOT;

        $this->assertEquals(
            $output,
            $this->clearSpaces(
                $this->clearExecutionTime(
                    $commandTester->getDisplay()
                )
            )
        );
    }

    public function testFullCovered()
    {
        $commandTester = $this->prepareCommand([
            'report_file' => $this->reportPaths['fix'] . 'clover_100_percent.xml',
            '--skip-dir' => '',
            '--show-coverage' => true,
        ]);

        $output = <<<EOT

Clover report generator.
========================

[Coverage report file] tests/reports/fixed/clover_100_percent.xml

Found 3 source files:
  - 100%        BlueEvent\\Event\\Base\\Event
  - 100%        BlueEvent\\Event\\Base\\EventDispatcher
  - 100%        BlueEvent\\Event\\Base\\EventLog

Total coverage: 100% 🍺🍺🍺
EOT;

        $this->assertEquals(
            $output,
            $this->clearExecutionTime($commandTester->getDisplay())
        );
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearExecutionTime($report)
    {
        return substr($report, 0, strrpos($report, "\n[Execution"));
    }

    /**
     * @param string $report
     * @return string
     */
    protected function clearSpaces($report)
    {
        return preg_replace('#[ ]+\n#', "\n", $report);
    }

    /**
     * @param array $parameters
     * @return CommandTester
     */
    protected function prepareCommand(array $parameters = [])
    {
        $application = new Application;

        $application->add(new Commands);

        $command = $application->find('reporter');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array_merge(['command' => $command->getName()], $parameters),
            ['decorated' => false]
        );

        return $commandTester;
    }
}
