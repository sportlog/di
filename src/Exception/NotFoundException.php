<?php

/**
 * DI
 *
 * @license MIT License
 */

declare (strict_types = 1);

namespace Sportlog\DI\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
