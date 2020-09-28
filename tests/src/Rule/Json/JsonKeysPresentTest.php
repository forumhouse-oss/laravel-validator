<?php namespace FHTeam\LaravelValidator\Tests\Rule\Json;

use FHTeam\LaravelValidator\Rule\Json\JsonKeysPresentValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use FHTeam\LaravelValidator\Tests\TestBase;

class JsonKeysPresentTest extends TestBase
{
    /**
     * @var ValidationRuleInterface
     */
    protected $rule;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(JsonKeysPresentValidationRule::class);
    }

    public function testDeclaration()
    {
        $this->assertInstanceOf(ValidationRuleInterface::class, $this->rule);
    }

    public function testKeysPresent()
    {
        $this->assertTrue($this->rule->validate('json', '{"valid": "json"}', ['valid']));
    }

    public function testKeysNotPresent()
    {
        $this->assertFalse($this->rule->validate('json', '{"valid": "json"}', ['invalid']));
    }
}
