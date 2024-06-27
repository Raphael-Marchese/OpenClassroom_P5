<?php

namespace App\Model\Validator;

/**
 * @template T
 */
interface ValidatorInterface
{
    /**
     * @param T $data
     * @return array<string, string>
     */
    public function validate($data): array;

}
