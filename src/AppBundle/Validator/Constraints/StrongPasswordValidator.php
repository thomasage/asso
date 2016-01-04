<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StrongPasswordValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/[a-z]/', $value, $matches) // lowercase
            || !preg_match('/[A-Z]/', $value, $matches) // uppercase
            || !preg_match('/[0-9]/', $value, $matches) // numbers
            || !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $value, $matches) // special chars
        ) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}