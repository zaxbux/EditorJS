import EditorJS from '@editorjs/editorjs';
import DragDrop from 'editorjs-drag-drop';
import Undo from 'editorjs-undo';

/*
 * Rich text editor with blocks form field control (WYSIWYG)
 *
 * Data attributes:
 * - data-control="editorjs" - enables the editorjs plugin
 *
 * JavaScript API:
 * $('div#id').editor()
 *
 */

import './editor.css';

export default (function ($) {
    "use strict";
    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    // Editor CLASS DEFINITION
    // ============================

    var Editor = function (element, options) {
        this.options = options
        this.$el = $(element)
        this.$form = this.$el.closest('form')
        this.$textarea = this.$el.find('>textarea:first')
        this.$editor = null
        this.settings = this.$el.data('settings')
        this.blockSettings = this.$el.data('block-settings')
        this.tuneSettings = this.$el.data('tune-settings')
        this.inlineToolbarSettings = this.$el.data('inline-toolbar-settings')
        //this.imageUploadByFileHandler = this.$el.data('image-upload-by-file')
        //this.imageUploadByUrlHandler = this.$el.data('image-upload-by-url')
        this.useMediaManager = this.$el.data('use-media-manager')
        this.uploadHandler = this.$el.data('upload-handler')
        this.sessionKey = $('input[name=_session_key]', this.$form).val()

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)

        this.init()
    }

    Editor.prototype = Object.create(BaseProto)
    Editor.prototype.constructor = Editor

    Editor.prototype.init = function () {
        this.$el.one('dispose-control', this.proxy(this.dispose))

        this.initEditorJS();
    }

    Editor.prototype.initEditorJS = function () {

        // Init all plugin classes from config
        for (let [key, value] of Object.entries(this.blockSettings)) {
            value.class = window[value.class];

            // Set custom uploader
            if (key === 'image') {
                value.config.uploader = {
                    //uploadByFile: this.proxy(this.handleImageUploadByFile),
                    uploadByFile: (file_data) => this.handleAttachUploadByFile({ file_data, _tool: 'image' }),
                    uploadByUrl: (url) => this.handleAttachUploadByFile({ url, _tool: 'image' }),
                };
            }

            if (key === 'attaches') {
                value.config.uploader = {
                    uploadByFile: (file_data) => this.handleAttachUploadByFile({ file_data, _tool: 'attaches' }),
                }
            }

            if (key === 'mediamanager') {
            }
        }

        // Parameters for EditorJS
        /** @type {import('@editorjs/editorjs').EditorConfig} */
        let parameters = {
            //holder: this.$el.attr('id'),
            holder: this.$el[0],
            autofocus: this.settings.autofocus,
            defaultBlock: this.settings.defaultBlock,
            placeholder: this.settings.placeholder,
            tools: this.blockSettings,
            tunes: this.tuneSettings,
            inlineToolbar: this.inlineToolbarSettings,
            readOnly: this.settings.readOnly,
            i18n: this.settings.i18n,
            onReady: () => {
                new DragDrop(this.$editor);
                const undo = new Undo({ editor: this.$editor });
                if (this.$textarea.val().length > 0 && this.isJson(this.$textarea.val()) === true) {
                    undo.initialize(JSON.parse(this.$textarea.val()));
                }
                this.$el.trigger('formwidgets.editorjs.ready', [this])
            },
            onChange: () => {
                this.onChange()
            },
        }

        this.$form.on('oc.beforeRequest', this.proxy(this.onFormBeforeRequest))

        // Parsing already existing data from textarea
        if (this.$textarea.val().length > 0 && this.isJson(this.$textarea.val()) === true) {
            parameters.data = JSON.parse(this.$textarea.val())
        }

        this.$editor = new EditorJS(parameters);

        Snowboard.globalEvent("formwidgets.editorjs.init", this)
    }

    Editor.prototype.dispose = function () {
        this.unregisterHandlers();

        this.$editor.destroy();

        this.options = null;
        this.$el = null;
        this.$form = null;
        this.$textarea = null;
        this.settings = null;
        this.blockSettings = null;
        this.tuneSettings = null;
        this.inlineToolbarSettings = null;
        //this.imageUploadByFileHandler = null;
        //this.imageUploadByUrlHandler = null;
        this.useMediaManager = null;
        this.uploadHandler = null;
        this.$editor = null;
        this.sessionKey = null;

        BaseProto.dispose.call(this)
    }

    Editor.prototype.unregisterHandlers = function () {
        this.$form.off('oc.beforeRequest', this.proxy(this.onFormBeforeRequest))

        this.$el.off('dispose-control', this.proxy(this.dispose))
    }

    Editor.prototype.getElement = function () {
        return this.$el
    }

    Editor.prototype.getEditor = function () {
        return this.$editor
    }

    Editor.prototype.getTextarea = function () {
        return this.$textarea
    }

    Editor.prototype.getContent = function () {
        return this.$editor.save()
    }

    Editor.prototype.setContent = function (data) {
        this.$editor.render(data)
    }

    /**
     * Upload file to the server and return an uploaded image data
     * @param {File} file - file selected from the device or pasted by drag-n-drop
     * @return {Promise.<{success, file: {url}}>}
     */
    /* Editor.prototype.handleImageUploadByFile = function (file) {
        const req = new Promise((resolve, reject) => {
            $.request(this.imageUploadByFileHandler, {
                files: true,
                data: {
                    file,
                    _tool: 'image',
                    _sessionKey: this.sessionKey,
                },
                success: (data) => {
                    resolve(data);
                },
            });
        });

        return req.then(data => {
            return data;
        });
    } */

    /**
     * Send URL-string to the server. Backend should load image by this URL and return an uploaded image data
     * @param {string} url - pasted image URL
     * @return {Promise.<{success, file: {url}}>}
     */
    /* Editor.prototype.handleImageUploadByUrl = function (url) {
        const req = new Promise((resolve, reject) => {
            $.request(this.imageUploadByUrlHandler, {
                data: {
                    url,
                    _tool: 'image',
                    _session_key: this.sessionKey,
                },
                success: (data) => {
                    resolve(data);
                },
            });
        });

        return req.then(data => {
            return data;
        });
    } */

    /**
     * Upload file to the server and return an uploaded image data
     * @param {{ file_data: File | url: string}} data - file selected from the device or pasted by drag-n-drop
     * @return {Promise.<{success, file: {url}}>}
     */
    Editor.prototype.handleAttachUploadByFile = function (data) {
        const req = new Promise((resolve, reject) => {
            $.request(this.uploadHandler, {
                files: true,
                data: {
                    ...data,
                    _session_key: this.sessionKey,
                },
                success: (data) => {
                    resolve(data);
                },
            });
        });

        return req.then(data => {
            return data;
        });
    }

    /*
     * Instantly synchronizes HTML content.
     */
    Editor.prototype.onChange = function (ev) {
        this.$editor.save().then(outputData => {
            this.$textarea.val(JSON.stringify(outputData));
            this.$textarea.trigger('formwidgets.editorjs.change', [this, outputData])
        })
            .catch(error => console.log('editorjs - Error get content: ', error.message));
    }

    /*
     * Synchronizes HTML content before sending AJAX requests
     */
    Editor.prototype.onFormBeforeRequest = function (ev) {
        this.onChange(ev);
    }

    Editor.prototype.isJson = function (string) {
        try {
            JSON.parse(string);
        } catch (e) {
            return false;
        }
        return true;
    }

    // Editor PLUGIN DEFINITION
    // ============================

    var old = $.fn.Editor

    $.fn.Editor = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this = $(this)
            var data = $this.data('wn.editorjs')
            var options = $.extend({}, Editor.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('wn.editorjs', (data = new Editor(this, options)))
            // if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.Editor.Constructor = Editor

    // Editor NO CONFLICT
    // =================

    $.fn.Editor.noConflict = function () {
        $.fn.Editor = old
        return this
    }

    // Editor DATA-API
    // ===============

    $(document).render(function () {
        $('[data-control="editorjs"]').Editor();
    })

})(window.jQuery);
