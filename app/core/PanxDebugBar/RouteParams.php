<?php
class PDB_RouteParams implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Vysvětlující popisek">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Route Params</span>
        </span>';
	}

	public function getPanel()
	{
		return '<h1>Titulek</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                obsah
            </div>
            </div>';
	}
}