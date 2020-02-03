<?php
class PDB_Controller implements Tracy\IBarPanel
{
	public function getTab()
	{
        return '<span title="Current controller">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Contoller</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Contoller</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.(Route::getController() ?? "No controller").'
            </div>
            </div>';
	}
}