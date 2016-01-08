<?php
namespace Tests\AppBundle\Validator\Constraints;

use AppBundle\Validator\Constraints\StrongPassword;
use Symfony\Component\Validator\Validation;

class StrongPasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAlphaUpperOnlyInvalid()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $errors = $validator->validate($this->getPasswordAlphaUpper(), new StrongPassword());

        $this->assertEquals(1, count($errors));
    }

    /**
     * @param int $length
     * @return string
     */
    private function getPasswordAlphaUpper($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $characters_length)];
        }

        return $string;
    }

    public function testAlphaLowerOnlyInvalid()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $errors = $validator->validate($this->getPasswordAlphaLower(), new StrongPassword());

        $this->assertEquals(1, count($errors));
    }

    /**
     * @param int $length
     * @return string
     */
    private function getPasswordAlphaLower($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $characters_length = strlen($characters) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $characters_length)];
        }

        return $string;
    }

    public function testDigitOnlyInvalid()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $errors = $validator->validate($this->getPasswordDigit(), new StrongPassword());

        $this->assertEquals(1, count($errors));
    }

    /**
     * @param int $length
     * @return string
     */
    private function getPasswordDigit($length = 8)
    {
        $characters = '0123456789';
        $characters_length = strlen($characters) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $characters_length)];
        }

        return $string;
    }

    public function testMixedValid()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $errors = $validator->validate('#AlPhA02468!', new StrongPassword());

        $this->assertEquals(0, count($errors));
    }
}