module.exports = function(grunt) {
    grunt.initConfig({
        uglify: {
            react: {
                src: 'node_modules/react/dist/react.js',
                dest: 'resources/assets/js/lib/react.js'
            },
            backbone: {
                src: 'node_modules/backbone/backbone.js',
                dest: 'resources/assets/js/lib/backbone.js'
            },
            underscore: {
                src: 'node_modules/underscore/underscore.js',
                dest: 'resources/assets/js/lib/underscore.js'
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
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-requirejs');

    grunt.registerTask('build', ['uglify', 'requirejs:dev']);
};