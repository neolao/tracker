var express     = require('express'),
    neolao      = require('../../lib/neolao'),
    Site        = require('../../lib/neolao/Site.js'),
    mustache    = require('hogan-express'),
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

// Initialize the public directory
application.use(express.static(publicDirectory));

//application.use(express.favicon());
//application.use(express.logger('dev'));
//application.use(express.bodyParser());
//application.use(express.methodOverride());
//application.use(application.router);


application.configure('development', function(){
    application.use(express.errorHandler());
});


// Initialize the routes
var routes = require(path.join(__dirname, '../../../config/siteMainRoutes.json')),
    route, routeName, routeHandler;

routeHandler = function(route)
{
    var routePattern    = route.pattern,
        routeController = route.controller,
        routeAction     = route.action,
        routeReverse    = route.reverse,
        routePath;

    if (routePattern === undefined) {
        routePath = '*';
    } else {
        routePath = eval(routePattern);
    }

    application.get(routePath, function(serverRequest, serverResponse)
    {
        var ControllerClass = require('./controllers/'+routeController),
            controller      = new ControllerClass(),
            RequestClass    = require('../../lib/neolao/site/Request.js'),
            request         = new RequestClass();

        request.serverRequest   = serverRequest;
        request.action          = routeAction;
        controller.response     = serverResponse;
        controller.dispatch(request);
    });
};
for (routeName in routes) {
    route = routes[routeName];
    routeHandler(route);
}

// Run the server
http.createServer(application).listen(application.get('port'), function()
{
    console.log("Server listening on port " + application.get('port'));
});

