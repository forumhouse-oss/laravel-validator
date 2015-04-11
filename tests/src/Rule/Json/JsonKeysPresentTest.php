<?php namespace FHTeam\LaravelValidator\Tests\Rule\Json;

use FHTeam\LaravelValidator\Rule\Json\JsonKeysPresent;
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
    public function setUp()
    {
        parent::setUp();
        $this->rule = $this->app->make(JsonKeysPresent::class);
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
