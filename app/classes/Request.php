<?php
/**
 * @name Request.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with HTTP requests and HTTP methods. Part of panx-framework.
 */

declare(strict_types=1);

class Request {
    /** 
     * @var string The current method.
     */
    private $method;
    
	/** 
     * @var URL The URL object.
     */
    private $url;
    
	/** 
     * @var array The array of headers.
    */
    private $headers;
    
	/** 
     * @var string|null The client's IP adress or null.
     */
    private $remoteAddress;

    /**
     * @var boolean Should be data from $_GET and $_POST escaped?
     */
    private $htmlEscape;

    /**
     * Creates new instance of Request
     * @param boolean $htmlEscape If sets the true, the data from $_GET and $_POST will be escaped using htmlspecialchars().
     */
	public function __construct(bool $htmlEscape = true) {
		$this->url = new URL();
		$this->headers = array_change_key_case((array) getallheaders(), CASE_LOWER);
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->htmlEscape = $htmlEscape;
		$this->remoteAddress = $_SERVER["REMOTE_ADDR"];
    }
    
    /**
	 * Returns URL object.
     * @return URL The URL object.
	 */
	public function getUrl(): URL {
		return $this->url;
	}

    /**
	 * Returns variable provided to the script via URL query ($_GET).
	 * If no key is passed, returns the entire array.
     * If key does not exists, returns null.
     * @param string|null $key The key of $_GET.
	 * @return mixed
	 */
	public function getQuery(string $key = null) {
		if ($key === null) {
			return explode('?', $_SERVER["REQUEST_URI"], 2)[1] ?? null;
        }
    
		return isset($_GET[$key]) ? ($this->htmlEscape ? htmlspecialchars($_GET[$key]) : $_GET[$key]) : null;
    }
    
    /**
	 * Returns variable provided to the script via POST method ($_POST).
	 * If no key is passed, returns the entire array.
     * @param string|null $key The key of $_POST.
	 * @return mixed
	 */
	public function getPost(string $key = null) {
		if ($key === null) {
			return $_POST;
		}
		return isset($_POST[$key]) ? ($this->htmlEscape ? htmlspecialchars($_POST[$key]) : $_POST[$key]) : null;
    }
    

    /**
	 * Returns HTTP request method (GET, POST, HEAD, PUT, ...).
     * @return string The HTTP request metod.
	 */
	public function getMethod(): string {
		return $this->method;
    }
    
    /**
	 * Checks if the request method is the given one.
     * @param string $method The HTTP method (case insensitive).
     * @return boolean Returns true, if $method is equaled to current method, false otherwise.
	 */
	public function isMethod(string $method): bool {
		return strcasecmp($this->method, $method) === 0;
    }
    
    /**
	 * Return the value of the HTTP header. Pass the header name as the
	 * plain, HTTP-specified header name (e.g. 'Accept-Encoding').
     * @param $header The header key.
     * @return string|null The key value of header, null if the key does not exists.
	 */
	public function getHeader(string $header): ?string {
		$header = strtolower($header);
		return $this->headers[$header] ?? null;
    }
    
	/**
	 * Returns all HTTP headers.
     * @return array Header array.
	 */
	public function getHeaders(): array {
		return $this->headers;
    }
    
	/**
	 * Returns referrer.
     * @return string|null Returns referer or null if not referer is set.
	 */
	public function getReferer(): ?string {
		return isset($this->headers['referer']) ? $this->headers['referer'] : null;
    }
    
	/**
	 * Is the request over HTTPS?
     * @return boolean Returns true if request is over HTTPS, false otherwise.
	 */
	public function isSecured(): bool {
		return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? true : false;
    }
    
	/**
	 * Is AJAX request?
     * @return boolean Returns true if request is AJAX request, false otherwise.
	 */
	public function isAjax(): bool {
		return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }
    
	/**
	 * Returns the IP address of the remote client.
     * @return string|null The client IP address or null.
	 */
	public function getRemoteAddress(): ?string {
		return $this->remoteAddress;
    }
    
	/**
	 * Parse Accept-Language header and returns preferred language.
	 * @param array $langs All languages supported by your site, e.g. [cz, en, sk] 
     * @return string|null Returns the best language or null if your site does not support any of user's accepted languages.
	 */
	public function detectLanguage(array $langs): ?string {
		$header = $this->getHeader('Accept-Language');
		if (!$header) {
			return null;
		}
		$s = strtolower($header);
		$s = strtr($s, '_', '-');
		rsort($langs);
		preg_match_all('#(' . implode('|', $langs) . ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#', $s, $matches);
		if (!$matches[0]) {
			return null;
		}
		$max = 0;
		$lang = null;
		foreach ($matches[1] as $key => $value) {
			$q = $matches[2][$key] === '' ? 1.0 : (float) $matches[2][$key];
			if ($q > $max) {
				$max = $q;
				$lang = $value;
			}
		}
		return $lang;
    }

    /**
     * Return most preferred language.
     * @return string Return string containing most preferred language, e.g. 'cz', 'en' (lower cased), or null if header does not define the preferred language.
     */
    public function getMostPreferredLanguage(): ?array {
        $header = $this->getHeader('Accept-Language');
		if (!$header) {
			return null;
        }
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
		$lang = strtolower($lang);
		$lang = explode('-', $lang)[0];
        return $lang == "cs" ? "cz" : $lang;
    }
    
    /**
     * Checks if are all of $vars isset()
     * @param string $method The HTTP method (GET or POST). Case insensitive.
     * @param array $vars The array containing all keys.
     * @return boolean Returns true, if all keys are set (isset()), false otherwise. Also returns false if you enter different method from GET or POST.
     */
    public function workWith(string $method, array $vars): bool {
        $method = strtolower($method);
        if($method == "get") {
            foreach ($vars as $var) {
                if($this->getQuery($var) === null) {
                    return false;
                }
            }
            return true;
        } else if($method == "post") {
            foreach ($vars as $var) {
                if($this->getPost($var) === null) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}