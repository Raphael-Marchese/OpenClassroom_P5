<?php

namespace App\model;

interface ValidatorInterface
{
    public static function validate($data): array;

}