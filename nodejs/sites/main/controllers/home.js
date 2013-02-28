var neolao      = require('neolao'),
    Controller  = require('neolao/site/Controller.js');

/**
 * @class       Home controller
 * @inherit     neolao.site.Controller
 */
module.exports = function()
{
    this.constructor.super_();
};
module.exports.extends(Controller);
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


