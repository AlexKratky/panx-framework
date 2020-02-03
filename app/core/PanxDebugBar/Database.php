<?php
class PDB_Database implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Database">
            <!--<svg>....</svg>-->
            <span class="tracy-label">db</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Database</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                obsah
            </div>
            </div>';
	}
}