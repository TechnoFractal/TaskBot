const	serve = require('serve'),
		path = require('path');

const server = serve(
	path.join(
		__dirname, 
		'build'
	), {
		port: 5000,
		ignore: ['node_modules']
	}
)
