<?php
class PDB_Alias implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Current alias">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Alias</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Alias</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.(Route::getAlias() ?? "No alias set").'
            </div>
            </div>';
	}
}