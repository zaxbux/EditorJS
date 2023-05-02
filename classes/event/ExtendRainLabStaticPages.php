<?php namespace ReaZzon\Editor\Classes\Event;

use Winter\Translate\Classes\MLStaticPage;
use ReaZzon\Editor\Models\Settings;
use System\Classes\PluginManager;

/**
 * Class ExtendWinterStaticPages
 *
 * The pages plugin uses the richeditor widget, which uploads inserted images/audio/video/documents to the media library.
 *
 * @package ReaZzon\Editor\Classes\Event
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class ExtendRainLabStaticPages
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $event
     */
    public function subscribe($event)
    {
        if (Settings::get('integration_static_pages', false) &&
            PluginManager::instance()->hasPlugin('Winter.Pages')) {

            $event->listen('backend.form.extendFields', function ($widget) {

                // Only for Winter.StaticPages Index controller
                if (!$widget->getController() instanceof \Winter\Pages\Controllers\Index) {
                    return;
                }

                // Only for Winter.StaticPages Page model
                if (!$widget->model instanceof \Winter\Pages\Classes\Page) {
                    return;
                }

                $widget->removeField('markup');

                $fieldType = 'editorjs';

                if (PluginManager::instance()->hasPlugin('Winter.Translate')
                    && !PluginManager::instance()->isDisabled('Winter.Translate')) {
                    $fieldType = 'mleditorjs';
                }

                // Registering editorjs formWidget
                $widget->addSecondaryTabFields([
                    'viewBag[editor]' => [
                        'tab' => 'winter.pages::lang.editor.content',
                        'type' => $fieldType,
                        'stretch' => true
                    ]
                ]);
            });

            \Winter\Pages\Classes\Page::extend(function ($model) {
                /** @var \October\Rain\Database\Model $model */
                $model->implement[] = 'ReaZzon.Editor.Behaviors.ConvertToHtml';

                $model->bindEvent('model.beforeSave', function () use ($model) {
                    //$model->markup = $model->convertJsonToHtml($model->viewBag['editor']);
                    $model->markup = $model->convertJsonToHtml($model->markup);
                });
            });

            if (PluginManager::instance()->hasPlugin('Winter.Translate')
                && !PluginManager::instance()->isDisabled('Winter.Translate')) {

                MLStaticPage::extend(function (MLStaticPage $model) {
                    /** @var \Winter\Storm\Database\Model $model */
                    $model->implement[] = 'ReaZzon.Editor.Behaviors.ConvertToHtml';

                    $model->bindEvent('model.beforeSave', function () use ($model) {
                        if (isset($model->viewBag['editor']) && !empty($model->viewBag['editor'])) {
                            $model->markup = $model->convertJsonToHtml($model->viewBag['editor']);
                        }
                    });
                });
            }
        }
    }
}
