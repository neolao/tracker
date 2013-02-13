/**
 * Change the scope of a function
 * 
 * @param   Object      scope           The new scope
 * @return  Function                    The function with a new scope
 */
Function.prototype.delegate = function(scope)
{
    var f = function(){
        return arguments.callee.func.apply(arguments.callee.target, arguments);
    };
    f.target = scope;
    f.func = this;
    return f;
};

/**
 * Extend a class
 * 
 * @param   Function    base            The class object
 */
Function.prototype.extend = function(base)
{
    if (this && this.prototype && base && base.prototype) {
        for (var key in base.prototype) {
            this.prototype[key] = base.prototype[key];
        }
    }
};
