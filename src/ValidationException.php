<?php

namespace FHTeam\LaravelValidator;

use Exception;
use Illuminate\Contracts\Support\MessageProvider;

/**
 * Class that can show complete list of fields, that failed validation
 *
 * @package FHTeam\LaravelValidator
 */
class ValidationException extends Exception
{
    /**
     * Message provider from which to extract failed validation messages
     *
     * @param string          $message
     * @param MessageProvider $failedProvider
     */
    public function __construct($message, MessageProvider $failedProvider = null)
    {
        if ($failedProvider) {
            $message = $message."\r\nThe following fields have not passed validation:\r\n";
            foreach ($failedProvider->getMessageBag()->all() as $key => $text) {
                $message .= $key.' => '.$text."\r\n";
            }
        }
        parent::__construct($message);
    }
}
