<?php
class PDB_Cookies implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Cookies">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Cookies</span>
        </span>';
	}

	public function getPanel()
	{
		$c = "";
		foreach ($_COOKIE as $key => $value) {
			$c .= "<tr><td><b>{$key}:</b></td><td>{$value}</td></tr>";
		}

		return '<h1>Cookies</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
				'  . ($c == "" ? 'No cookies set' : '<table>
					'.$c.'
				</table>') . '
            </div>
            </div>';
	}
}