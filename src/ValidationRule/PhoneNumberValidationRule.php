<?php namespace FHTeam\LaravelValidator\ValidationRule;

use Exception;
use FHTeam\LaravelValidator\ValidationRuleInterface;
use libphonenumber\PhoneNumberUtil;

/**
 * Class PhoneNumberRule
 * 'phone' => 'phone_number';
 * 'phone' => 'phone_number:RU'
 * 'phone' => 'phone_number:UA'
 *
 * @package FHTeam\LaravelValidator
 */
class PhoneNumberValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        $country = isset($parameters[0]) ? $parameters[0] : "RU";

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse($value, $country);
        } catch (Exception $e) {
            return false;
        }
        if (!$phoneUtil->isValidNumber($phoneNumber)) {
            return false;
        }

        return true;
    }
}
