<?php

namespace Omgcatz\Command;

use Knp\Command\Command;
use Omgcatz\Database;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTables extends Command
{
  protected function configure()
  {
    $this
      ->setName('install:slaves')
      ->setDescription('Install slaves');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    /** @var Database $db */
    $db = $this->getSilexApplication()['database'];


    $db->simpleQuery("TRUNCATE TABLE minions");

    // TODO: Minions

//    foreach (Config::$minions as $minion) {
//      $db->simpleQuery("INSERT INTO minions (`minionRoot`,`load`) VALUES ('{$minion}',0)");
//    }


    $output->writeln('Minons Created');
  }
}
