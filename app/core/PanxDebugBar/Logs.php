<?php
class PDB_Logs implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Logs">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Logs</span>
        </span>';
	}

	public function getPanel()
	{   
        $p = $_SERVER["DOCUMENT_ROOT"].'/../logs/';
        $f = scandir($p);
        $c = "";
        foreach ($f as $file) {
            if($file == "." || $file == "..") continue;
            $c .= "<h2 style='font-size: 22px'>$file</h2>";
            $l = shell_exec('tail -6 '.$p.$file);
            $l = explode(PHP_EOL, $l);
            $c .= "<table>";
            foreach ($l as $v) {
                $v = trim($v);
                if($v == "") continue;
                $c .= "<tr><td>$v</td></tr>";
            }
            $c .= "</table>";
        }
        //d(shell_exec('tail -5 '.$_SERVER["DOCUMENT_ROOT"].'/../logs/access.log'));
		return '<h1>Logs</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No logs files found' : $c) . '
            </div>
            </div>';
	}
}