var neolao          = require('neolao'),
    logger          = require('neolao/Logger'),
    ConsoleListener = require('neolao/logger/ConsoleListener'),
    FileListener    = require('neolao/logger/FileListener'),
    i18n            = require('neolao/I18n'),
    Locale          = require('neolao/i18n/Locale'),
    Site            = require('neolao/Site'),
    http            = require('http'),
    path            = require('path'),
    listener;

// Configure the logger
listener = new FileListener('../logs/debug.log', logger.DEBUG);
logger.addListener(listener);
listener = new FileListener('../logs/error.log', logger.ERROR);
logger.addListener(listener);
listener = new FileListener('../logs/warning.log', logger.WARNING);
logger.addListener(listener);
listener = new ConsoleListener();
logger.addListener(listener);

// Load configuration
var configuration       = require('../config/siteMain.json');
var routes              = require('../config/siteMainRoutes.json');

// Load locales
var localeMessages      = require('../locales/en_US/messages.json');
var locale              = new Locale('en_US');
locale.configureMessages(localeMessages);
i18n.addLocale(locale);

// Create the site
var site                = new Site();
site.serverPort         = process.env.PORT || 8080;
site.serverName         = configuration.server.name;
site.controllersPath    = path.join(__dirname, 'sites/main/controllers');
site.viewsPath          = path.join(__dirname, '../www/themes', configuration.theme, 'views');
site.publicPath         = path.join(__dirname, '../www');
site.configureRoutes(routes);
site.run();


