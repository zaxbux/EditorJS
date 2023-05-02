<?php
Route::group(['prefix' => 'editorjs'], function () {
    Route::group([
        'prefix' => 'plugins',
        'middleware' => ['web', \Zaxbux\EditorJS\Classes\Middlewares\PluginGroupMiddleware::class]
    ], function () {
        Route::any('linktool', \Zaxbux\EditorJS\Classes\Plugins\LinkTool\Plugin::class);
        Route::any('image/{type}', \Zaxbux\EditorJS\Classes\Plugins\Image\Plugin::class);
        Route::any('attaches', \Zaxbux\EditorJS\Classes\Plugins\Attaches\Plugin::class);
    });
});
