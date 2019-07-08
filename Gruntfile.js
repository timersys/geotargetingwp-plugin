module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		min: {
			dist : {
				src : ["public/js/geotarget-public.js"],
				dest : "public/js/min/geotarget-public-min.js"
			}
		}
	});

	grunt.loadNpmTasks('grunt-yui-compressor');

	// Default task(s).
	grunt.registerTask('default', ['min']);
};