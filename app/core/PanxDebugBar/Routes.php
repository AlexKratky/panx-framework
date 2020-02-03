<?php
class PDB_Routes implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Route list">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Routes</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "<table>";
        $c .= "<tr>
                <th>TYPE</th>
                <th>URI/CODE</th>
                <th>ACTION</th>
                <th>LOCK</th>
                <th>MIDDLEWARES</th>
                <th>CONTROLLER</th>
                <th>ALIAS</th>
                <th>API ENDPOINT</th>
                <th>R_P_G</th>
                <th>R_P_P</th>
                <th>R_P_E</th>
        </tr>";
        $x = Route::getDataTable();
        foreach ($x as $route) {
            $c .= "<tr>
                    <td>{$route['TYPE']}</td>
                    <td>{$route['URI/CODE']}</td>
                    <td>{$route['ACTION']}</td>
                    <td>{$route['LOCK']}</td>
                    <td>{$route['MIDDLEWARES']}</td>
                    <td>{$route['CONTROLLER']}</td>
                    <td>{$route['ALIAS']}</td>
                    <td>{$route['API_EP']}</td>
                    <td>{$route['R_P_G']}</td>
                    <td>{$route['R_P_P']}</td>
                    <td>{$route['R_P_E']}</td>
            </tr>";
        }
        $c .= "</table>";

		return '<h1>Route list</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '.$c.'
            </div>
            </div>';
	}
}