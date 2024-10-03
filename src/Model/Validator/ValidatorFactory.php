<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Exception\BlogPostException;
use App\Exception\CommentException;
use App\Exception\ImageException;
use App\Exception\UserException;

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

    /**
     * @throws ImageException | UserException | BlogPostException | CommentException
     */
    public static function validate(object $object): void
    {
        foreach (self::getValidators() as $validator) {
            if ($validator->supports($object)) {
                $validator->validate($object);
            }
        }
    }
}