module.exports = function()
{
    var controller = new ErrorController();
    return controller;
};

function ErrorController()
{
};

ErrorController.prototype.dispatch = function(request, response)
{
    response.render('errors/404');
};
