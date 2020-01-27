<?php
class PDB_Variables implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Vysvětlující popisek">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Variables</span>
        </span>';
	}

	public function getPanel()
	{
        //d(array_keys($GLOBALS));
		return '<h1>Titulek</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                obsah
            </div>
            </div>';
	}
}