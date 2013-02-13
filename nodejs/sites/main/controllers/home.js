var neolao      = require('../../../lib/neolao'),
    Controller  = require('../../../lib/neolao/site/Controller.js');

/**
 * @class       Home controller
 * @inherit     neolao.site.Controller
 */
module.exports = function()
{
    Controller.call(this);
};
module.exports.extend(Controller);
proto = module.exports.prototype;

/**
 * Get the representation string
 *
 * @return  String          The representation string
 */
proto.toString = function()
{
    return '[HomeController]';
};

/**
 * Default action
 */
proto.indexAction = function()
{
    this.render('home');
};


