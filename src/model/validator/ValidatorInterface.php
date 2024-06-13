<?php

namespace App\model\validator;

interface ValidatorInterface
{
    public static function validate($data): array;

}
