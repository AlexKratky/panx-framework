<?php
class PDB_Mail implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Sent mails">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Mail</span>
        </span>';
	}

	public function getPanel()
	{
        $c = "";
		foreach ($GLOBALS["sent_mails"] as $key) {
			$c .= "<tr><td>{$key[0]}</td><td>{$key[1]}</td><td>{$key[2]}</td><td>{$key[3]}</td></tr>";
		}

		return '<h1>Sent mails</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                '  . ($c == "" ? 'No mails sent' : '<table><tr><th>Reciever</th><th>Subject</th><th>Message</th><th>Headers</th></tr>
					'.$c.'
				</table>') . '
            </div>
            </div>';
	}
}