module.exports = function()
{
    var controller = new HomeController();
    return controller;
};

function HomeController()
{
};

HomeController.prototype.dispatch = function(request, response)
{
    response.render('home');
};
