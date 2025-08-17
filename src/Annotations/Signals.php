<?php

namespace App\CLI\Annotations;

use Attraction\Annotation;
use Signals\Signal;

use Attribute;

/**
 * Application Signals.
 * 
 * @api
 * @final
 * @since 0.3.0
 * @version 1.0.0
 * @package cli-app
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Signals extends Annotation {

    /**
     * The signals handled by the application.
     * 
     * @api
     * @since 1.0.0
     * @var array<Signal> $signals
     */
    public readonly array $signals;

    /**
     * Creates a new Signals annotation instance.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Signal ...$signals
     */
    public final function __construct(Signal $signal, Signal ...$signals) {

        $this->signals = [ $signal, ...$signals ];
    }
}
