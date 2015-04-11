<?php namespace FHTeam\LaravelValidator\Tests\Rule\Image;

use Exception;
use FHTeam\LaravelValidator\Rule\Image\ImageDimensionValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use FHTeam\LaravelValidator\Tests\TestBase;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageDimensionValidationRuleTest
 *
 * @package FHTeam\LaravelValidator\Test\Rule\Image
 */
class ImageDimensionValidationRuleTest extends TestBase
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
        $this->rule = $this->app->make(ImageDimensionValidationRule::class);
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'lv-test');
    }

    public function tearDown()
    {
        unlink($this->tmpFile);
    }

    public function testDeclaration()
    {
        $this->assertInstanceOf(ValidationRuleInterface::class, $this->rule);
    }

    public function testNoParameters()
    {
        $this->setExpectedException(Exception::class);
        $this->rule->validate('image', new UploadedFile('', ''), []);
    }

    public function testParametersNotNumbers()
    {
        $this->setExpectedException(Exception::class);
        $this->rule->validate('image', new UploadedFile('', ''), ['aa']);
    }

    public function testValueIsNotUploadedFile()
    {
        $this->assertFalse($this->rule->validate('image', new stdClass(), [1]));
    }

    public function testValueIsBadImageFile()
    {
        $this->setExpectedException(Exception::class);
        $this->assertFalse($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), ['aa']));
    }

    public function testValueOk()
    {
        $this->createImage(110, 110);
        $this->assertTrue($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), [5]));
        $this->assertTrue($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), [5, 110]));
    }

    public function testValueFail()
    {
        $this->createImage(110, 110);
        $this->assertFalse($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), [200]));
        $this->assertFalse($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), [200, 100]));
        $this->assertFalse($this->rule->validate('image', new UploadedFile($this->tmpFile, 'test'), [200, 300]));
    }

    protected function createImage($width, $height)
    {
        $im = imagecreate($width, $height);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5, "Простая Текстовая Строка", $text_color);
        imagepng($im, $this->tmpFile);
    }
}
