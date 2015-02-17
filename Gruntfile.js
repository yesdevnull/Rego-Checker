module.exports = function(grunt) {
    grunt.initConfig({
        uglify: {
            react_dev: {
                src: 'node_modules/react/react.js',
                dest: 'public/dist/js/react.min.js'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('build', ['uglify:react_dev']);
};