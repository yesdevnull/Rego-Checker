module.exports = function(grunt) {
    grunt.initConfig({
        uglify: {
            react: {
                src: 'bower_components/react/react-with-addons.js',
                dest: 'resources/assets/js/lib/react.js'
            },
            router: {
                src: 'bower_components/react-router/build/global/ReactRouter.js',
                dest: 'resources/assets/js/lib/router.js'
            },
            jsxtransformer: {
                src: 'bower_components/react/JSXTransformer.js',
                dest: 'resources/assets/js/lib/JSXTransformer.js'
            },
            backbone: {
                src: 'node_modules/backbone/backbone.js',
                dest: 'resources/assets/js/lib/backbone.js'
            },
            underscore: {
                src: 'node_modules/underscore/underscore.js',
                dest: 'resources/assets/js/lib/underscore.js'
            },
            jsx: {
                src: 'bower_components/requirejs-react-jsx/jsx.js',
                dest: 'resources/assets/js/lib/jsx.js'
            },
            tools: {
                src: 'bower_components/requirejs-text/text.js',
                dest: 'resources/assets/js/lib/text.js'
            },
            extras: {
                options: {
                    sourceMap: false
                },
                files: [{
                    expand: true,
                    cwd: 'resources/assets/js',
                    src: '_*.js',
                    dest: 'public/dist/js',
                    ext: '.min.js',
                    extDot: 'first'
                }]
            }
        },
        requirejs: {
            options: {
                mainConfigFile: 'resources/assets/js/main.js',
                include: ['main'],
                out: 'public/dist/js/main.min.js',
                baseUrl: 'resources/assets/js',
                removeCombined: true,
                findNestedDependencies: true,
                preserveLicenseComments: false,
                wrap: true,
                insertRequire: ['main']
            },
            dev: {
                options: {
                    optimize: 'none',
                    generateSourceMaps: true
                }
            }
        },
        compass: {
            options: {
                sassDir: 'resources/assets/css/',
                cssDir: 'public/dist/css/',
                require: 'susy'
            }
        },
        watch: {
            js_app: {
                files: ['resources/assets/js/app.jsx'],
                tasks: 'requirejs:dev'
            }
        },
        notify_hooks: {
            options: {
                enabled: true,
                success: true,
                title: 'Plates'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-notify');

    grunt.registerTask('build', ['uglify', 'requirejs:dev']);
    grunt.registerTask('app', ['requirejs:dev']);

    grunt.task.run('notify_hooks');
};