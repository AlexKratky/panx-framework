<?php
class PDB_Redirects implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Last redirects">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Redirects</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Redirects</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                obsah
            </div>
            </div>';
	}
}