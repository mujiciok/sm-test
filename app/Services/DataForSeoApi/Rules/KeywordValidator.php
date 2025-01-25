<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Rules;

use App\Services\DataForSeoApi\Exceptions\ValidationException;

class KeywordValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        if (empty($data['keyword'])) {
            $this->fail('Search keyword is required.');
        }

        /**
         * @TODO add validation rules
         * you can specify up to 700 characters in the keyword field
         * all %## will be decoded (plus character ‘+’ will be decoded to a space character)
         * if you need to use the “%” character for your keyword, please specify it as “%25”;
         * if you need to use the “+” character for your keyword, please specify it as “%2B”;
         */
    }
}
