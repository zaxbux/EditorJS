import { build } from "vite";
import path from "path";
import { fileURLToPath } from 'url';
const __dirname = path.dirname(fileURLToPath(import.meta.url));

const tools = {
    header: 'Header',
    marker: 'Marker',
    image: 'ImageTool',
    attaches: 'AttachesTool',
    link: 'LinkTool',
    linkautocomplete: 'LinkAutocomplete',
    list: 'List',
    checklist: 'Checklist',
    table: 'Table',
    quote: 'Quote',
    code: 'CodeTool',
    embed: 'Embed',
    raw: 'RawTool',
    delimiter: 'Delimiter',
    underline: 'Underline',
};

Object.entries(tools).forEach(async ([key, className]) => {
    await build({
        build: {
            manifest: false,
            outDir: '../../assets/js/tools',
            emptyOutDir: false,
            rollupOptions: undefined,
            lib: {
                entry: path.resolve(__dirname, `../src/tools/${key}.js`),
                fileName: `${key}`,
                name: `${className}`,
                formats: ['umd'],
            },
        },
    });
});
