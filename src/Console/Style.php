<?php

namespace CloverReporter\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Style extends SymfonyStyle
{
    /**
     * @var \Symfony\Component\Console\Helper\FormatterHelper
     */
    protected $formatter;

    /**
     * @var int
     */
    protected $align = 20;

    /**
     * @var OutputInterface
     */
    protected $overrideOutput;

    /**
     * Style constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Command $command
     */
    public function __construct(InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->formatter = $command->getHelper('formatter');
        $this->overrideOutput = $output;

        parent::__construct($input, $output);
    }

    /**
     * @param int $align
     * @return $this
     */
    public function setAlign($align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * @return int
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @param string $section
     * @param string $message
     * @param string $style
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function formatSection($section, $message, $style = 'info')
    {
        $this->writeln(
            $this->formatter->formatSection(
                $section,
                $message,
                $style
            )
        );

        return $this;
    }

    /**
     * @param string|array $messages
     * @param string $style
     * @param bool $large
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function formatBlock($messages, $style, $large = false)
    {
        $this->writeln(
            $this->formatter->formatBlock(
                $messages,
                $style,
                $large
            )
        );

        return $this;
    }

    /**
     * @param array $message
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function errorLine(array $message)
    {
        $this->writeln(
            $this->formatBlock($message, 'error')
        );

        return $this;
    }

    /**
     * @param string|int $strLength
     * @param int $align
     * @return string
     */
    public function align($strLength, $align)
    {
        if (is_string($strLength)) {
            $strLength = mb_strlen($strLength);
        }

        $newAlign = ' ';
        $spaces = $align - $strLength;

        for ($i = 1; $i <= $spaces; $i++) {
            $newAlign .= ' ';
        }

        return $newAlign;
    }

    /**
     * @param $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function okMessage($message)
    {
        $alignment = $this->align(4, $this->align);
        $this->write('<info>[OK]</info>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    /**
     * @param $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function errorMessage($message)
    {
        $alignment = $this->align(7, $this->align);
        $this->write('<fg=red>[ERROR]</>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    /**
     * @param $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function warningMessage($message)
    {
        $alignment = $this->align(9, $this->align);
        $this->write('<comment>[WARNING]</comment>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    public function note($message)
    {
        $alignment = $this->align(0, 100);
        $alignmentMessage = $this->align($message, 91);

        $this->writeln("<bg=blue>$alignment</>");
        $this->writeln("<fg=white;bg=blue>  [NOTE] $message$alignmentMessage</>");
        $this->writeln("<bg=blue>$alignment</>");
    }

    public function caution($message)
    {
        $alignment = $this->align(0, 100);
        $alignmentMessage = $this->align($message, 88);

        $this->writeln("<bg=magenta>$alignment</>");
        $this->writeln("<fg=white;bg=magenta>  [CAUTION] $message$alignmentMessage</>");
        $this->writeln("<bg=magenta>$alignment</>");
    }

    public function success($message)
    {
        $alignment = $this->align(0, 100);
        $alignmentMessage = $this->align($message, 88);

        $this->writeln("<bg=green>$alignment</>");
        $this->writeln("<fg=white;bg=green>  [SUCCESS] $message$alignmentMessage</>");
        $this->writeln("<bg=green>$alignment</>");
    }

    public function warning($message)
    {
        $alignment = $this->align(0, 100);
        $alignmentMessage = $this->align($message, 88);

        $this->writeln("<bg=yellow>$alignment</>");
        $this->writeln("<fg=white;bg=yellow>  [WARNING] $message$alignmentMessage</>");
        $this->writeln("<bg=yellow>$alignment</>");
    }

    public function error($message)
    {
        $alignment = $this->align(0, 100);
        $alignmentMessage = $this->align($message, 90);

        $this->writeln("<bg=red>$alignment</>");
        $this->writeln("<fg=white;bg=red>  [ERROR] $message$alignmentMessage</>");
        $this->writeln("<bg=red>$alignment</>");
    }

    public function formatUncoveredLine($line)
    {
        
    }

    public function formatCoverage()
    {
        
    }

    public function formatFileCoverage()
    {
        
    }

    public function original()
    {
        $this->note(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu vitae sem faucibus porta',
        ));
        $this->caution(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu vitae sem faucibus porta',
        ));
        $this->success(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));
        $this->warning(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));
        $this->error(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));
    }
}
