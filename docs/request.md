# Request class

Class to work with HTTP requests.

The class have following functions:

* `getUrl(): URL` - Returns URL object.
* `getQuery(string $key = null)` - Returns variable provided to the script via URL query ($_GET). If no key is passed, returns the entire array. If key does not exists, returns null.
* `getPost(string $key = null)` - Returns variable provided to the script via POST method ($_POST). If no key is passed, returns the entire array.
* `getMethod(): string` - Returns HTTP request method (GET, POST, HEAD, PUT, ...).
* `isMethod(string $method): bool` - Checks if the request method is the given one.
* `getHeader(string $header): ?string` - Return the value of the HTTP header. Pass the header name as the plain, HTTP-specified header name (e.g. 'Accept-Encoding').
* `getHeaders(): array` - Returns all HTTP headers.
* `getReferer(): ?string` - Returns referrer.
* `isSecured(): bool` - Is the request over HTTPS?
* `isAjax(): bool` - Is AJAX request?
* `getRemoteAddress(): ?string` - Returns the IP address of the remote client.
* `detectLanguage(array $langs): ?string` - Parse Accept-Language header and returns preferred language.
* `getMostPreferredLanguage(): ?string` - Return most preferred language.
* `getClientID()` - Returns string representaining the client unique ID.
* `workWith(string $method, array $vars): bool` -  Checks if are all of $vars isset(). The $method is GET ($_GET) or POST ($_POST)