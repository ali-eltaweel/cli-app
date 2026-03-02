<?php

namespace App\CLI;

use CommandParser\{ Command, CommandLineParser, Specs\Command as CommandSpecs };
use Exception;

/**
 * CLI Application.
 * 
 * @api
 * @abstract
 * @since 0.1.0
 * @version 1.4.0
 * @package cli-app
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
abstract class CliApplication {

    /**
     * The last created application instance.
     * 
     * @static
     * @internal
     * @since 1.3.0
     * 
     * @var CliApplication|null $instance
     */
    private static ?CliApplication $instance = null;

    protected readonly Command $commandline;

    /**
     * Creates a new CLI application instance.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Command $commandline The parsed command line arguments.
     */
    public final function __construct() {

        self::$instance = $this;
    }

    public final function setCommandLine(Command $commandline): void {

        $this->commandline = $commandline;
    }

    /**
     * Daemonizes the process.
     * 
     * @final
     * @internal
     * @since 1.1.0
     * @version 1.0.1
     * 
     * @throws Exception
     * @return void
     */
    protected final function daemonize(): void {

        $pid = pcntl_fork();

        if ($pid === -1) {

            throw new Exception('Could not fork');
        }

        if ($pid !== 0) {

            exit(0);
        }

        if (posix_setsid() == -1) {

            throw new Exception("Could not detach from terminal\n");
        }

        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
    }

    /**
     * Sets up the application.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.2.0
     * 
     * @return void
     */
    protected function setup(): void {}
    
    /**
     * Main application loop.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @return void
     */
    protected function loop(): void {

        exit(0);
    }

    /**
     * Retrieves the command specifications for the application.
     * 
     * @internal
     * @abstract
     * @since 1.0.0
     * @version 1.1.0
     * 
     * @return CommandSpecs
     */
    protected abstract function getCommandSpecs(): CommandSpecs;

    /**
     * Main entry point for the CLI application.
     * 
     * @api
     * @final
     * @static
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $appName
     * @param string[] $args
     * @return never
     */
    public static final function main(string $appName, string ...$args): never {

        $commandParser = new CommandLineParser();

        $app = new static();
        $command = $commandParser->parse([ $appName, ...$args ], $app->getCommandSpecs());

        $app->setCommandLine($command);

        $app->setup();
        
        while (true) {
        
            $app->loop();
            usleep(1);
        }
    }

    /**
     * Retrieves the last created CLI application instance.
     * 
     * @api
     * @final
     * @static
     * @since 1.3.0
     * @version 1.0.0
     * 
     * @return CliApplication|null
     */
    public static final function instance(): ?static {

        return self::$instance;
    }
}
