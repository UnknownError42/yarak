<?php

namespace Yarak;

use Yarak\Config\Config;
use Yarak\Exceptions\InvalidInput;
use Yarak\Helpers\NamespaceResolver;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class Kernel
{
    /**
     * Array of registered commands.
     *
     * @var array
     */
    protected $commands;

    /**
     * Construct.
     *
     * @param array|string $userConfig
     * @param bool         $merge
     */
    public function __construct($userConfig = null, $merge = true)
    {
        Config::getInstance()->setConfig($userConfig, $merge);
    }

    /**
     * Handle an incoming console command.
     */
    public function handle($input = null, $output = null)
    {
        $application = new Application('Yarak - Phalcon devtools');

        $this->registerCommands($application);

        if ($input && $output) {
            $this->validateCommand($application, $input);

            $application->setAutoExit(false);

            return $application->run($input, $output);
        }

        $application->run();
    }

    /**
     * Register all Yarak commands.
     *
     * @param Application $application
     */
    protected function registerCommands(Application $application)
    {
        $this->getApplicationCommands();

        $this->getUserCommands();

        foreach ($this->commands as $command) {
            $application->add(new $command());
        }
    }

    /**
     * Get array of all Yarak commands.
     */
    protected function getApplicationCommands()
    {
        $directory = new \DirectoryIterator(__DIR__.'/Commands');

        foreach ($directory as $file) {
            if (!$file->isDot()) {
                $className = str_replace('.php', '', $file->getFilename());

                $this->commands[] = 'Yarak\\Commands\\'.$className;
            }
        }
    }

    /**
     * Get array of all user defined commands.
     */
    protected function getUserCommands()
    {
        $path = Config::getInstance()->getConsoleDirectory('Kernel.php');

        if (file_exists($path)) {
            $kernelClassName = NamespaceResolver::resolve('console', 'Kernel');

            $kernel = new $kernelClassName();

            $this->commands = array_merge($this->commands, $kernel->getCommands());
        }
    }

    /**
     * Validate the given command.
     *
     * @param Application    $application
     * @param InputInterface $input
     *
     * @throws InvalidInput
     */
    protected function validateCommand(Application $application, InputInterface $input)
    {
        $command = $input->getFirstArgument();

        if ($application->has($command) === false) {
            throw InvalidInput::invalidCommand($command);
        }
    }
}
