<?php
class PDB_Middlewares implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Middlewares for current route">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Middlewares</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Current middlewares</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.(Route::getMiddlewares(Route::$CURRENT_ROUTE_INFO["route"]) != null ? implode(", ", Route::getMiddlewares(Route::$CURRENT_ROUTE_INFO["route"])) : "No middleware set").'
            </div>
            </div>';
	}
}