<?php

namespace App\CLI;

use CommandParser\{ Command, CommandLineParser, Specs\Command as CommandSpecs };

use Signals\Signal;

use Exception, ReflectionClass;

/**
 * CLI Application.
 * 
 * @api
 * @abstract
 * @since 0.1.0
 * @version 1.2.0
 * @package cli-app
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
abstract class CliApplication {

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
    public final function __construct(protected readonly Command $commandline) {}

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
     * @version 1.1.0
     * 
     * @return void
     */
    protected function setup(): void {

        $classReflection = new ReflectionClass($this);

        foreach (Annotations\Signals::annotatedOn($classReflection->getMethod('signalHandler'))?->signals ?? [] as $signal) {

            /** @var Signal $signal */
            $signal->handle(fn (Signal $signal, array $siginfo) => $this->signalHandler($signal, $siginfo));
        }
    }
    
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
     * Handles signals received by the application.
     * 
     * @internal
     * @since 1.2.0
     * @version 1.0.0
     * 
     * @param Signal $signal  The signal instance.
     * @param array $signinfo The signal information.
     * @return void
     */
    protected function signalHandler(Signal $signal, array $signinfo) {}

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
