<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Exceptions;

use Exception;

class FixtureMissingException extends Exception
{
    protected $message = 'Fixture file not found';
}
