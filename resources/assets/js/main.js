require.config({
    paths: {
        react: 'lib/react',
        JSXTransformer: 'lib/JSXTransformer',
        jquery: 'lib/jquery',
        backbone: 'lib/backbone',
        underscore: 'lib/underscore',
        jsx: 'lib/jsx',
        text: 'lib/text'
    },
    shim: {
        backbone: {
            deps: [
                'jquery',
                'underscore'
            ],
            exports: "Backbone"
        },
        jquery: "$",
        underscore: "_",
        react: "React",
        JSXTransformer: "JSXTransformer",
        text: "text"
    },
    jsx: {
        fileExtension: ".jsx",
        transformOptions: {
            harmony: true,
            stripTypes: false
        },
        usePragma: false
    }
});

require(['jsx!app'], function() {

});