<?php
class Mail {
    private $subject = "";
    private $message = "";
    private $headers = "";


    public function __construct() {
        $url = parse_url($GLOBALS["CONFIG"]["basic"]["APP_URL"])["host"];
        $this->headers =    'From: info@'. $url . "\r\n" .
                            'Reply-To: info@'. $url . "\r\n" .
                            'MIME-Version: 1.0' . "\r\n" . 
                            'Content-type: text/html; charset=windows-1250' . "\r\n" . 
                            'X-Mailer: PHP/' . phpversion();
    }

    public function subject($s) {
        $this->subject = $s;
    }

    public function message($m) {
        $this->message = $m;
    }

    public function headers($h) {
        $this->headers = $h;
    }

    public function send($reciever) {
        mail($reciever, $this->subject, $this->message, $this->headers);
    }
}