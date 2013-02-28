var neolao      = require('neolao'),
    i18n        = require('neolao/I18n.js'),
    Locale      = require('neolao/i18n/Locale.js'),
    Site        = require('neolao/Site.js'),
    http        = require('http'),
    path        = require('path'),
    configuration,
    routes,
    locale, localeMessages,
    site;


// Load configuration
configuration   = require('../config/siteMain.json');
routes          = require('../config/siteMainRoutes.json');

// Load locales
localeMessages  = require('../locales/en_US/messages.json');
locale          = new Locale('en_US');
locale.configureMessages(localeMessages);
i18n.addLocale(locale);

// Create the site
site                    = new Site();
site.serverPort         = process.env.PORT || 8080;
site.serverName         = configuration.server.name;
site.controllersPath    = path.join(__dirname, 'sites/main/controllers');
site.viewsPath          = path.join(__dirname, '../www/themes', configuration.theme, 'views');
site.publicPath         = path.join(__dirname, '../www');
site.configureRoutes(routes);
site.run();


