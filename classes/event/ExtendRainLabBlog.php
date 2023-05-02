<?php namespace ReaZzon\Editor\Classes\Event;

use System\Classes\PluginManager;
use ReaZzon\Editor\Models\Settings;

/**
 * Class ExtendRainLabBlog
 *
 * The blog plugin uses its markdown editor, which handles image insertions by attaching to the post model.
 *
 * @package ReaZzon\Editor\Classes\Event
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class ExtendRainLabBlog
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $event
     */
    public function subscribe($event)
    {
        if (Settings::get('integration_blog', false) &&
            PluginManager::instance()->hasPlugin('Winter.Blog')) {

            $event->listen('backend.form.extendFields', function ($widget) {

                // Only for Winter.Blog Posts controller
                if (!$widget->getController() instanceof \Winter\Blog\Controllers\Posts) {
                    return;
                }

                // Only for Winter.Blog Post model
                if (!$widget->model instanceof \Winter\Blog\Models\Post) {
                    return;
                }

                $fieldType = 'editorjs';
                $fieldWidgetPath = \ReaZzon\Editor\FormWidgets\EditorJS::class;

                if (PluginManager::instance()->hasPlugin('Winter.Translate')
                    && !PluginManager::instance()->isDisabled('Winter.Translate')) {
                    $fieldType = 'mleditorjs';
                    $fieldWidgetPath = \ReaZzon\Editor\FormWidgets\MLEditorJS::class;
                }

                // Finding content field and changing it's type regardless whatever it already is.
                foreach ($widget->getFields() as $field) {
                    if ($field->fieldName === 'content') {
                        $field->config['type'] = $fieldType;
                        $field->config['widget'] = $fieldWidgetPath;
                        $field->config['stretch'] = true;
                        $field->config['default'] = json_encode([
                            "blocks" => [
                                [
                                   "type" => "paragraph",
                                   "data" => [
                                      "text" => "Default",
                                   ],
                                ],
                             ],
                        ]);
                    }
                }
            });

            // Replacing original content_html attribute.
            \Winter\Blog\Models\Post::extend(function ($model) {
                /** @var \Winter\Storm\Database\Model $model */
                $model->implement[] = \ReaZzon\Editor\Behaviors\ConvertToHtml::class;

                $model->bindEvent('model.getAttribute', function ($attribute, $value) use ($model) {
                    if ($attribute == 'content_html') {
                        return $model->convertJsonToHtml($model->getAttribute('content'));
                    }
                });
            });
        }
    }
}
