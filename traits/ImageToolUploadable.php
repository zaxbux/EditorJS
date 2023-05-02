<?php

namespace ReaZzon\Editor\Traits;

use Request;
use Exception;
use Illuminate\Support\Facades\Lang;
use System\Classes\PluginManager;
use Winter\Storm\Support\Facades\Validator;
use Winter\Storm\Exception\ValidationException;
use Illuminate\Http\UploadedFile;
use System\Models\File;
use ReaZzon\Editor\Models\Settings;
use ReaZzon\Editor\Classes\Exceptions\PluginErrorException;

trait ImageToolUploadable
{
    use \Backend\Traits\FormModelSaver;
    use \Backend\Traits\FormModelWidget;

    /**
     * Handle images being uploaded to the blog post
     */
    public function onImageUploadByFile()
    {
        if ($this->readOnly) {
            return null;
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = Request::file('file');
        if (null === $uploadedFile || !$uploadedFile instanceof UploadedFile) {
            throw new PluginErrorException;
        }

        try {
            $file = new File();
            $file->fromPost($uploadedFile);
        } catch (Exception $ex) {
            $message = $uploadedFile
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $uploadedFile->getClientOriginalName(), 'error' => $ex->getMessage()])
                : $ex->getMessage();

            throw new PluginErrorException($message, $ex->getCode(), $ex);
        }

        return $this->processFile($file, $uploadedFile->getClientOriginalName());
    }

    /**
     * Handle images being uploaded to the blog post
     */
    public function onImageUploadByURL()
    {
        if ($this->readOnly) {
            return null;
        }

        $fileUrl = Request::input('url');
        if (empty($fileUrl)) {
            throw new PluginErrorException;
        }

        try {
            $file = new File();
            $file->fromUrl($fileUrl);
        } catch (Exception $ex) {
            $message = $fileUrl
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $fileUrl, 'error' => $ex->getMessage()])
                : $ex->getMessage();

            throw new PluginErrorException($message, $ex->getCode(), $ex);
        }

        return $this->processFile($file);
    }

    protected function processFile(File $file, string $uploadedFileName = null)
    {
        try {

            if (!$uploadedFileName) {
                $uploadedFileName = $file->getFilename();
            }

            $validationRules = ['max:' . File::getMaxFilesize()];
            $validationRules[] = 'mimes:jpg,jpeg,bmp,png,gif';

            $validation = Validator::make(
                ['file_data' => $file],
                ['file_data' => $validationRules]
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            // if (!$uploadedFile->isValid()) {
            //     throw new SystemException(Lang::get('cms::lang.asset.file_not_valid'));
            // }

            $file->is_public = true;
            $file->save();

            if ($fileRelation = $this->getFileRelation()) {
                // Using `$this->sessionKey` doesn't work, the token won't match
                $fileRelation->add($file, post('_session_key'));
            } else {
                // todo: if no relation, upload to media library instead
            }

            $result = [
                'success' => true,
                'file' => [
                    //'file' => $uploadedFileName,
                    'id' => $file->id,
                    'url' => $file->getPath(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtensionAttribute(),
                    'width' => $file->getWidthAttribute(),
                    'height' => $file->getHeightAttribute(),
                    'size' => $file->file_size, //$file->getSizeAttribute(),
                ],
            ];

            return $result;
        } catch (Exception $ex) {
            $message = $uploadedFileName
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $uploadedFileName, 'error' => $ex->getMessage()])
                : $ex->getMessage();

            throw new PluginErrorException($message, $ex->getCode(), $ex);
        }
    }

    protected function getFileRelation()
    {
        $pluginManager = PluginManager::instance();

        if (Settings::get('integration_blog', false) && $pluginManager->hasPlugin('Winter.Blog') && $this->model instanceof \Winter\Blog\Models\Post) {
            return $this->model->content_images();
        }
    }
}
