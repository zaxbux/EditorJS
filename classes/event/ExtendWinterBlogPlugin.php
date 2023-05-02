<?php namespace Zaxbux\EditorJS\Classes\Event;

use Backend\Widgets\Form;
use System\Classes\PluginManager;
use Zaxbux\EditorJS\Models\Settings;

/**
 * Extends the `Winter.Blog` plugin, replacing the *content* field richeditor with the EditorJS editor.
 * @package Zaxbux\EditorJS\Classes\Event
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class ExtendWinterBlogPlugin extends AbstractFormExtender
{
    protected function replaceField(Form $widget)
    {
        if ($field = $widget->getField('content')) {
            $field->displayAs('widget', ['widget' => $this->fieldWidgetPath]);
            $field->stretch = true;
        }
    }

    protected function extendModel()
    {
        // Replacing original content_html attribute.
        $this->modelClass::extend(function ($model) {
            $model->implement[] = \Zaxbux\EditorJS\Behaviors\ConvertToHtml::class;

            $model->bindEvent('model.getAttribute', function ($attribute, $value) use ($model) {
                if ($attribute == 'content_html') {
                    return $model->convertJsonToHtml($model->getAttribute('content'));
                }
            });

            $model->bindEvent('model.translate.resolveComputedFields', function ($locale) use ($model) {
                return [
                    'content_html' => $model->convertJsonToHtml($model->asExtension('TranslatableModel')->getAttributeTranslated('content', $locale))
                ];
            });
        });
    }

    protected function getControllerClass()
    {
        return \Winter\Blog\Controllers\Posts::class;
    }

    protected function getModelClass()
    {
        return \Winter\Blog\Models\Post::class;
    }

    protected function isEnabled()
    {
        if (Settings::get('integration_winter_blog', false) &&
            PluginManager::instance()->hasPlugin('Winter.Blog')) {
            return true;
        }

        return false;
    }
}
