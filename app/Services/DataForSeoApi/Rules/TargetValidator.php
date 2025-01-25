<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Rules;

use App\Services\DataForSeoApi\Exceptions\ValidationException;

class TargetValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        if (empty($data['target'])) {
            $this->fail('Target is required.');
        }
        /**
         * @TODO add validation rules
         * a domain or a subdomain should be specified without https:// and www.
         */
    }
}
