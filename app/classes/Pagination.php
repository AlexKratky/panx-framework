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
    public $type;
    public $currentPage = 1;
    public $totalPages = 1;
    public $perPage = 10;

    public const DATA_ARRAY = "DATA_ARRAY";
    public const DATA_SQL = "DATA_SQL";
    public const DATA_FILE = "DATA_FILE";

    /**
     * Creates pagination from source.
     * @param mixed $data The data that will be splitted to pages. It can be array, file path or SQL query.
     * @param int $perPage Entries per page. If the source is array, then it will use elements. If the souce is SQL query, then it will use LIMIT. If the source is file, then it will use file lines. The SQL query is just 'FROM x (WHERE)'.
     * @param string $DATA_TYPE Determine the source type.
     */
    public function __construct($data, $perPage = 10, $DATA_TYPE = "DATA_ARRAY") {
        $this->data = $data;
        switch ($DATA_TYPE) {
            case self::DATA_ARRAY:
                $this->totalPages  = count($data)/$perPage;
                if(is_float($this->totalPages)) {
                    $this->totalPages = (floor($this->totalPages)+1);
                }
                break;
            case self::DATA_SQL:
                $this->totalPages  = db::count("SELECT COUNT(*) ".$this->data, array())/$perPage;
                if(is_float($this->totalPages)) {
                    $this->totalPages = (floor($this->totalPages)+1);
                }
                break;
            case self::DATA_FILE:
                $this->totalPages = 0;
                $handle = fopen($data, "r");
                while (!feof($handle)) {
                    $line = fgets($handle);
                    $this->totalPages++;
                }

                fclose($handle);
                $this->totalPages = $this->totalPages / $perPage;

                if (is_float($this->totalPages)) {
                    $this->totalPages = (floor($this->totalPages) + 1);
                }
                break;
        }
        $this->type = $DATA_TYPE;
        $this->perPage = $perPage;
        $this->currentPage = (Route::getValue("PAGE") !== false ? (int)Route::getValue("PAGE") : ($GLOBALS["request"]->getQuery("page") ?? 1));    
    }

    public function getData() {
        $start = (($this->currentPage-1) * $this->perPage);
        $max = $this->perPage + (($this->currentPage-1) * $this->perPage);
        $res = array();
        switch ($this->type) {
            case self::DATA_ARRAY:
                for ($i = $start; $i < $max; $i++) {
                    if(isset($this->data[$i]))
                        array_push($res, $this->data[$i]);
                }
                break;
            case self::DATA_SQL:
                //dump("SELECT * ".$this->data." WHERE ID>$start LIMIT {$this->perPage}", false);
                $x = db::multipleSelect("SELECT * ".$this->data." WHERE ID>$start LIMIT {$this->perPage}");
                foreach ($x as $v) {
                    array_push($res, $v);
                }
                break;
            case self::DATA_FILE:
                $spl = new SplFileObject($this->data);
                for ($i = $start; $i < $max; $i++) {
                    $spl->seek($i);
                    if($spl->current() !== false)
                        array_push($res, $spl->current());
                }
                break;
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
