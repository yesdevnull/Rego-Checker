require.config({
    paths: {
        react: 'lib/react',
        jquery: 'lib/jquery',
        backbone: 'lib/backbone',
        underscore: 'lib/underscore'
    },
    shim: {
        backbone: {
            deps: [
                'jquery',
                'underscore'
            ],
            exports: "Backbone"
        },
        jquery: {
            exports: "$"
        },
        underscore: {
            "exports": "_"
        }
    }
});

require(['app'], function() {
    console.log('App loaded');
});