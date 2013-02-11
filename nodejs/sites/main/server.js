var express     = require('express'),
    mustache    = require('hogan-express'),
    //routes  = require('./routes')
    //user    = require('./routes/user'),
    http        = require('http'),
    path        = require('path');

var application     = express();
var publicDirectory = path.join(__dirname, '../../../www');
var viewsDirectory  = path.join(__dirname, '../../../www/themes/default/views');


// Initialize the port
application.set('port', process.env.PORT || 8080);

// Initialize the views
application.engine('mustache', mustache);
application.set('view engine', 'mustache');
application.set('views', viewsDirectory);
application.set('partials', {
    _header: '_header',
    _footer: '_footer'
});

//application.use(express.favicon());
//application.use(express.logger('dev'));
application.use(express.bodyParser());
application.use(express.methodOverride());
application.use(application.router);
application.use(express.static(publicDirectory));


application.configure('development', function(){
    application.use(express.errorHandler());
});



//application.get('/', routes.index);
//application.get('/users', user.list);
application.get('/', function(request, response)
{
    response.render('home');
});

// Run the server
http.createServer(application).listen(application.get('port'), function()
{
    console.log("Server listening on port " + application.get('port'));
});


