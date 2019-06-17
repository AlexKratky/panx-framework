<?php
class TextTable
{
    private $data;

    private $cs = array();

    private $rs = array();

    private $keys = array();

    private $mH = 2;

    private $mW = 100;

    private $head = false;
    private $pcen = "+";
    private $prow = "-";
    private $pcol = "|";

    public function __construct($data) {
        $this->data = &$data;
        $this->cs = array();
        $this->rs = array();

        if (!$xc = count($this->data)) {
            return false;
        }

        $this->keys = array_keys($this->data[0]);
        $columns = count($this->keys);

        for ($x = 0; $x < $xc; $x++) {
            for ($y = 0; $y < $columns; $y++) {
                $this->setMax($x, $y, $this->data[$x][$this->keys[$y]]);
            }
        }

    }

    public function showHeaders($bool) {
        if ($bool) {
            $this->setHeading();
        }

    }

    public function setMaxWidth($maxWidth) {
        $this->mW = (int) $maxWidth;
    }

    public function setMaxHeight($maxHeight)
    {
        $this->mH = (int) $maxHeight;
    }

    public function render($return = false) {
        if ($return) {
            ob_start(null, 0, true);
        }

        $this->printLine();
        $this->printHeading();

        $rc = count($this->data);
        for ($i = 0; $i < $rc; $i++) {
            $this->printRow($i);
        }

        $this->printLine(false);
        print("\n");
        if ($return) {
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
    }

    private function setHeading() {
        $data = array();
        foreach ($this->keys as $colKey => $value) {
            $this->setMax(false, $colKey, $value);
            $data[$colKey] = strtoupper($value);
        }
        if (!is_array($data)) {
            return false;
        }

        $this->head = $data;
    }

    private function printLine($nl = true) {
        print $this->pcen;
        foreach ($this->cs as $key => $val) {
            print $this->prow .
            str_pad('', $val, $this->prow, STR_PAD_RIGHT) .
            $this->prow .
            $this->pcen;
        }

        if ($nl) {
            print "\n";
        }

    }

    private function printHeading() {
        if (!is_array($this->head)) {
            return false;
        }

        print $this->pcol;
        foreach ($this->cs as $key => $val) {
            print ' ' .
            str_pad($this->head[$key], $val, ' ', STR_PAD_BOTH) .
            ' ' .
            $this->pcol;
        }

        print "\n";
        $this->printLine();
    }

    private function printRow($rowKey) {
        // loop through each line
        for ($line = 1; $line <= $this->rs[$rowKey]; $line++) {
            print $this->pcol;
            for ($colKey = 0; $colKey < count($this->keys); $colKey++) {
                print " ";
                print str_pad(substr($this->data[$rowKey][$this->keys[$colKey]], ($this->mW * ($line - 1)), $this->mW), $this->cs[$colKey], ' ', STR_PAD_RIGHT);
                print " " . $this->pcol;
            }
            print "\n";
        }
    }

    private function setMax($rowKey, $colKey, &$colVal) {
        $w = mb_strlen($colVal);
        $h = 1;
        if ($w > $this->mW) {
            $h = ceil($w % $this->mW);
            if ($h > $this->mH) {
                $h = $this->mH;
            }

            $w = $this->mW;
        }

        if (!isset($this->cs[$colKey]) || $this->cs[$colKey] < $w) {
            $this->cs[$colKey] = $w;
        }

        if ($rowKey !== false && (!isset($this->rs[$rowKey]) || $this->rs[$rowKey] < $h)) {
            $this->rs[$rowKey] = $h;
        }

    }
}