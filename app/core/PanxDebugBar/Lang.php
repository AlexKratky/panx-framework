<?php
class PDB_Lang implements Tracy\IBarPanel
{

	public function getTab()
	{
		return '<span title="Missing translations">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Lang</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "";
        foreach ($GLOBALS["missing_translations"] as $key) {
            $c .= "<tr><td><b>{$key[0]}</b></td><td>{$key[1]}:{$key[2]}</td></td>";
        }
		return '<h1>Missing translations</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No missing translations found' : '<table style="width: 100%">
					'.$c.'
				</table>') . '
            </div>
            </div>';
	}
}