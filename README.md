# Editor.js for Winter CMS

Meet the new Editor. The most advanced "WYSWYG" (if you can say so) editor ever.

## Key features

- It is a block-styled editor
- It returns clean data output in JSON
- Designed to be extendable with a simple API
- Native OctoberCMS events support for convenient extending of custom blocks

### Integrations:
- [Winter.Blog](https://github.com/wintercms/wn-blog-plugin)
- [Winter.Pages](https://github.com/wintercms/wn-pages-plugin)
- Lovata.GoodNews
- Indikator.News

### Block Tools

- [Attaches](https://github.com/editor-js/attaches)
- [Checklist](https://github.com/editor-js/checklist)
- [Code](https://github.com/editor-js/code)
- [Delimiter](https://github.com/editor-js/delimiter)
- [Embed](https://github.com/editor-js/embed)
- [Header](https://github.com/editor-js/header)
- [Image](https://github.com/editor-js/image)
  (paste url, drag'n'drop, upload)
- [Link](https://github.com/editor-js/link)
  (Opengraph)
- [List](https://github.com/editor-js/list) & [Nexted List](https://github.com/editor-js/nested-list)
- [Media Manager](https://github.com/zaxbux/wn-editorjs-plugin/tools/mediamanager)
  Winter CMS Media Library
- [Paragraph](https://github.com/editor-js/paragraph)
- [Quote](https://github.com/editor-js/quote)
- [Raw](https://github.com/editor-js/raw)
- [Table](https://github.com/editor-js/table)
- [Warning](https://github.com/editor-js/warning)

### Inline Tools

- [Inline Code](https://github.com/editor-js/inline-code)
- [Link Autocomplete](https://github.com/editor-js/link-autocomplete)
- [Marker](https://github.com/editor-js/marker)
- [Underline](https://github.com/editor-js/underline)

### Plugins

- [Undo](https://github.com/kommitters/editorjs-undo)
- [Drag/Drop](https://github.com/kommitters/editorjs-drag-drop)

### **What does it mean «block-styled editor»**

Workspace in classic editors is made of a single contenteditable element, used to create different HTML markups. Editor workspace consists of separate Blocks: paragraphs, headings, images, lists, quotes, etc. Each of them is an independent contenteditable element (or more complex structure) provided by Plugin and united by Editor's Core.

There are dozens of ready-to-use Blocks and the simple API for creation any Block you need. For example, you can implement Blocks for Tweets, Instagram posts, surveys and polls, CTA-buttons and even games.

### **What does it mean clean data output**

Classic WYSIWYG-editors produce raw HTML-markup with both content data and content appearance. On the contrary, Editor.js outputs JSON object with data of each Block.

Given data can be used as you want: render with HTML for Web clients, render natively for mobile apps, create markup for Facebook Instant Articles or Google AMP, generate an audio version and so on.

## **How to install**

Install plugin by OctoberCMS plugin updater.

Go to Settings –> Updates&Plugins find EditorJS in plugin search. Click on icon and install it.

## **Usage**

After installing plugin, you are now able to set in `fields.yaml`  `type:editorjs` to any desirable field. That's all.
You are not limited of how many editors can be rendered at one page.

### Form Widgets

#### `editorjs`

**Configuration:**

| Option          | Type       | Description |
| --------------- | ---------- | ----------- |
| `default`       | `string`   | [Data](https://editorjs.io/saving-data/#output-data-format). Initial editor data to render.
| `readOnly`      | `boolean`  | [Read-Only Mode](https://editorjs.io/configuration/#read-only-mode). Enable read-only mode. |
| `placeholder`   | `string`   | [Placeholder](https://editorjs.io/configuration/#placeholder). First Block placeholder. |
| `autofocus`     | `boolean`  | [Autofocus](https://editorjs.io/configuration/#autofocus). If true, set caret at the first Block after Editor is ready. |
| `defaultBlock`  | `string`   | [Default Block](https://editorjs.io/configuration/#change-the-default-block). This Tool will be used as default. If not specified, Paragraph Tool will be used. |
| `tools`         | `object`   | [Tools](). |
| `i18n`          | `object`   | [Internationalization](https://editorjs.io/configuration/#internationalization). |
| `inlineToolbar` | `string[]` | [Inline Toolbar Order](https://editorjs.io/configuration/#inline-toolbar-order) Defines default toolbar for all tools. |
| `tunes`         | `string[]` | [Block Tunes](https://editorjs.io/configuration/#block-tunes-connection). Common Block Tunes list. |

### How to enable integrations

1. Make sure that the desirable plugin for integration is installed in system (list of supported plugins listed in Key Features section)
2. Go to Settings
3. In the sidebar find `Editor Settings` button inside `Editor tab`
4. Enable desirable integrations
5. Done.

### How to render HTML from Editor JSON
To implement Editor to your Model, you must prepare a column in a database that is set to text.

1. Create a column with type `text` at your Model table, or use an already existing one.
2. Add `'ReaZzon.Editor.Behaviors.ConvertToHtml'` to $implement attribute of your model.
3. Add **get<YourColumnName>HtmlAttribute()** method and paste line of code as in the example below:
```
return $this->convertJsonToHtml($this->YourColumnName);
```
4. Render your field `{{ model.YourColumnName_html|raw }}`
5. Add editor styles to your page by `<link href="/plugins/reazzon/editor/assets/css/editorjs.css" rel="stylesheet">`

Example of model:
```
// ...
class Post extends Model
{

    // ...

    public $implement = [
        'ReaZzon.Editor.Behaviors.ConvertToHtml'
    ];

    // ...

    public function getContentHtmlAttribute()
    {
        return $this->convertJsonToHtml($this->content);
    }
}
```
Example of rendering:
```
{{ post.content_html|raw }}
```

## **Extending**

You can create any new block as you like by reading official documentation that you can find here [Editor.Js docs](https://editorjs.io/api)

After creating new JS scripts with new block type Class, you can go through steps below to extend EditorJS formwidget:
1. Create new method in your Plugin.php file named `registerEditorBlocks()`, and by example below add blocks array and scripts for them.
    ```
    /**
     * Registers additional blocks for EditorJS
     * @return array
     */
    public function registerEditorBlocks()
    {
        return [
            'raw' => [
                'settings' => [
                    'class' => 'RawTool'
                ],
                'validation' => [
                    'html' => [
                        'type' => 'string',
                        'allowedTags' => '*',
                    ]
                ],
                'scripts' => [
                    '/plugins/reazzon/editor/formwidgets/editorjs/assets/js/tools/raw.js',
                ],
                'view' => 'reazzon.editor::blocks.raw'
            ],
        ];
    }
    ```
2. Done.

Now you can even publish your editorjs extender plugin to marketplace, so everyone can use your block!

---

Editor.js developed by CodeX Club of web-development.
Adapted for OctoberCMS by Nick Khaetsky. [reazzon.ru](https://reazzon.ru)
