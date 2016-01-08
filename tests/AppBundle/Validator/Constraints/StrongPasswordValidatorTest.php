<?php
namespace Tests\AppBundle\Validator\Constraints;

use AppBundle\Validator\Constraints\StrongPassword;
use Symfony\Component\Validator\Validation;

class StrongPasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider passwordInvalidProvider
     */
    public function testPasswordInvalid($password)
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($password, new StrongPassword());
        $this->assertEquals(1, count($errors));
    }

    /**
     * @dataProvider passwordValidProvider
     */
    public function testPasswordValid($password)
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($password, new StrongPassword());
        $this->assertEquals(0, count($errors));
    }

    /**
     * @return array
     */
    public function passwordInvalidProvider()
    {
        return array(
            array('0123456789'),
            array('azerty'),
            array('AZERTY'),
            array('aZeRtY5'),
            array('#888AZE'),
        );
    }

    /**
     * @return array
     */
    public function passwordValidProvider()
    {
        return array(
            array('#AlPhA02468!'),
        );
    }
}