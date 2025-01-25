<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Exceptions;

use Exception;

class FailedApiResponseException extends Exception
{
    protected $message = 'API response failed';
}
