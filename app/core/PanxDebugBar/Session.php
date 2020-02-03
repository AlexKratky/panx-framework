<?php
class PDB_Session implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Session">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Session</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "";
		foreach ($_SESSION as $key => $value) {
            if($key == "_tracy") continue;
			$c .= "<tr><td><b>{$key}:</b></td><td>". (is_array($value) ? implode(", ", $value) : $value) . "</td></tr>";
		}
		return '<h1>Session</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No session set' : '<table>
					'.$c.'
				</table>') . '
            </div>
            </div>';
	}
}