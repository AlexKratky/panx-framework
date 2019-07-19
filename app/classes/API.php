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

class API {
    private $endpoint = ""; //e.g. v1
    private $request;
    private $apiModel;

    public function __construct($endpoint) {
        $this->endpoint = $endpoint;
        $this->request = new Request();
        $this->apiModel = new APIModel();
    }

    public function request($URL) {
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

    public function error($msg = "Ivalid request. Check your API key and your rate limits.") {
        return json_encode(
            array(
                "success" => false,
                "error" => $msg
            )
        );
    }

    public function validate() {
        if($this->request->getPost('API_KEY') !== null) {
            return $this->apiModel->validate($this->request->getPost('API_KEY'));
        } else {
            echo json($this->error("No API_KEY provided."));
            exit();
        }
    }

    public function cacheResult($result, $URL_STRING) {
        Cache::save($this->request->getPost('API_KEY') . str_replace('/', '_', $URL_STRING), $result);
    }

    public function getFromCache($URL_STRING) {
        return Cache::get($this->request->getPost('API_KEY') . str_replace('/', '_', $URL_STRING), 10);
    }

    public function updateRate() {
        $this->apiModel->updateRate($this->request->getPost('API_KEY'));
    }
}