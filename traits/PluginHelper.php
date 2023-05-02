<?php namespace Zaxbux\EditorJS\Traits;

use Response;
use Zaxbux\EditorJS\Models\Settings;

/**
 * PluginHelper
 *
 * @package Zaxbux\EditorJS\Traits;
 * @author Nick Khaetsky, nick@reazzon.ru
 * @deprecated
 */
trait PluginHelper
{
    /**
     * @return boolean|void
     */
    public function checkRequest()
    {
        if (
            !Settings::get('disable_secure_endpoints', false)
            && (empty(request()->header('referer'))
                && !strpos(request()->header('referer'), \Config::get('cms.backendUri')))) {
            return true;
        }
    }

    /**
     * @param $key
     * @param $body
     * @return Response
     */
    public function success($key, $body)
    {
        return Response::make(array_add(['success' => 1], $key, $body), 200);
    }

    /**
     * @return Response
     */
    public function error()
    {
        return Response::make(['success' => 0], 406);
    }
}
