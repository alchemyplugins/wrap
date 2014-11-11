module.exports = function(grunt) {
	require('load-grunt-tasks')(grunt);
	// settings
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
	});
	// default
	grunt.config.merge({
		watch: {
			config: {
				files: 'gruntfile.js',
				options: {
					reload: true
				}
			},
			deploy: {
				files: 'src/**',
				tasks: 'deploy'
			}
		}
	});
	//build
	grunt.config.merge({
		clean: {
			beforeBuild: {
				src: "build/"
			},
			afterBuild: {
				src: ["build/assets/sass", "build/**/*.js", "!build/**/*.min.js"]
			}
		},
		copy: {
			build: {
				expand: true,
				cwd: 'src/',
				src: '**',
				dest: 'build/',
				options: {
					process: function(content) {
						return grunt.template.process(content);
					}
				}
			}
		},
		jshint: {
			build: ["build/**/*.js"]
		},
		sass: {
			build: {
				files: {
					'build/assets/style.min.css': 'build/assets/sass/style.scss'
				},
				options: {
					style: 'compressed',
					sourcemap: 'none',
				}
			}
		},
		uglify: {
			build: {
				files: {
					'build/assets/script.min.js': 'build/assets/script.js'
				}
			}
		},
		notify: {
			build: {
				options: {
					message: '<%= pkg.title %> built'
				}
			}
		}
	});
	// deploy
	grunt.config.merge({
		copy: {
			deploy: {
				files: [
					{
						expand: true,
						cwd: 'build/',
						src: '**',
						dest: '<%= pkg.settings.wpDir %>/wp-content/plugins/<%= pkg.name %>'
					}
				]
			}
		},
		notify: {
			deploy: {
				options: {
					message: '<%= pkg.title %> deployed'
				}
			}
		}
	});
	/*
	compress: {
		dist: {
			options: {
				archive: 'dist/<%= pkg.name %>-dist-<%= grunt.template.today("yyyymmdd") %>.zip'
			},
			files: [
				{
					expand: true,
					src: ['{css,images,js,vendor}/**', '*.html', 'README.md'],
					dest: '<%= pkg.name %>-dist'
				}
			]
		},
		src: {
			options: {
				archive: 'dist/<%= pkg.name %>-src-<%= grunt.template.today("yyyymmdd") %>.zip'
			},
			files: [
				{
					expand: true,
					src: ['**','!dist/**', '!bower_components/**', '!node_modules/**'],
					dest: '<%= pkg.name %>-src'
				}
			]
		}
	}
	*/
	// tasks
	// build test // setup testing files
	// build core // setup plugin files
	grunt.registerTask('build', ['clean:beforeBuild', 'copy:build', 'jshint:build', 'sass:build', 'uglify:build', 'clean:afterBuild', 'notify:build']);
	grunt.registerTask('deploy', ['build', 'copy:deploy', 'notify:deploy']);
	//grunt.registerTask('release', ['build']);
	grunt.registerTask('default', ['watch']);
};
