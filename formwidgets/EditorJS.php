<?php

namespace ReaZzon\Editor\FormWidgets;

use Winter\Storm\Support\Facades\Event;
use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\Lang;
use System\Classes\PluginManager;
use Backend\Classes\FormWidgetBase;
use Winter\Storm\Support\Arr;

/**
 * EditorJS Form Widget
 * @package ReaZzon\Editor\FormWidgets
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class EditorJS extends FormWidgetBase
{
    //use \Backend\Traits\UploadableWidget;
    use \ReaZzon\Editor\Traits\ToolUploadableHandler;
    //use \ReaZzon\Editor\Traits\ImageToolUploadable;

    const EVENT_CONFIG_BUILT = 'reazzon.editorjs.config.built';

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'editorjs';

    /**
     * @var string|null Path in the Media Library where uploaded files should be stored. If null it will be pulled from Request::input('path');
     * @see \Backend\Traits\UploadableWidget
     */
    public $uploadPath = '/uploaded-files';

    /**
     * @var bool {@see https://editorjs.io/configuration/#read-only-mode Enable read-only mode}.
     */
    public $readOnly = false;

    /**
     * @var bool If true, the editor is set to read-only mode.
     */
    public $disabled = false;

    /**
     * @var string {@see https://editorjs.io/configuration/#placeholder First block placeholder}
     */
    public $placeholder = 'reazzon.editor::lang.formwidget.placeholder';

    /**
     * @var bool {@see https://editorjs.io/configuration/#autofocus If true, set caret at the first Block after Editor is ready.}
     */
    public $autofocus = false;

    /**
     * @var string {@see https://editorjs.io/configuration/#change-the-default-block This Tool will be used as default.}
     */
    public $defaultBlock = null;

    public $tools = null;

    public $i18n = null;

    public $inlineToolbar = null;

    public $tunes = null;

    public $settings = [];

    public $blockSettings = [];

    public $tuneSettings = [];

    public $inlineToolbarSettings = [];

    public $blocksScripts = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->fillFromConfig([
            //'settings',
            'readOnly',
            'disabled',
            'placeholder',
            'autofocus',
            'defaultBlock',
            'tools',
            'i18n',
            'inlineToolbar',
            'tunes',
        ]);
        $this->prepareVars();
        return $this->makePartial('editorjs');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['disabled'] = $this->disabled;
        $this->vars['settings'] = [
            'readOnly' => $this->readOnly || $this->disabled,
            'placeholder' => Lang::get($this->placeholder),
            'autofocus' => $this->autofocus,
            'defaultBlock' => $this->defaultBlock,
            'i18n' => $this->i18n,
            'inlineToolbar' => $this->inlineToolbar,
            'tunes' => $this->tunes,
        ];
        $this->vars['blockSettings'] = $this->blockSettings;
        $this->vars['tuneSettings'] = $this->tuneSettings;
        $this->vars['inlineToolbarSettings'] = $this->inlineToolbarSettings;
        $this->vars['useMediaManager'] = BackendAuth::getUser()->hasAccess('media.manage_media');

        // Specify AJAX event handlers for 'image' tool uploads.
        // if (array_key_exists('image', $this->blockSettings)) {
        //     $this->formField->attributes['field']['data-image-upload-by-file'] = $this->getEventHandler('onImageUploadByFile');
        //     $this->formField->attributes['field']['data-image-upload-by-url'] = $this->getEventHandler('onImageUploadByUrl');
        // }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->prepareBlocks();
        $this->addCss('css/style.css', 'ReaZzon.Editor');
        $this->addJs('js/editor.js', ['build' => 'ReaZzon.Editor']);
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }

    protected function prepareBlocks()
    {
        $pluginManager = PluginManager::instance();
        $plugins = $pluginManager->getPlugins();

        foreach ($plugins as $plugin) {
            $this->processEditorBlocks($plugin);
            $this->processEditorTunes($plugin);
            $this->processEditorInlineToolbar($plugin);

            /**
             * Extend config, add your own settings to already existing plugins.
             *
             * Event::listen(\ReaZzon\Editor\FormWidgets\EditorJS::EVENT_CONFIG_BUILT, function($blocks) {
             *
             *     foreach($blocks['settings'] as $settings) {
             *          // ..
             *     }
             *
             *     foreach($blocks['scripts'] as $script) {
             *         // ..
             *     }
             *
             *     foreach($blocks['tunes'] as $tuneItem) {
             *         // ..
             *     }
             *
             *     foreach($blocks['inlineToolbar'] as $inlineToolbarItem) {
             *         // ..
             *     }
             *
             *     return $blocks;
             * });
             */
            $eventBlocks = Event::fire(self::EVENT_CONFIG_BUILT, [
                'settings' => $this->blockSettings,
                'scripts' => $this->blocksScripts,
                'tunes' => $this->tuneSettings,
                'inlineToolbar' => $this->inlineToolbarSettings
            ]);

            if (!empty($eventBlocks)) {
                $this->blockSettings = $eventBlocks['settings'];
                $this->blocksScripts = $eventBlocks['scripts'];
                $this->tuneSettings = $eventBlocks['tunes'];
                $this->inlineToolbarSettings = $eventBlocks['inlineToolbar'];
            }

            if (!empty($this->blocksScripts)) {
                foreach ($this->blocksScripts as $script) {
                    $this->addJs($script);
                }
            }
        }
    }

    protected function processEditorBlocks($plugin): void
    {
        if (!method_exists($plugin, 'registerEditorBlocks')) {
            return;
        }

        $editorPlugins = $plugin->registerEditorBlocks();
        if (!is_array($editorPlugins) && !empty($editorPlugins)) {
            return;
        }

        /**
         * @var string $block
         * @var array $section
         */
        foreach ($editorPlugins as $block => $sections) {
            foreach ($sections as $name => $section) {
                if ($name === 'settings') {
                    $this->blockSettings = array_add($this->blockSettings, $block, $section);
                }
                if ($name === 'scripts') {
                    foreach ($section as $script) {
                        $this->blocksScripts[] = $script;
                    }
                }
            }
        }
    }

    protected function processEditorTunes($plugin): void
    {
        if (!method_exists($plugin, 'registerEditorTunes')) {
            return;
        }

        $editorTunes = $plugin->registerEditorTunes();
        if (empty($editorTunes) && !is_array($editorTunes)) {
            return;
        }

        foreach ($editorTunes as $tune) {
            $this->tuneSettings[] = $tune;
        }
    }

    protected function processEditorInlineToolbar($plugin): void
    {
        if (!method_exists($plugin, 'registerEditorInlineToolbar')) {
            return;
        }

        $inlineToolbarSettings = $plugin->registerEditorInlineToolbar();
        if (empty($inlineToolbarSettings) && !is_array($inlineToolbarSettings)) {
            return;
        }

        foreach ($inlineToolbarSettings as $inlineToolbarSetting) {
            $this->inlineToolbarSettings[] = $inlineToolbarSetting;
        }
    }
}
