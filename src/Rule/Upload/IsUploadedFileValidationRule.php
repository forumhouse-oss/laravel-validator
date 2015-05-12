<?php namespace FHTeam\LaravelValidator\Rule\Upload;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageRatioValidationRule
 *
 * @package FHTeam\LaravelValidator\Rule\Image
 */
class IsUploadedFileValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        return $value instanceof UploadedFile;
    }
}
