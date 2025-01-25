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

        /**
         * @TODO add validation rules
         * The maximum number of keywords you can specify: 1000
         * The maximum number of characters for each keyword: 80
         * The maximum number of words for each keyword phrase: 10
         */
    }
}
