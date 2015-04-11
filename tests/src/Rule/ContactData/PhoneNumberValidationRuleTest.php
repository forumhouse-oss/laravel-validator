<?php namespace FHTeam\LaravelValidator\Tests\src\Rule\ContactData;

use FHTeam\LaravelValidator\Rule\ContactData\PhoneNumberValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use FHTeam\LaravelValidator\Tests\TestBase;

class PhoneNumberValidationRuleTest extends TestBase
{
    /**
     * @var ValidationRuleInterface
     */
    protected $rule;

    /**
     * @var string
     */
    protected $tmpFile;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->rule = $this->app->make(PhoneNumberValidationRule::class);
    }

    public function testDeclaration()
    {
        $this->assertInstanceOf(ValidationRuleInterface::class, $this->rule);
    }

    public function testNumberValid()
    {
        $this->assertTrue($this->rule->validate('phone_number', '+79261234567'));
        $this->assertTrue($this->rule->validate('phone_number', '+7-926-123-45-67'));
        $this->assertTrue($this->rule->validate('phone_number', '89261234567'));
        $this->assertTrue($this->rule->validate('phone_number', '8-926-123-45-67'));
    }

    public function testNumberInvalid()
    {
        $this->assertFalse($this->rule->validate('phone_number', '261234567'));
        $this->assertFalse($this->rule->validate('phone_number', '26-123-45-67'));
        $this->assertFalse($this->rule->validate('phone_number', '261234567'));
        $this->assertFalse($this->rule->validate('phone_number', '26-123-45-67'));
    }
}
