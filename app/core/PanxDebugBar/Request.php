<?php
class PDB_Request implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Request">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Request</span>
        </span>';
	}

	public function getPanel()
	{
        global $request;
        // headers, referer, is secured, get remote address, lang, getClientID
		return '<h1>Request</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                <table>
                    <tr>
                        <td><b>Headers</b></td>
                        <td>'.implode(", ", $request->getHeaders()).'</td>
                    </tr>
                    <tr>
                        <td><b>Referer</b></td>
                        <td>' . $request->getReferer() . '</td>
                    </tr>
                    <tr>
                        <td><b>Secured</b></td>
                        <td>' . ($request->isSecured() ? "true" : "false") . '</td>
                    </tr>
                    <tr>
                        <td><b>Remote address</b></td>
                        <td>' . $request->getRemoteAddress() . '</td>
                    </tr>
                    <tr>
                        <td><b>Preferred language</b></td>
                        <td>' . $request->getMostPreferredLanguage() . '</td>
                    </tr>
                    <tr>
                        <td><b>Client ID</b></td>
                        <td>' . $request->getClientID() . '</td>
                    </tr>
                </table>
            </div>
            </div>';
	}
}