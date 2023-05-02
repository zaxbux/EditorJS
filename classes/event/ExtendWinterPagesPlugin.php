<?php namespace ReaZzon\Editor\Classes\Event;

use Backend\Widgets\Form;
use System\Classes\PluginManager;
use ReaZzon\Editor\Models\Settings;

/**
 * Extends the `Winter.Pages` plugin, replacing the *content* field richeditor with the EditorJS editor.
 * @package ReaZzon\Editor\Classes\Event
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class ExtendWinterPagesPlugin extends AbstractFormExtender
{
    protected function replaceField(Form $widget)
    {
        $widget->removeField('markup');
        // Registering editorjs formWidget
        $widget->addSecondaryTabFields([
            'viewBag[editor]' => [
                'tab' => 'winter.pages::lang.editor.content',
                'type' => $this->fieldType,
                'stretch' => true
            ]
        ]);
    }

    protected function extendModel()
    {
        \Winter\Pages\Classes\Page::extend(function ($model) {
            /** @var \Winter\Storm\Database\Model $model */
            $model->implement[] = 'ReaZzon.Editor.Behaviors.ConvertToHtml';

            $model->bindEvent('model.beforeSave', function () use ($model) {
                $model->markup = $model->convertJsonToHtml($model->viewBag['editor']);
            });
        });

        if (PluginManager::instance()->hasPlugin('Winter.Translate')
            && !PluginManager::instance()->isDisabled('Winter.Translate')) {

            \Winter\Translate\Classes\MLPage::extend(function ($model) {
                /** @var \Winter\Stirm\Database\Model $model */
                $model->implement[] = \ReaZzon\Editor\Behaviors\ConvertToHtml::class;

                $model->bindEvent('model.beforeSave', function () use ($model) {
                    if (isset($model->viewBag['editor']) && !empty($model->viewBag['editor'])) {
                        $model->markup = $model->convertJsonToHtml($model->viewBag['editor']);
                    }
                });
            });
        }
    }

    protected function getControllerClass()
    {
        return \Winter\Pages\Controllers\Index::class;
    }

    protected function getModelClass()
    {
        return \Winter\Pages\Classes\Page::class;
    }

    protected function isEnabled()
    {
        if (Settings::get('integration_winter_pages', false) &&
            PluginManager::instance()->hasPlugin('Winter.Pages')) {
            return true;
        }

        return false;
    }
}
