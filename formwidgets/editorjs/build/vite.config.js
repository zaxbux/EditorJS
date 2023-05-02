import { defineConfig, /* splitVendorChunkPlugin */ } from 'vite';

export default defineConfig({
    build: {
        emptyOutDir: false,
        outDir: '../assets',
        sourcemap: false,
        manifest: true,
        lib: {
            entry: {
                editor: './src/editor.js',
            },
            name: 'EditorJS',
            formats: ['umd'],
        },
        rollupOptions: {
            output: {
              chunkFileNames: 'js/[name].js',
              entryFileNames: 'js/[name].js',

              assetFileNames: ({name}) => {
                if (/\.css$/.test(name ?? '')) {
                    return 'css/[name][extname]';
                }

                // default value
                // ref: https://rollupjs.org/guide/en/#outputassetfilenames
                return '[name][extname]';
              },
            },
          }
    },
    //plugins: [splitVendorChunkPlugin()],
})
