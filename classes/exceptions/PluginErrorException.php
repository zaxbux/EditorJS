<?php namespace Zaxbux\EditorJS\Classes\Exceptions;

use Winter\Storm\Exception\ApplicationException;

/**
 * Class PluginErrorException
 * @package Zaxbux\EditorJS\Classes\Exceptions
 */
class PluginErrorException extends ApplicationException
{
    protected $code = 406;

    /**
     * @return array
     */
    public function render(): array
    {
        $errorBody = [
            'success' => 0
        ];

        if (!empty($this->getMessage())) {
            $errorBody['message'] = $this->getMessage();
        }

        return $errorBody;
    }
}
