<?php

namespace ReaZzon\Editor\Classes\Plugins\LinkAutocomplete\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LinkResource
 * @package ReaZzon\Editor\Classes\Plugins\LinkAutocomplete\Resources
 */
class LinkResource extends JsonResource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}
