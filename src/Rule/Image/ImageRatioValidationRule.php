<?php namespace FHTeam\LaravelValidator\Rule\Image;

use Exception;
use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageRatioValidationRule
 *
 * @package FHTeam\LaravelValidator\Rule\Image
 */
class ImageRatioValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        if (count($parameters) < 2) {
            throw new Exception("ImageRatioRule requires two parameters to be passed: min and max ratio");
        }

        if ((!is_numeric($parameters[0])) || (!is_numeric($parameters[1]))) {
            throw new Exception("ImageRatioRule parameters must be numbers");
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
        $ratio = $width / $height;

        $includeBounds = false;
        if (!empty($parameters[2])) {
            $includeBounds = (bool)$parameters[2];
        }

        $result = ($includeBounds
            ? (($ratio >= $parameters[0]) && ($ratio <= $parameters[1]))
            : (($ratio > $parameters[0]) && ($ratio < $parameters[1])));

        return $result;
    }

    public function replace($message, $attribute, $rule, array $parameters = [])
    {
        $message = str_replace(':min_ratio', $parameters[0], $message);
        $message = str_replace(':max_ratio', $parameters[1], $message);

        return $message;
    }
}
