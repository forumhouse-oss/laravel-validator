<?php namespace FHTeam\LaravelValidator\Tests\Rule\Json;

use FHTeam\LaravelValidator\Rule\Json\JsonValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use FHTeam\LaravelValidator\Tests\TestBase;

/**
 * Class JsonValidationRuleTest
 *
 * @package FHTeam\LaravelValidator\Test\Rule\Json
 */
class JsonValidationRuleTest extends TestBase
{
    /**
     * @var ValidationRuleInterface
     */
    protected $rule;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->rule = $this->app->make(JsonValidationRule::class);
    }

    public function testDeclaration()
    {
        $this->assertInstanceOf(ValidationRuleInterface::class, $this->rule);
    }

    public function testJsonValid()
    {
        $this->assertTrue($this->rule->validate('json', '{"valid": "json"}'));
    }

    public function testJsonInvalid()
    {
        $this->assertFalse($this->rule->validate('json', 'invalid: "json"}'));
    }
}
