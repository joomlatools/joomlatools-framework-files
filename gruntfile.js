module.exports = function(grunt) {

    // measures the time each task takes
    require('time-grunt')(grunt);

    // load time-grunt and all grunt plugins found in the package.json
    require('jit-grunt')(grunt);

    // grunt config
    grunt.initConfig({

        // Grunt variables
        assetsPath: 'resources/assets',


        // Compile sass files
        sass: {
            options: {
                outputStyle: 'compact'
            },
            dist: {
                files: {
                    '<%= assetsPath %>/css/files.css': '<%= assetsPath %>/scss/files.scss',
                    '<%= assetsPath %>/css/uploader.css': '<%= assetsPath %>/scss/uploader.scss'
                }
            }
        },


        // Autoprefixer
        autoprefixer: {
            options: {
                browsers: ['> 5%', 'last 2 versions', 'ie 11']
            },
            files: {
                expand: true,
                flatten: true,
                src: '<%= assetsPath %>/css/*.css',
                dest: '<%= assetsPath %>/css/'
            }
        },


        // Shell commands
        shell: {
            updateCanIUse: {
                command: 'npm update caniuse-db'
            }
        },


        // Watch files
        watch: {
            sass: {
                files: [
                    '<%= assetsPath %>/scss/*.scss',
                    '<%= assetsPath %>/scss/**/*.scss'
                ],
                tasks: ['sass', 'autoprefixer'],
                options: {
                    interrupt: true,
                    atBegin: true
                }
            }
        }


    });

    // The dev task will be used during development
    grunt.registerTask('default', ['shell', 'watch']);

};