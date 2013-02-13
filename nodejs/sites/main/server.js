var neolao      = require('../../lib/neolao'),
    Site        = require('../../lib/neolao/Site.js'),
    http        = require('http'),
    path        = require('path'),
    configuration,
    routes,
    site;


// Load configuration
configuration   = require('../../../config/siteMain.json');
routes          = require('../../../config/siteMainRoutes.json');

// Create the site
site                    = new Site();
site.serverPort         = process.env.PORT || 8080;
site.serverName         = configuration.server.name;
site.controllersPath    = path.join(__dirname, 'controllers');
site.viewsPath          = path.join(__dirname, '../../../www/themes', configuration.theme, 'views');
site.publicPath         = path.join(__dirname, '../../../www');
site.configureRoutes(routes);
site.run();


