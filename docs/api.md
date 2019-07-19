# API

API class is class to work with API request. On created [API endpoint](https://panx.eu/docs/api-endpoints) creates API interface, which will handle all requests. To response to the request, the request must contain valid API key (One from `api_keys` table). The valid means, that the key must exists and must have not reached the rate limits. If the corresponding route have template file(s), it will be required, but if have function, the function will be executed, and if the function return something, the result will outputed as beautify json string (The result will be encoded using json_encode) and also it will save he response as cache file. This cache file name is combination of API key and URL.



To work with API class, you need to setup DB connection and run command `php panx-worker create api`