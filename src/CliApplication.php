<?php

namespace App\CLI;

use CommandParser\{ Command, CommandLineParser, Specs\Command as CommandSpecs };

/**
 * CLI Application.
 * 
 * @api
 * @abstract
 * @since 0.1.0
 * @version 1.3.0
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
    public final function __construct(protected readonly Command $commandline) {

        self::$instance = $this;
    }

    /**
     * Daemonizes the process.
     * 
     * @final
     * @internal
     * @since 1.1.0
     * @version 1.0.0
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

        $command = $commandParser->parse([ $appName, ...$args ], static::getCommandSpecs());

        $app = new static($command);

        $app->setup();
        
        while (true) {
        
            $app->loop();
            usleep(1);
        }
    }

    /**
     * Retrieves the last created cli apllication instance.
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

    /**
     * Retrieves the command specifications for the application.
     * 
     * @static
     * @internal
     * @abstract
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @return CommandSpecs
     */
    protected static abstract function getCommandSpecs(): CommandSpecs;
}
