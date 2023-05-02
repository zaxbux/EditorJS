<?php

namespace ReaZzon\Editor\Classes\Plugins\LinkAutocomplete;

use Event;
use Lang;
use Illuminate\Http\Request;
use ReaZzon\Editor\Classes\Exceptions\PluginErrorException;
use ReaZzon\Editor\Classes\Plugins\LinkAutocomplete\Resources\LinkCollection;
use Winter\Storm\Support\Arr;
use Winter\Storm\Support\Str;

/**
 * LinkAutocomplete Plugin
 * @package ReaZzon\Editor\Classes\Plugins\LinkAutocomplete
 * @author Zach Schneider <hello@zacharyschneider.ca>
 */
class Plugin
{
    /**
     * LinkAutocomplete constructor
     */
    public function __invoke(Request $request)
    {
        $q = $request->get('q');
        if (empty($q)) {
            throw new PluginErrorException;
        }

        return new LinkCollection(collect($this->getFilteredPageLinks($q)));
    }

    protected function getFilteredPageLinks(string $q) {
        return Arr::where($this->getPageLinksArray(), function ($item) use ($q) {
            return Str::contains($item['name'], $q, true) || Str::contains($item['href'], $q, true);
        });
    }

    /**
     * Returns a list of registered page link types.
     * This is reserved functionality for separating the links by type.
     *
     * @see \Backend\FormWidgets\RichEditor::getPageLinkTypes()
     *
     * @return array Returns an array of registered page link types
     */
    protected function getPageLinkTypes()
    {
        $result = [];

        $apiResult = Event::fire('backend.richeditor.listTypes');
        if (is_array($apiResult)) {
            foreach ($apiResult as $typeList) {
                if (!is_array($typeList)) {
                    continue;
                }

                foreach ($typeList as $typeCode => $typeName) {
                    $result[$typeCode] = $typeName;
                }
            }
        }

        return $result;
    }

    /**
     * @see \Backend\FormWidgets\RichEditor::getPageLinks()
     */
    protected function getPageLinks($type)
    {
        $result = [];

        $apiResult = Event::fire('backend.richeditor.getTypeInfo', [$type]);
        if (is_array($apiResult)) {

            foreach ($apiResult as $typeInfo) {
                if (!is_array($typeInfo)) {
                    continue;
                }

                foreach ($typeInfo as $name => $value) {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns a single collection of available page links.
     *
     * @see \Backend\FormWidgets\RichEditor::getPageLinksArray()
     *
     * @return array
     */
    protected function getPageLinksArray()
    {
        $links = [];
        $types = $this->getPageLinkTypes();

        $iterator = function ($links, $typeCode, $typeName, $parent = null) use (&$iterator) {
            $result = [];

            foreach ($links as $linkUrl => $link) {
                /*
                 * Remove scheme and host from URL
                 */
                $baseUrl = \Request::getSchemeAndHttpHost();
                if (strpos($linkUrl, $baseUrl) === 0) {
                    $linkUrl = substr($linkUrl, strlen($baseUrl));
                }

                /*
                 * Root page fallback.
                 */
                if (strlen($linkUrl) === 0) {
                    $linkUrl = '/';
                }

                $linkName = empty($parent) ? '' : $parent . ' / ';
                $linkName .= is_array($link) ? array_get($link, 'title', '') : $link;
                $result[] = [
                    'name' => $linkName,
                    'href' => $linkUrl,
                    'description' => Lang::get($typeName),
                    'type' => $typeCode,
                ];

                if (is_array($link)) {
                    $result = array_merge(
                        $result,
                        $iterator(array_get($link, 'links', []), $typeCode, $typeName, $linkName)
                    );
                }
            }

            return $result;
        };

        foreach ($types as $typeCode => $typeName) {
            $links = array_merge($links, $iterator($this->getPageLinks($typeCode), $typeCode, $typeName));
        }

        return $links;
    }
}
