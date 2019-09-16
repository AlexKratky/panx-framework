<?php
/**
 * @name API.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with API rate limits. Part of panx-framework.
 */

declare(strict_types=1);

class API {
    /**
     * @var string $endpoint The endpoint name in URI (e.g. v1)
     */
    private $endpoint = ""; //e.g. v1
    /**
     * @var Request
     */
    private $request;
    /**
     * @var APIModel
     */
    private $apiModel;
    /**
     * @var int The cahce time of result per API key & request.
     */
    private const CACHE_TIME = 5;

    /**
     * Creates a new API endpoint. Prevent from running in terminal.
     * @param string $endpoint The endpoint name in URI (e.g. v1)
     */
    public function __construct(string $endpoint) {
        //if ran from terminal, prevent to all aciton
        if(!empty($_SERVER["REQUEST_URI"])) {
            $this->endpoint = $endpoint;
            $this->request = new Request();
            $this->apiModel = new APIModel();
        }
    }

    /**
     * Determine if the request is valid and the data can be outputted or not.
     * @param URL $URL The requested URL (Used in cache).
     * @return bool Returns true if the request is valid, false otherwise.
     */
    public function request(URL $URL): bool {
        if($this->validate()) {
            $this->updateRate();
            $x = Route::searchWithNoLimits();
            if (is_callable($x)) {
                $cachedData = $this->getFromCache($URL->getString()); 
                if($cachedData !== false && $cachedData !== null) {
                    echo json(json_encode($cachedData));
                    exit();
                }
                $result = $x();
                if($result !== null) {
                    echo json(json_encode($result));
                    $this->cacheResult($result, $URL->getString());
                }
                exit();
            }

            return true;
        }
        return false;
    }

    /**
     * This function is called when the request is not valid.
     * @param string $msg The error message.
     * @return string JSON string containing: (bool) 'success' => false; (string) 'error' => $msg
     */
    public function error(string $msg = "Ivalid request. Check your API key and your rate limits."): string {
        return json_encode(
            array(
                "success" => false,
                "error" => $msg
            )
        );
    }

    /**
     * Validates API_KEY from $_POST["API_KEY"].
     * @return bool Returns true if the key is valid, false otherwise. If no key provided, execute and print json($this->error("No API_KEY provided.")) 
     */
    public function validate(): bool {
        if($this->request->getPost('API_KEY') !== null) {
            return $this->apiModel->validate($this->request->getPost('API_KEY'));
        } else {
            echo json($this->error("No API_KEY provided."));
            exit();
        }
    }

    /**
     * Caches result. Saves as {API_KEY}{URL}
     * @param mixed $result The data to be saved.
     * @param string $URL_STRING The URL string.
     */
    public function cacheResult($result, string $URL_STRING) {
        Cache::save($this->request->getPost('API_KEY') . str_replace('/', '_', $URL_STRING), $result);
    }

    /**
     * Obtain cached result.
     * @param string $URL_STRING The URL String of cached result.
     * @return mixed Returns false if no cache false, otherwise returns the result.
     */
    public function getFromCache(string $URL_STRING) {
        return Cache::get($this->request->getPost('API_KEY') . str_replace('/', '_', $URL_STRING), self::CACHE_TIME);
    }

    /**
     * Increses the Rate limit of API Key.
     */
    public function updateRate() {
        $this->apiModel->updateRate($this->request->getPost('API_KEY'));
    }
}
