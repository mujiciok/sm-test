<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Rules;

use App\Services\DataForSeoApi\Exceptions\ValidationException;
use Illuminate\Support\Facades\Log;

abstract class Validator
{
    abstract public function validate(array $data): void;

    /**
     * @throws ValidationException
     */
    protected function fail(string $message): never
    {
        Log::channel('dfs')->alert($message);
        throw new ValidationException($message);
    }
}
