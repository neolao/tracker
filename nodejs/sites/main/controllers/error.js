var neolao      = require('neolao'),
    Controller  = require('neolao/site/Controller');

/**
 * @class       Error controller
 * @inherit     neolao.site.Controller
 */
module.exports = function()
{
    this.constructor.super_.call(this);
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

