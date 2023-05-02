<?php namespace ReaZzon\Editor;

use Backend, Event;
use System\Classes\PluginBase;
use ReaZzon\Editor\Console\RefreshStaticPages;
use Illuminate\Contracts\Routing\ResponseFactory;
use ReaZzon\Editor\Classes\Event\ProcessMLFields;
use ReaZzon\Editor\Classes\Exceptions\PluginErrorException;

use ReaZzon\Editor\Behaviors\ConvertToHtml;
use ReaZzon\Editor\Classes\Event\ExtendWinterBlogPlugin;
use ReaZzon\Editor\Classes\Event\ExtendWinterPagesPlugin;

/**
 * Editor Plugin Information File
 * @package ReaZzon\Editor
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
            'name' => 'reazzon.editor::lang.plugin.name',
            'description' => 'reazzon.editor::lang.plugin.description',
            'author' => 'Nick Khaetsky',
            'icon' => 'icon-pencil-square-o',
            'homepage' => 'https://github.com/FlusherDock1/EditorJS'
        ];
    }

    /**
     *
     */
    public function register()
    {
        $this->registerConsoleCommand('editor:refresh.static-pages', RefreshStaticPages::class);
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
            'reazzon.editor.access_settings' => [
                'tab' => 'reazzon.editor::lang.plugin.name',
                'label' => 'reazzon.editor::lang.permission.access_settings'
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
                'label' => 'reazzon.editor::lang.settings.menu_label',
                'description' => 'reazzon.editor::lang.settings.menu_description',
                'category' => 'reazzon.editor::lang.plugin.name',
                'class' => 'ReaZzon\Editor\Models\Settings',
                'permissions' => ['reazzon.editor.access_settings'],
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
            'ReaZzon\Editor\FormWidgets\EditorJS' => 'editorjs',
            'ReaZzon\Editor\FormWidgets\MLEditorJS' => 'mleditorjs',
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
                'view' => 'reazzon.editor::blocks.paragraph'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/header.js',
                ],
                'view' => 'reazzon.editor::blocks.heading'
            ],
            'Marker' => [
                'settings' => [
                    'class' => 'Marker',
                    'shortcut' => 'CMD+SHIFT+M',
                ],
                'scripts' => [
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/marker.js',
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/image.js',
                ],
                'view' => 'reazzon.editor::blocks.image'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/attaches.js',
                ],
                'view' => 'reazzon.editor::blocks.attaches'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/link.js',
                ],
                'view' => 'reazzon.editor::blocks.link'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/list.js',
                ],
                'view' => 'reazzon.editor::blocks.list'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/checklist.js',
                ],
                'view' => 'reazzon.editor::blocks.checklist'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/table.js',
                ],
                'view' => 'reazzon.editor::blocks.table'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/quote.js',
                ],
                'view' => 'reazzon.editor::blocks.quote'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/code.js',
                ],
                'view' => 'reazzon.editor::blocks.code'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/embed.js',
                ],
                'view' => 'reazzon.editor::blocks.embed'
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
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/raw.js',
                ],
                'view' => 'reazzon.editor::blocks.raw'
            ],
            'delimiter' => [
                'settings' => [
                    'class' => 'Delimiter'
                ],
                'scripts' => [
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/delimiter.js',
                ],
                'validation' => [],
                'view' => 'reazzon.editor::blocks.delimiter'
            ],
            'underline' => [
                'settings' => [
                    'class' => 'Underline'
                ],
                'scripts' => [
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/underline.js',
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
