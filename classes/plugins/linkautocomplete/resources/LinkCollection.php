<?php

namespace ReaZzon\Editor\Classes\Plugins\LinkAutocomplete\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LinkCollection extends ResourceCollection
{

    public static $wrap = 'items';

    public $additional = [
        'success' => true,
    ];

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
