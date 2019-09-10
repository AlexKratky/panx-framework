<?php
/**
 * @name Mail.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to send mails. Part of panx-framework.
 */

declare(strict_types=1);
 
class Mail {
    /**
     * @var string The subject of mail.
     */
    private $subject = "";
    /**
     * @var string The message of mail.
     */
    private $message = "";
    /**
     * @var string The headers of mail.
     */
    private $headers = "";


    /**
     * Create instance of Mail and sets the defalt headers.
     */
    public function __construct() {
        $url = parse_url($GLOBALS["CONFIG"]["basic"]["APP_URL"])["host"];
        $this->headers =    'From: info@'. $url . "\r\n" .
                            'Reply-To: info@'. $url . "\r\n" .
                            'MIME-Version: 1.0' . "\r\n" . 
                            'Content-type: text/html; charset=windows-1250' . "\r\n" . 
                            'X-Mailer: PHP/' . phpversion();
    }

    /**
     * Sets the subject of mail.
     */
    public function subject(string $s) {
        $this->subject = $s;
    }

    /**
     * Sets the message of mail.
     */
    public function message(string $m) {
        $this->message = $m;
    }

    /**
     * Sets the headers of mail.
     */
    public function headers(string $h) {
        $this->headers = $h;
    }

    /**
     * Sends the mail using mail() function.
     */
    public function send($reciever) {
        mail($reciever, $this->subject, $this->message, $this->headers);
    }
}
