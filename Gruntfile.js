module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		sass: {
			dist: {
				files: [{
					expand: true,
					cwd: 'src/sass/',
					src: ['**/*.sass'],
					dest: 'assets/css',
					ext: '.css'
				}]
			}
		},
		jshint: {
			gruntfile: ['Gruntfile.js']
		},
		watch: {
			jshint: {
				files: 'Gruntfile.js',
				tasks: ['jshint:gruntfile']
			},
			css: {
				files: 'src/sass/**/*.sass',
				tasks: ['sass:dist']
			},
			lib: {
				files: 'lib/**/*.sass',
				tasks: ['sass:dist']
			}
		}
	});

	// Load needed plugins.
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');

	// Setup tasks.
	grunt.registerTask('default', ['sass', 'jshint', 'watch']);
};