<?php

/**
 * DI
 *
 * @license MIT License
 */

declare (strict_types = 1);

namespace Sportlog\DI\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
