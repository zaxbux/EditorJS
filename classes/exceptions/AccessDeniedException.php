<?php namespace Zaxbux\EditorJS\Classes\Exceptions;


/**
 * Class AccessDeniedException
 * @package Zaxbux\EditorJS\Classes\Exceptions
 */
class AccessDeniedException extends PluginErrorException
{
    protected $code = 403;

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
