module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		jshint: {
			gruntfile: ['Gruntfile.js']
		},
		watch: {
			jshint: {
				files: 'Gruntfile.js',
				tasks: ['jshint:gruntfile']
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