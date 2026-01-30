<?php

namespace CloverReporterTest\Console;

use CloverReporter\Console\Commands;
use PHPUnit\Framework\TestCase;
use CloverReporter\Console\Style;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\BufferedOutput;

class StyleTest extends TestCase
{
    protected $output;
    
    public function testCreatingObject()
    {
        $style = $this->createStyle();

        $this->assertInstanceOf(Style::class, $style);
    }

    public function testOkMessage()
    {
        $style = $this->createStyle();
        $style->okMessage('test');

        $style->setAlign(10);

        $this->assertEquals(10, $style->getAlign());

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
[OK]                 test

EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testFormatBlock()
    {
        $style = $this->createStyle();
        $style->formatBlock('test', 'ok');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
<ok> test </ok>

EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testErrorLine()
    {
        $style = $this->createStyle();
        $style->errorLine('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
 test 

EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testWarningMessage()
    {
        $style = $this->createStyle();
        $style->warningMessage('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
[WARNING]            test

EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testNote()
    {
        $style = $this->createStyle();
        $style->note('test');

        $fetch = $this->output->fetch();

        $out = <<<'EOT'
                                                                                                     
  [NOTE] test                                                                                        
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testCaution()
    {
        $style = $this->createStyle();
        $style->caution('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
                                                                                                     
  [CAUTION] test                                                                                     
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testSuccess()
    {
        $style = $this->createStyle();
        $style->success('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
                                                                                                     
  [SUCCESS] test                                                                                     
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testWarning()
    {
        $style = $this->createStyle();
        $style->warning('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
                                                                                                     
  [WARNING] test                                                                                     
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testError()
    {
        $style = $this->createStyle();
        $style->error('test');

        $fetch = $this->output->fetch();
        $out = <<<'EOT'
                                                                                                     
  [ERROR] test                                                                                       
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testGenericBlock()
    {
        $style = $this->createStyle();
        $style->genericBlock(['test', 'test2'], 'blue', 'ok');

        $fetch = $this->output->fetch();
        file_put_contents('out.txt', $fetch);
        $out = <<<'EOT'
                                                                                                     
  [OK] test                                                                                          
                                                                                                     

                                                                                                     
  [OK] test2                                                                                         
                                                                                                     


EOT;
        $this-> assertEquals($out, $fetch);
    }

    public function testEscapeCharIfLineContainsSymfonyStylesMarkdowns()
    {
        $style = $this->createStyle();
        
        $background = 'red';
        $alignment = '';
        $outputUncovered = "     0::      0                                                                                                                        \n";
        $outputCovered = "     0:0:      ﹤bg=red>﹤/>                                                                                                              \n";

        $style->formatUncoveredLine(0, 0, "<bg=$background>$alignment</>");
        $this->assertEquals($outputUncovered, $this->output->fetch());

        $style->formatCoveredLine(0, 0, "<bg=$background>$alignment</>");
        $this->assertEquals($outputCovered, $this->output->fetch());
    }

    protected function createStyle(): Style
    {
        $application = new Application();
        $application->addCommand(new Commands());
        $command = $application->find('reporter');
        $this->output = new BufferedOutput();

        $style = new Style(
            $this->createMock('Symfony\Component\Console\Input\InputInterface'),
            $this->output,
            $command
        );
        
        return $style;
    }
}
