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
        //d($GLOBALS["database_queries"]);
        $c = "<table>";
        $c .= "<tr>
                <th>Query</th>
                <th>Parmas</th>
        </tr>";
        foreach ($GLOBALS["database_queries"] as $q) {
            $c .= "<tr>
                    <td>{$q[0]}</td>
                    <td>" . implode(", ", $q[1]) . "</td>
            </tr>";
        }
        $c .= "</table>";
		return '<h1>Database</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                ' . $c . '
            </div>
            </div>';
	}
}