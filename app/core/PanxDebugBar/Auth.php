<?php
class PDB_Auth implements Tracy\IBarPanel
{
	public function getTab()
	{
		return '<span title="Auth">
            <!--<svg>....</svg>-->
            <span class="tracy-label">Auth</span>
        </span>';
	}

	public function getPanel()
	{
        global $auth;
        if(!$auth->isLogined()) {
            return '<h1>Auth</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                Not logined<br>
                <a href="'.Route::alias('login').'">Login</a><br>
                <a href="'.Route::alias('register').'">Register</a>
            </div>
            </div>';
        }
		return '<h1>Auth</h1>
            <div class="tracy-inner">
            <div class="tracy-inner-container">
                <table>
                    <tr>
                        <td><b>ID:</b></td>
                        <td>'.$auth->user('id').'</td>
                    </tr>
                    <tr>
                        <td><b>USERNAME:</b></td>
                        <td>'.$auth->user('name').'</td>
                    </tr>
                    <tr>
                        <td><b>EMAIL:</b></td>
                        <td>'.$auth->user('mail').'</td>
                    </tr>
                    <tr>
                        <td><b>VERIFIED:</b></td>
                        <td>'.($auth->user('verified') ? "true" : "false").'</td>
                    </tr>
                    <tr>
                        <td><b>ROLE:</b></td>
                        <td>'.$auth->user('role').'</td>
                    </tr>
                    <tr>
                        <td><b>PERMISSIONS:</b></td>
                        <td>'.str_replace("|", ", ", $auth->user('permissions')).'</td>
                    </tr>
                    <tr>
                        <td><b>LAST EDIT:</b></td>
                        <td>'.$auth->user('edited_at').'</td>
                    </tr>
                </table>
                <a href="'.Route::alias('edit').'">Edit</a><br>
                <a href="'.Route::alias('logout').'">Logout</a>
            </div>
            </div>';
	}
}