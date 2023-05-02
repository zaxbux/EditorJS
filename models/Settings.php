<?php

namespace ReaZzon\Editor\Models;

use Model;
use System\Classes\PluginManager;

/**
 * Class Settings
 * @package ReaZzon\Editor\Models
 * @author Nick Khaetsky, nick@reazzon.ru
 */
class Settings extends Model
{
    public $implement = [
        \System\Behaviors\SettingsModel::class,
    ];

    /**
     * @var string A unique code
     */
    public $settingsCode = 'reazzon_editor_settings';

    /**
     * @var string Reference to field configuration
     */
    public $settingsFields = 'fields.yaml';

    /**
     * Disables form fields based on presence of plugins.
     */
    public function filterFields($fields, $context = null)
    {
        $pluginManager = PluginManager::instance();

        $integrations = [
            'integration_winter_blog' => 'Winter.Blog',
            'integration_winter_pages' => 'Winter.Pages',
        ];

        foreach ($integrations as $key => $value) {
            $fields->{$key}->disabled = !$pluginManager->hasPlugin($value);
        }
    }
}
