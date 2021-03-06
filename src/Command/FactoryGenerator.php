<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Command\Command;
use Trismegiste\Mondrian\Refactor\FactoryGenBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Parser\PhpDumper;
use Trismegiste\Mondrian\Parser\NullDumper;

/**
 * FactoryGenerator is a refactoring tools which scans all new statements
 * and create a protected method for each.
 * 
 * With this, it is possible to mockup the new instance for unit testing
 */
class FactoryGenerator extends Command
{

    protected $dumper;
    protected $source;

    protected function configure()
    {
        $this->setName('refactor:factory')
                ->addArgument('file', InputArgument::REQUIRED, 'The source file to refactor')
                ->setDescription('Scans a file and replace new instances in methods by protected factories')
                ->addOption('dry', null, InputOption::VALUE_NONE, 'Dry run (no write)');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->source = new \ArrayIterator(
                array(new \Symfony\Component\Finder\SplFileInfo(
                    $input->getArgument('file'), '', '')));

        if ($input->getOption('dry')) {
            $this->dumper = new NullDumper();
        } else {
            $this->dumper = new PhpDumper();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compil = new Linking(
                new Builder(), new FactoryGenBuilder($this->dumper));

        $compil->run($this->source);
    }

}