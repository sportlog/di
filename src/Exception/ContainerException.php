<?php

/**
 * DI
 *
 * @license MIT License
 */

declare (strict_types = 1);

namespace YaMVC\DI\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
