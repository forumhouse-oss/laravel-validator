<?php namespace FHTeam\LaravelValidator\Rule;

use Exception;
use FHTeam\LaravelValidator\ValidationRuleInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Validation rule to check if both height and width of the image are between specified bounds.
 *
 * @package FHTeam\LaravelValidator\Rule
 */
class ImageDimensionValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        if (count($parameters) < 1) {
            throw new Exception("ImageDimensionValidationRule requires at least one parameter");
        }

        $minDimensionSize = $parameters[0];
        $maxDimensionSize = isset($parameters[1]) ? $parameters[1] : null;

        if ((!is_numeric($minDimensionSize)) || ($maxDimensionSize && (!is_numeric($maxDimensionSize)))) {
            throw new Exception("ImageDimensionValidationRule parameters must be numbers");
        }

        if (!$value instanceof UploadedFile) {
            return false; //Not an uploaded file
        }

        $imageDimensions = @getimagesize($value->getRealPath());
        if (!$imageDimensions) {
            return false; //Not an image file
        }

        $width = $imageDimensions[0];
        $height = $imageDimensions[1];

        if (($width < $minDimensionSize) or ($height < $minDimensionSize)) {
            return false;
        }

        if ($maxDimensionSize && (($width > $maxDimensionSize) or ($height > $maxDimensionSize))) {
            return false;
        }

        return true;
    }

    public function replace($message, $attribute, $rule, array $parameters = [])
    {
        $message = str_replace(':min_dimension', $parameters[0], $message);
        $message = str_replace(':max_dimension', isset($parameters[1]) ? $parameters[1] : '∞', $message);

        return $message;
    }
}
