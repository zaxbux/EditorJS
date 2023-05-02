<?php

namespace ReaZzon\Editor\Traits;

use Winter\Storm\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Exception;
use Illuminate\Support\Facades\Lang;
use System\Classes\PluginManager;
use Winter\Storm\Support\Facades\Validator;
use Winter\Storm\Exception\ValidationException;
use Illuminate\Http\UploadedFile;
use System\Models\File;
use ReaZzon\Editor\Models\Settings;
use ReaZzon\Editor\Classes\Exceptions\PluginErrorException;
use Winter\Storm\Exception\ApplicationException;

/**
 *
 * Similar to {@see \Backend\Traits\UploadableWidget}, however more file metadata is returned.
 */
trait ToolUploadableHandler
{
    use \Backend\Traits\FormModelSaver;
    use \Backend\Traits\FormModelWidget;
    use \Backend\Traits\UploadableWidget;

    /**
     * Handle images being uploaded to the blog post
     */
    public function onUpload()/* : ?\Illuminate\Http\Response */
    {
        if ($this->readOnly) {
            return null;
        }

        /**
         * @event reazzon.editorjs.attaches.onUpload
         * Provides an opportunity to process the file upload using custom logic.
         *
         * Example usage ()
         */
        if ($result = Event::fire('reazzon.editorjs.attaches.onUpload', [$this], true)) {
            return $result;
        }

        if (Request::post('_tool') === 'attaches' && Request::hasFile('file_data')) {
            return $this->onUploadDirect();
        } else if (Request::post('_tool') === 'image' && Request::hasFile('file_data')) {
            return $this->onUploadDirect();
        } else if (Request::post('_tool') === 'image' && Request::has('url')) {
            return $this->onUploadByURL();
        }

        throw new PluginErrorException;
    }

    protected function onUploadDirect()
    {
        if (!Request::hasFile('file_data')) {
            throw new PluginErrorException('File missing from request');
        }

        $uploadedFile = Request::file('file_data');

        if (!($uploadedFile instanceof UploadedFile)) {
            throw new PluginErrorException(Lang::get('cms::lang.asset.file_not_valid'));
        }

        if (!$uploadedFile->isValid()) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $message = "The file \"{$uploadedFile->getClientOriginalName()}\" uploaded successfully but wasn't "
                    . "available at {$uploadedFile->getPathName()}. Check to make sure that nothing moved it away.";
            } else {
                $message = $uploadedFile->getErrorMessage();
            }
            throw new ApplicationException($message);
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

        return $this->processFile($file);
    }

    protected function onUploadByURL() {
        $url = Request::post('url');

        try {
        $file = new File;
        $file->fromUrl($url);
        } catch (Exception $ex) {
            $message = $url
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $url, 'error' => $ex->getMessage()])
                : $ex->getMessage();

            throw new PluginErrorException($message, $ex->getCode(), $ex);
        }

        return $this->processFile($file);
    }

    protected function processFile(File $file)
    {
        try {
            $validationRules = ['max:' . File::getMaxFilesize()];

            /* if (Request::post('_tool') === 'image') {
                $validationRules[] = 'mimes:jpg,jpeg,bmp,png,gif';
            } */

            if (Request::post('_tool') === 'image' && !$file->isImage()) {
                throw new PluginErrorException('File must be image');
            }

            $validation = Validator::make(
                ['file_data' => $file],
                ['file_data' => $validationRules]
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            $result = [
                'url' => $file->getPath(),
                'name' => $file->getFilename(),
                'size' => $file->file_size,
                'extension' => $file->getExtension(),
            ];

            if ($file->isImage()) {
                $result['width'] = $file->getWidthAttribute();
                $result['height'] = $file->getHeightAttribute();
            }

            if ($fileRelation = $this->getFileRelation()) {
                $file->is_public = true;
                $file->save();

                $result['id'] = $file->id;

                $fileRelation->add($file);
            } else {
                $result['url'] = $this->putMediaLibrary($file);
            }

            return [
                'success' => true,
                'file' => $result,
            ];
        } catch (Exception $ex) {
            $message = $file->getFilename()
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $file->getFilename(), 'error' => $ex->getMessage()])
                : $ex->getMessage();

            throw new PluginErrorException($message, $ex->getCode(), $ex);
        }
    }

    protected function getFileRelation()
    {
        if ($this->shouldAttachToWinterBlogPost()) {
            return $this->model->content_images();
        }
    }

    protected function putMediaLibrary(File $file)
    {
        $fileName = $this->validateMediaFileName(
            $file->getFilename(), //$uploadedFile->getClientOriginalName(),
            $file->getExtension(), //$uploadedFile->getClientOriginalExtension()
        );

        /*
         * getRealPath() can be empty for some environments (IIS)
         */
        // $sourcePath = empty(trim($uploadedFile->getRealPath()))
        //     ? $uploadedFile->getPath() . DIRECTORY_SEPARATOR . $uploadedFile->getFileName()
        //     : $uploadedFile->getRealPath();

        $filePath = $this->uploadableGetUploadPath($fileName);

        //$this->uploadableGetDisk()->put($filePath, $file->getDiskPath());
        $file->getDisk()->move($file->getDiskPath(), $filePath);

        //$this->fireSystemEvent('media.file.upload', [&$filePath, $uploadedFile]);

        return $this->uploadableGetUploadUrl($filePath);
    }

    private function shouldAttachToWinterBlogPost()
    {
        return Request::post('_tool') === 'image' && Settings::get('integration_blog', false) && PluginManager::instance()->hasPlugin('Winter.Blog') && $this->model instanceof \Winter\Blog\Models\Post;
    }
}
