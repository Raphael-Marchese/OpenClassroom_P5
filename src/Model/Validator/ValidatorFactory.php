<?php
declare(strict_types=1);

namespace App\Model\Validator;

class ValidatorFactory
{

    /**    @return ValidatorInterface[] */
    public static function getValidators(): array
    {
        return [
            new ImageValidator(),
            new UserValidator(),
            new PostValidator(),
            new CommentValidator(),
        ];
    }

    public static function validate(object $object): void
    {
        foreach (self::getValidators() as $validator) {
            if ($validator->supports($object)) {
                $validator->validate($object);
            }
        }
    }
}