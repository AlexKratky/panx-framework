<?php
class PDB_ActualRoute implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Actual route">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Route</span>
        </span>';
	}

	public function getPanel()
	{
            //d(Route::$CURRENT_ROUTE_INFO);
		return '<h1>Actual route</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.(Route::$CURRENT_ROUTE_INFO["route"]).'
            </div>
            </div>';
	}
}