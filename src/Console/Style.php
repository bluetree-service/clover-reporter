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
     * @param string $section
     * @param string $message
     * @param string $style
     * @throws \InvalidArgumentException
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
    }

    /**
     * @param string|array $messages
     * @param string $style
     * @param bool $large
     * @throws \InvalidArgumentException
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
    }

    /**
     * @param array $message
     * @throws \InvalidArgumentException
     */
    public function errorLine(array $message)
    {
        $this->writeln(
            $this->formatter->formatBlock($message, 'error')
        );
    }

    public function original($input, $output)
    {
        /** @var \Symfony\Component\Console\Helper\FormatterHelper $formatter */

        $errorMessages = array('Error!', 'Something went wrong');
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);




        $io = new SymfonyStyle($input, $output);
        $io->title('Lorem Ipsum Dolor Sit Amet');
        $io->section('Adding a User');
        $io->section('Generating the Password');
        $io->text(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu vitae sem faucibus porta',
        ));
        $io->listing(array(
            'Element #1 Lorem ipsum dolor sit amet',
            'Element #2 Lorem ipsum dolor sit amet',
            'Element #3 Lorem ipsum dolor sit amet',
        ));
        $io->table(
            array('Header 1', 'Header 2'),
            array(
                array('Cell 1-1', 'Cell 1-2'),
                array('Cell 2-1', 'Cell 2-2'),
                array('Cell 3-1', 'Cell 3-2'),
            )
        );
        $io->note(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu vitae sem faucibus porta',
        ));
        $io->caution(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu vitae sem faucibus porta',
        ));
        $io->success(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));
        $io->warning(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));
        $io->error(array(
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ));

        $io->block([
            'test',
            'test1'
        ]);

        $io->comment([
            'test',
            'test1'
        ]);
    }
}
