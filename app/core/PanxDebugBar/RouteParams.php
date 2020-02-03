<?php
class PDB_RouteParams implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Route parameters">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Route Params</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "";
		foreach (Route::getValues() as $key => $value) {
			$c .= "<tr><td><b>{$key}:</b></td><td>". (is_array($value) ? implode(", ", $value) : $value) . "</td></tr>";
		}
		return '<h1>Route parameters</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No parameters set' : '<table style="width: 100%;">
					'.$c.'
				</table>') . '                
            </div>
            </div>';
	}
}