<?php
/**
 * @name Pagination.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to split data into several pages. Part of panx-framework.
 */

class Pagination {
    private $data = array();
    public $currentPage = 1;
    public $totalPages = 1;
    public $perPage = 10;

    public function __construct($data, $perPage = 10) {
        $this->data = $data;
        $this->perPage = $perPage;
        $this->currentPage = (Route::getValue("PAGE") !== false ? (int)Route::getValue("PAGE") : ($GLOBALS["request"]->getQuery("page") ?? 1));
    
        $this->totalPages  = count($data)/$perPage;
        if(is_float($this->totalPages)) {
            $this->totalPages = (floor($this->totalPages)+1);
        }
    }

    public function getData() {
        $start = (($this->currentPage-1) * $this->perPage);
        $max = $this->perPage + (($this->currentPage-1) * $this->perPage);
        $res = array();
        for ($i = $start; $i < $max; $i++) {
            if(isset($this->data[$i]))
                array_push($res, $this->data[$i]);
        }
        return $res;
    }

    public function totalPages() {
        return $this->totalPages;
    }

    public function currentPage() {
        return $this->currentPage;
    }

    public function previousPage() {
        return ($this->currentPage - 1 < 1 ? false : $this->currentPage - 1);
    }

    public function nextPage() {
        if ($this->currentPage + 1 > $this->totalPages) {
            return false;
        } else {
            return $this->currentPage + 1;
        }
    }


    public static function infinityScroll($URI = null, $callback = null) {
        if($URI === null) {
            $URI = $GLOBALS["request"]->getUrl()->getString() . "/load/";
        }
        echo '<script src="/res/js/InfinityScroll.js"></script>';
        echo '<script>initInfinityScroll("'.$URI.'");</script>';
    }
}
