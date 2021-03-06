require.config({
    paths: {
        'jsx': '../lib/jsx',
        'JSXTransformer': '../lib/JSXTransformer',
        'react': '../lib/react',
        'text': '../lib/text',
        'router': '../lib/router',
        'jquery': '../lib/jquery',
        'classnames': '../lib/classnames'
    },
    jsx: {
        fileExtension: ".jsx"
    }
});

require(['jsx!Core']);