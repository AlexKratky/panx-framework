# Middlewares

Using middlewares, you can restrict access to content. Middlewares are located in `/app/middlewares` and each middleware must have ::handle() function. The handle() function will return true or false. The true means, that the user can continue and view the content, but the false means, that the user have not access for viewing content. If the middleware have also ::error() function, then on the false, the error() function will be called, if the middleware does not have error() function, the loader will call error(ERROR_MIDDLEWARE) which will include the error template file.







To see how register middleware, see [Routes](https://panx.eu/docs/routes).