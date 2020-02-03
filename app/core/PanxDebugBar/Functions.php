<?php
class PDB_Functions implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Functions">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Functions</span>
        </span>';
	}

	public function getPanel()
	{
        //d(get_defined_functions()["user"]);
        $f = get_defined_functions()["user"];
        $c = "";

        for($i = count($f) - 1; $i >= 0; $i--) {
            $r = new ReflectionFunction($f[$i]);
            $c .= "<tr><td style='font-size: 14px;'>" . '<span style="color: #0040ff;">' . $f[$i] . "</span>(". $this->getParams($r, $f[$i]) . ")".$this->returnType($r)."</td></tr>";
        }
		return '<h1>Functions</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No custom functions found' : '<table>
					'.$c.'
				</table>') . '
            </div>
            </div>';
    }
    
    private function getParams($f, $name) {
        $x = "";
        $params = $f->getParameters();
        $i = 0;
        foreach ($params as $param) {
            $p = new ReflectionParameter($name, $i++);
            $x .= ($p->hasType() ? '<span style="color: #00ae68;">' .$p->getType() . "</span> ": "") . '<span style="color: #007bff;">' . $param->getName() . "</span>, ";
        }
        $x = rtrim($x, ", ");
        return $x;
    }

    private function returnType($f) {
        if($f->hasReturnType()) {
            return ": <span style='color: #00ae68;'>" . $f->getReturnType() . "</span>";
        } else {
            return ": <span style='color: #00ae68;'>mixed</span>";
        }
    }
}