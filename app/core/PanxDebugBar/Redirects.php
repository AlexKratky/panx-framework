<?php
class PDB_Redirects implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Last 3 redirects">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Redirects</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "";
        for($i = 1; $i <= 3; $i++) {
            $c .= "<h2 style='font-size:22px'>Redirect $i</h2>";
            if(!isset($_SESSION["__redirect__$i"]) || $_SESSION["__redirect__$i"] == null) {
                continue;
            }
            $c .= "<table style='width: 100%'>";

            $c .= "<tr><td><b>From</b></td><td>".$_SESSION["__redirect__$i"]["current_url"]."</td></tr>";
            $c .= "<tr><td><b>To</b></td><td>".$_SESSION["__redirect__$i"]["redirect_to"]."</td></tr>";

            $c .= "</table>";
            if(count($_SESSION["__redirect__$i"]["post_params"]) > 0) {
                $c .= "<h3 style='font-size:16px'>POST:</h3>";
                $c .= "<table style='width: 100%'>";
                foreach ($_SESSION["__redirect__$i"]["post_params"] as $key => $value) {
                    if($key == "pass" || $key == "password") {
                        $value = "***** (Hidden)";
                    }
                    $c .= "<tr><td><b>{$key}</b></td><td>" . (is_array($value) ? implode(", ", $value) : $value) . "</td></tr>";
                }
                $c .= "</table>";
            } else {
                $c .= "No POST parameters";
            }
        }
		return '<h1>Redirects</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.$c.'
            </div>
            </div>';
	}
}