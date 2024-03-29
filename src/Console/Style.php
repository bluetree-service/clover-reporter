<?php

declare(strict_types=1);

namespace CloverReporter\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function mb_strlen;

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
     * Style constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Command $command
     */
    public function __construct(InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->formatter = $command->getHelper('formatter');

        parent::__construct($input, $output);
    }

    /**
     * @param int $align
     * @return $this
     */
    public function setAlign(int $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * @return int
     */
    public function getAlign(): int
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
    public function formatSection(string $section, string $message, string $style = 'info'): self
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
    public function formatBlock($messages, string $style, bool $large = false): self
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
    public function errorLine(array $message): self
    {
        $this->writeln(
            $this->formatBlock($message, 'error')
        );

        return $this;
    }

    /**
     * @param int $strLength
     * @param int $align
     * @return string
     */
    public function align(int $strLength, int $align): string
    {
        $newAlign = ' ';
        $spaces = $align - $strLength;

        for ($i = 1; $i <= $spaces; $i++) {
            $newAlign .= ' ';
        }

        return $newAlign;
    }

    /**
     * @param mixed $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function okMessage($message): self
    {
        $alignment = $this->align(4, $this->align);
        $this->write('<info>[OK]</info>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    /**
     * @param mixed $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function errorMessage($message): self
    {
        $alignment = $this->align(7, $this->align);
        $this->write('<fg=red>[ERROR]</>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    /**
     * @param mixed $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function warningMessage($message): self
    {
        $alignment = $this->align(9, $this->align);
        $this->write('<comment>[WARNING]</comment>');
        $this->write($alignment);
        $this->writeln($message);

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function note($message): self
    {
        return $this->genericBlock($message, 'blue', 'note');
    }

    /**
     * @param string $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function caution($message): self
    {
        return $this->genericBlock($message, 'magenta', 'caution');
    }

    /**
     * @param string $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function success($message): self
    {
        return $this->genericBlock($message, 'green', 'success');
    }

    /**
     * @param string $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function warning($message): self
    {
        return $this->genericBlock($message, 'yellow', 'warning');
    }

    /**
     * @param string $message
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function error($message): self
    {
        return $this->genericBlock($message, 'red', 'error');
    }

    /**
     * @param string $message
     * @param string $background
     * @param string $type
     * @param int $length
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function genericBlock(string $message, string $background, string $type, int $length = 100): self
    {
        $type = \strtoupper($type);
        $alignment = $this->align(0, $length);
        $alignmentMessage = $this->align(mb_strlen($message), $length - (mb_strlen($type) + 5));

        $this->writeln("<bg=$background>$alignment</>");
        $this->writeln("<fg=white;bg=$background>  [$type] $message$alignmentMessage</>");
        $this->writeln("<bg=$background>$alignment</>");
        $this->newLine();

        return $this;
    }

    /**
     * @param int $lineNumber
     * @param string $line
     * @param string $coverage
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function formatUncoveredLine(int $lineNumber, string $line, string $coverage = ''): self
    {
        $endAlign = $this->align(mb_strlen($line), 120);
        $this->writeln(
            "     <comment>$lineNumber</comment>:"
            . ($coverage === '' ? $coverage : "<fg=red>$coverage</>:")
            . $this->align(mb_strlen((string) $lineNumber), 6)
            . "<error>$line$endAlign</error>"
        );

        return $this;
    }

    /**
     * @param float $coverage
     * @param string $namespace
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function formatCoverage(float $coverage, string $namespace): self
    {
        $coverage = \round($coverage, 3);

        $align = $this->align(mb_strlen((string) $coverage), 10);

        $this->write('  - ');
        $this->write($this->formatCoveragePercent($coverage));
        $this->write('%');
        $this->write($align);
        $this->writeln($namespace);

        return $this;
    }

    /**
     * @param int $lineNumber
     * @param int|string $lineCoverage
     * @param string $line
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function formatCoveredLine(int $lineNumber, $lineCoverage, string $line): self
    {
        $lineColor = 'info';
        if ($lineCoverage === '-') {
            $lineColor = 'comment';
        }

        $endAlign = $this->align(mb_strlen($line), 120);
        $this->writeln(
            "     <comment>$lineNumber</comment>:"
            . "<$lineColor>$lineCoverage</$lineColor>:"
            . $this->align(mb_strlen((string) $lineNumber), 6)
            . "<$lineColor>$line$endAlign</$lineColor>"
        );

        return $this;
    }

    /**
     * @param float $coverage
     * @return string
     */
    public function formatCoveragePercent(float $coverage): string
    {
        switch (true) {
            case $coverage < 40:
                return "<fg=red>$coverage</>";

            case $coverage < 90:
                return "<comment>$coverage</comment>";

            default:
                return "<info>$coverage</info>";
        }
    }
}
