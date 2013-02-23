var neolao      = require('../../../lib/neolao'),
    Controller  = require('../../../lib/neolao/site/Controller.js');

/**
 * @class       Error controller
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
    return '[ErrorController]';
};

/**
 * HTTP 404
 */
proto.http404Action = function()
{
    this.render('errors/404');
};
