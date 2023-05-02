<?php namespace Zaxbux\EditorJS;

use Backend, Event;
use System\Classes\PluginBase;
use Zaxbux\EditorJS\Console\RefreshStaticPages;
use Illuminate\Contracts\Routing\ResponseFactory;
use Zaxbux\EditorJS\Classes\Event\ProcessMLFields;
use Zaxbux\EditorJS\Classes\Exceptions\PluginErrorException;

use Zaxbux\EditorJS\Behaviors\ConvertToHtml;
use Zaxbux\EditorJS\Classes\Event\ExtendWinterBlogPlugin;
use Zaxbux\EditorJS\Classes\Event\ExtendWinterPagesPlugin;

/**
 * Editor Plugin Information File
 * @package Zaxbux\EditorJS
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'zaxbux.editorjs::lang.plugin.name',
            'description' => 'zaxbux.editorjs::lang.plugin.description',
            'author' => 'Zach Schneider',
            'icon' => 'icon-pencil-square-o',
            'homepage' => 'https://github.com/zaxbux/wn-editorjs-plugin'
        ];
    }

    /**
     *
     */
    public function register()
    {
        $this->registerConsoleCommand('editorjs:refresh.static-pages', RefreshStaticPages::class);
        $this->registerErrorHandler();
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array|void
     */
    public function boot()
    {
        Event::subscribe(ExtendWinterBlogPlugin::class);
        Event::subscribe(ExtendWinterPagesPlugin::class);
        Event::subscribe(ProcessMLFields::class);
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'zaxbux.editorjs.access_settings' => [
                'tab' => 'zaxbux.editorjs::lang.plugin.name',
                'label' => 'zaxbux.editorjs::lang.permission.access_settings'
            ],
        ];
    }

    /**
     * Registers settings for this plugin
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'zaxbux.editorjs::lang.settings.menu_label',
                'description' => 'zaxbux.editorjs::lang.settings.menu_description',
                'category' => 'zaxbux.editorjs::lang.plugin.name',
                'class' => \Zaxbux\EditorJS\Models\Settings::class,
                'permissions' => ['zaxbux.editorjs.access_settings'],
                'icon' => 'icon-cog',
                'order' => 500,
            ]
        ];
    }

    /**
     * Registers formWidgets.
     *
     * @return array
     */
    public function registerFormWidgets()
    {
        return [
            \Zaxbux\EditorJS\FormWidgets\EditorJS::class => 'editorjs',
            \Zaxbux\EditorJS\FormWidgets\MLEditorJS::class => 'mleditorjs',
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'editorjs' => [$this, 'convertJsonToHtml'],
                'convertBytes' => [$this, 'convertBytes'],
            ],
        ];
    }

    public function convertJsonToHtml($field)
    {
        return (new ConvertToHtml)->convertJsonToHtml($field);
    }

    /**
     * Converts bytes to more sensible string
     *
     * @param int $bytes
     * @return string
     * @see \File::sizeToString($bytes);
     */
    public function convertBytes($bytes)
    {
        return \File::sizeToString($bytes);
    }

    /**
     * Registers additional blocks for EditorJS
     * @return array
     */
    public function registerEditorBlocks()
    {
        return [
            'paragraph' => [
                'validation' => [
                    'text' => [
                        'type' => 'string',
                        'allowedTags' => 'i,b,u,a[href],span[class],code[class],mark[class]'
                    ]
                ],
                'view' => 'zaxbux.editorjs::blocks.paragraph'
            ],
            'header' => [
                'settings' => [
                    'class' => 'Header',
                    'shortcut' => 'CMD+SHIFT+H',
                ],
                'validation' => [
                    'text' => [
                        'type' => 'string',
                    ],
                    'level' => [
                        'type' => 'int',
                        'canBeOnly' => [1, 2, 3, 4, 5]
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/header.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.heading'
            ],
            'Marker' => [
                'settings' => [
                    'class' => 'Marker',
                    'shortcut' => 'CMD+SHIFT+M',
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/marker.js',
                ]
            ],
            'image' => [
                'settings' => [
                    'class' => 'ImageTool',
                    'config' => [
                        'endpoints' => [
                            'byFile' => config('app.url') . '/editorjs/plugins/image/uploadFile',
                            'byUrl' => config('app.url') . '/editorjs/plugins/image/fetchUrl',
                        ]
                    ]
                ],
                'validation' => [
                    'file' => [
                        'type' => 'array',
                        'data' => [
                            'url' => [
                                'type' => 'string',
                            ],
                            'thumbnails' => [
                                'type' => 'array',
                                'required' => false,
                                'data' => [
                                    '-' => [
                                        'type' => 'string',
                                    ]
                                ],
                            ]
                        ],
                    ],
                    'caption' => [
                        'type' => 'string'
                    ],
                    'withBorder' => [
                        'type' => 'boolean'
                    ],
                    'withBackground' => [
                        'type' => 'boolean'
                    ],
                    'stretched' => [
                        'type' => 'boolean'
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/image.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.image'
            ],
            'attaches' => [
                'settings' => [
                    'class' => 'AttachesTool',
                    'config' => [
                        'endpoint' => config('app.url') . '/editorjs/plugins/attaches',
                    ]
                ],
                'validation' => [
                    'file' => [
                        'type' => 'array',
                        'data' => [
                            'url' => [
                                'type' => 'string',
                            ],
                            'size' => [
                                'type' => 'int',
                            ],
                            'name' => [
                                'type' => 'string',
                            ],
                            'extension' => [
                                'type' => 'string',
                            ],
                        ]
                    ],
                    'title' => [
                        'type' => 'string',
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/attaches.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.attaches'
            ],
            'linkTool' => [
                'settings' => [
                    'class' => 'LinkTool',
                    'config' => [
                        'endpoint' => '/editorjs/plugins/linktool',
                    ]
                ],
                'validation' => [
                    'link' => [
                        'type' => 'string'
                    ],
                    'meta' => [
                        'type' => 'array',
                        'data' => [
                            'title' => [
                                'type' => 'string',
                            ],
                            'description' => [
                                'type' => 'string',
                            ],
                            'image' => [
                                'type' => 'array',
                                'data' => [
                                    'url' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/link.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.link'
            ],
            'list' => [
                'settings' => [
                    'class' => 'List',
                    'inlineToolbar' => true,
                ],
                'validation' => [
                    'style' => [
                        'type' => 'string',
                        'canBeOnly' =>
                            [
                                0 => 'ordered',
                                1 => 'unordered',
                            ],
                    ],
                    'items' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'string',
                                'allowedTags' => 'i,b,u',
                            ],
                        ],
                    ],
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/list.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.list'
            ],
            'checklist' => [
                'settings' => [
                    'class' => 'Checklist',
                    'inlineToolbar' => true,
                ],
                'validation' => [
                    'items' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'array',
                                'data' => [
                                    'text' => [
                                        'type' => 'string',
                                        'required' => false
                                    ],
                                    'checked' => [
                                        'type' => 'boolean',
                                        'required' => false
                                    ],
                                ],

                            ],
                        ],
                    ],
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/checklist.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.checklist'
            ],
            'table' => [
                'settings' => [
                    'class' => 'Table',
                    'inlineToolbar' => true,
                    'config' => [
                        'rows' => 2,
                        'cols' => 3,
                    ],
                ],
                'validation' => [
                    'content' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'array',
                                'data' => [
                                    '-' => [
                                        'type' => 'string',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/table.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.table'
            ],
            'quote' => [
                'settings' => [
                    'class' => 'Quote',
                    'inlineToolbar' => true,
                    'shortcut' => 'CMD+SHIFT+O',
                    'config' => [
                        'quotePlaceholder' => 'Enter a quote',
                        'captionPlaceholder' => 'Quote\'s author',
                    ],
                ],
                'validation' => [
                    'text' => [
                        'type' => 'string',
                    ],
                    'alignment' => [
                        'type' => 'string',
                    ],
                    'caption' => [
                        'type' => 'string',
                    ],
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/quote.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.quote'
            ],
            'code' => [
                'settings' => [
                    'class' => 'CodeTool',
                ],
                'validation' => [
                    'code' => [
                        'type' => 'string'
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/code.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.code'
            ],
            'embed' => [
                'settings' => [
                    'class' => 'Embed',
                ],
                'validation' => [
                    'service' => [
                        'type' => 'string'
                    ],
                    'source' => [
                        'type' => 'string'
                    ],
                    'embed' => [
                        'type' => 'string'
                    ],
                    'width' => [
                        'type' => 'int'
                    ],
                    'height' => [
                        'type' => 'int'
                    ],
                    'caption' => [
                        'type' => 'string',
                        'required' => false,
                    ],
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/embed.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.embed'
            ],
            'raw' => [
                'settings' => [
                    'class' => 'RawTool'
                ],
                'validation' => [
                    'html' => [
                        'type' => 'string',
                        'allowedTags' => '*',
                    ]
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/raw.js',
                ],
                'view' => 'zaxbux.editorjs::blocks.raw'
            ],
            'delimiter' => [
                'settings' => [
                    'class' => 'Delimiter'
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/delimiter.js',
                ],
                'validation' => [],
                'view' => 'zaxbux.editorjs::blocks.delimiter'
            ],
            'underline' => [
                'settings' => [
                    'class' => 'Underline'
                ],
                'scripts' => [
                    '$/zaxbux/editorjs/formwidgets/editorjs/assets/js/tools/underline.js',
                ]
            ]
        ];
    }

    public function registerEditorTunes()
    {
        return [];
    }

    public function registerEditorInlineToolbar()
    {
        return [];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function registerErrorHandler(): void
    {
        \App::error(function (PluginErrorException $exception) {
            return app(ResponseFactory::class)->make(
                $exception->render(),
                $exception->getCode()
            );
        });
    }
}
