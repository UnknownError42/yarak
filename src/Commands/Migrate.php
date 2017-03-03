<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\Migrations\Migrator;
use Yarak\DB\ConnectionResolver;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate')
         ->setDescription('Run the database migrations.')
         ->setHelp('This command allows you to run migrations.')
         ->addOption(
            'rollback',
            null,
            InputOption::VALUE_OPTIONAL,
            'Rollback migrations by given number of steps.'
        )
        ->addOption(
            'reset',
            null,
            InputOption::VALUE_NONE,
            'Rollback all migrations.'
        )
        ->addOption(
            'refresh',
            null,
            InputOption::VALUE_NONE,
            'Rollback and re-run all migrations.'
        );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rollbackSteps = $this->getSteps($input);

        $migrator = $this->getMigrator();

        if ($rollbackSteps !== null) {
            $migrator->rollback($rollbackSteps);
        } elseif ($input->getOption('reset')) {
            $migrator->reset();
        } elseif ($input->getOption('refresh')) {
            $migrator->refresh();
        } else {
            $migrator->run();
        }

        foreach ($migrator->getLog() as $message) {
            $output->writeln($message);
        }
    }

    /**
     * Get number of steps to rollback.
     *
     * @param InputInterface $input
     *
     * @return int|null
     */
    protected function getSteps(InputInterface $input)
    {
        $rollbackSteps = $input->getOption('rollback');

        if ($rollbackSteps === true) {
            return 1;
        }

        return $rollbackSteps;
    }

    /**
     * Get an instance of the migrator.
     *
     * @return Migrator
     */
    protected function getMigrator()
    {
        $config = Config::getInstance($this->configArray);

        return new Migrator(
            $config,
            new ConnectionResolver(),
            $this->getRepository($config)
        );
    }

    /**
     * Get an instance of MigrationRepository.
     *
     * @param Config $config
     *
     * @return Yarak\Migrations\MigrationRepository
     */
    protected function getRepository(Config $config)
    {
        $repositoryType = ucfirst($config->get(['yarak', 'migrationRepository']));

        $repositoryClass = 'Yarak\\Migrations\\'.$repositoryType.'MigrationRepository';

        return new $repositoryClass();
    }
}