# CLI APP

**CLI application skeleton for PHP projects**

- [CLI APP](#cli-app)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Defining Application](#defining-application)
    - [Running Application](#running-application)
      - [Run as Daemon](#run-as-daemon)

***

## Installation

Install *cli-app* via Composer:

```bash
composer require ali-eltaweel/cli-app
```

## Usage

### Defining Application

```php
use App\CLI\CliApplication;
use CommandParser\Specs\Command;

class MyApp extends CliApplication {

  protected static function getCommandSpecs(): Command {

    return new Command('my-app');
  }

  protected function setup(): void {

    // this code runs once at the start
  }

  protected function loop(): void {

    // this code runs repeatedly
  }
}
```

### Running Application

```php
MyApp::main(...$argv);
```

#### Run as Daemon

```php
class MyApp extends CliApplication {

  protected function setup(): void {

    $this->daemonize();
  }
}
```
