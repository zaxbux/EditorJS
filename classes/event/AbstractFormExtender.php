<?php

namespace Zaxbux\EditorJS\Classes\Event;

use Backend\Widgets\Form;
use Winter\Storm\Events\Dispatcher;
use System\Classes\PluginManager;

abstract class AbstractFormExtender
{
    protected $controllerClass;
    protected $modelClass;

    protected $fieldType;
    protected $fieldWidgetPath;

    public function subscribe(Dispatcher $event)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->controllerClass = $this->getControllerClass();
        $this->modelClass = $this->getModelClass();

        $this->setFormWidget();

        $event->listen('backend.form.extendFields', function (Form $widget) {

            if (!$widget->getController() instanceof $this->controllerClass) {
                return;
            }

            if (!$widget->model instanceof $this->modelClass) {
                return;
            }

            $this->replaceField($widget);
        });

        $this->extendModel();
    }

    abstract protected function replaceField(Form $widget);

    abstract protected function extendModel();

    abstract protected function getControllerClass();

    abstract protected function getModelClass();

    abstract protected function isEnabled();

    private function setFormWidget() {
        $this->fieldType = 'editorjs';
        $this->fieldWidgetPath = \Zaxbux\EditorJS\FormWidgets\EditorJS::class;

        if (
            PluginManager::instance()->hasPlugin('Winter.Translate')
            && !PluginManager::instance()->isDisabled('Winter.Translate')
        ) {
            $this->fieldType = 'mleditorjs';
            $this->fieldWidgetPath = \Zaxbux\EditorJS\FormWidgets\MLEditorJS::class;
        }
    }
}
