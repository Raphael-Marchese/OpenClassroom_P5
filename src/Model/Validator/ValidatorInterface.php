<?php

namespace App\Model\Validator;

/**
 * @template T
 */
interface ValidatorInterface
{
    /**
     * @param T $data
     * @return void
     */
    public function validate($data): void;

    public function supports($object): bool;

}
