<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Rules;

use App\Services\DataForSeoApi\Exceptions\ValidationException;

class KeywordsValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        if (empty($data['keywords'])) {
            $this->fail('Keywords are required.');
        }
    }
}
