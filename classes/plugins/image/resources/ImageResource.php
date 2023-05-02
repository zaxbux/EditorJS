<?php namespace Zaxbux\EditorJS\Classes\Plugins\Image\Resources;


use Zaxbux\EditorJS\Classes\Plugins\Attaches\Resources\AttachResource;

/**
 * Class ImageResource
 * @package Zaxbux\EditorJS\Classes\Plugins\Image\Resources
 */
class ImageResource extends AttachResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'url' => $this->resource
        ];
    }
}
