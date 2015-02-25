module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);

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
            requirejs: {
                src: 'bower_components/requirejs-bower/require.js',
                dest: 'public/dist/js/require.min.js'
            },
            main_app: {
                src: 'build/js/main.js',
                dest: 'public/dist/js/main.min.js',
                options: {
                    compress: false,
                    mangle: false,
                    sourceMap: true
                }
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
                mainConfigFile: 'resources/assets/js/app/main.js',
                //out: 'public/dist/js/main.min.js',
                baseUrl: 'resources/assets/js/app',
                stubModules: ['jsx'],
                modules: [{
                    name: 'main',
                    exclude: ['JSXTransformer', 'text']
                }],
                dir: 'build/js',
                //removeCombined: true,
                //findNestedDependencies: true,
                //include: ['jsx!../app/Core'],
                preserveLicenseComments: false
                //wrap: true
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
                cssDir: 'public/dist/css/'
            },
            dev: {
                options: {
                    sourcemap: true,
                    outputStyle: 'nested',
                    watch: true
                }
            }
        },
        copy: {
            requirejs: {
                files: [{
                    expand: true,
                    src: ['build/js/main.*'],
                    dest: 'public/dist/js',
                    filter: 'isFile',
                    flatten: true
                }]
            }
        },
        watch: {
            js_app: {
                files: ['resources/assets/js/app.jsx'],
                tasks: 'requirejs:dev'
            },
            jsx: {
                files: ['resources/assets/js/**/*.jsx'],
                tasks: ['requirejs:dev', 'copy:requirejs'],
                options: {
                    livereload: true
                }
            }
        },
        concurrent: {
            dev: {
                tasks: ['watch', 'compass:dev'],
                options: {
                    logConcurrentOutput: true
                }
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
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-notify');

    grunt.registerTask('build', ['requirejs:dev', 'copy:requirejs']);
    grunt.registerTask('app', ['requirejs:dev']);
    grunt.registerTask('focus', ['concurrent:dev']);

    grunt.task.run('notify_hooks');
};