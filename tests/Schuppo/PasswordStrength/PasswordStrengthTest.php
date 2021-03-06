<?php namespace Schuppo\PasswordStrength;

use \Symfony\Component\Translation\Translator;
use  \Illuminate\Validation\Factory;
/**
 * PasswordStrengthTest
 *
 * @group validators
 */
class PasswordStrengthTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->factory = new Factory(new Translator('en'));
        $pS = new PasswordStrength();
        $this->factory->extend('case_diff', function($attribute, $value, $parameters) use ($pS){
            return $pS->validateCaseDiff($attribute, $value, $parameters);
        });
        $this->factory->extend('numbers', function($attribute, $value, $parameters) use ($pS){
            return $pS->validateNumbers($attribute, $value, $parameters);
        });
        $this->factory->extend('letters', function($attribute, $value, $parameters) use ($pS){
            return $pS->validateLetters($attribute, $value, $parameters);
        });
    }

    public function test_case_diff_fails_just_lowercase()
    {
        $this->validation = $this->factory->make(
            array( 'password' => 'tt' ),
            array( 'password' => 'case_diff' )
        );
        $this->assertFalse($this->validation->passes());
    }

    public function test_case_diff_fails_just_uppercase()
    {
       $this->validation = $this->factory->make(
            array( 'password' => 'TT' ),
            array( 'password' => 'case_diff')
        );
        $this->assertFalse($this->validation->passes());
    }

    public function test_case_diff_succeeds()
    {
        $this->validation = $this->factory->make(
            array( 'password' => 'Tt' ),
            array( 'password' => 'case_diff')
        );


        $this->assertTrue($this->validation->passes());
    }

    public function test_numbers_fails()
    {
        $this->validation = $this->factory->make(
            array( 'password' => 'T' ),
            array( 'password' => 'numbers' )
        );

        $this->assertFalse($this->validation->passes());
    }

    public function test_numbers_succeeds()
    {
        $this->validation = $this->factory->make(
            array( 'password' => '1' ),
            array( 'password' => 'numbers' )
        );

        $this->assertTrue($this->validation->passes());
    }

    public function test_letters_fails()
    {
        $this->validation = $this->factory->make(
            array( 'password' => '1' ),
            array( 'password' => 'letters' )
        );

        $this->assertFalse($this->validation->passes());
    }

    public function test_letters_succeeds()
    {
        $this->validation = $this->factory->make(
            array( 'password' => 'T' ),
            array( 'password' => 'letters')
        );

        $this->assertTrue($this->validation->passes());
    }

    public function test_custom_validation_errors_are_not_overwritten()
    {
        $this->validation = $this->factory->make(
            array( 'password' => '' ),
            array( 'password' => 'required' ),
            array( 'required' => 'Should not be overwritten.' )
        );

        $errorArray = $this->validation->errors()->get('password');
        $this->assertEquals('Should not be overwritten.', $errorArray[0]);
    }
}
