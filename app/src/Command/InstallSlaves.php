<?php

namespace Omgcatz\Command;

use Knp\Command\Command;
use Omgcatz\Includes\Database;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallSlaves extends Command
{
    protected function configure()
    {
        $this
      ->setName('setup:slaves')
      ->setDescription('Install slaves')
      ->addArgument('minions', InputArgument::IS_ARRAY, 'The server names/IPs for minions')
      ->addOption('clear', null, InputOption::VALUE_NONE, 'Clear existing minions from the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Database $db */
    $db = $this->getSilexApplication()['database'];

        $minions = $input->getArgument('minions');
        $clear = $input->getOption('clear');

        if ($clear) {
            $db->simpleQuery('TRUNCATE TABLE minions');
            $output->writeln('Minions Cleared');
        }

        foreach ($minions as $minion) {
            $db->simpleQuery("INSERT INTO minions (`minionRoot`,`load`) VALUES ('{$minion}',0)");
            $output->writeln(sprintf('Adding %s', $minion));
        }
    }
}
