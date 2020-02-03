<?php
//require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/ActualRoute.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Alias.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Auth.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Controller.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Cookies.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Database.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Functions.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Lang.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Logs.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Mail.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Middlewares.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Redirects.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Request.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/RouteParams.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Routes.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Session.php";
// require_once $_SERVER['DOCUMENT_ROOT']."/../app/core/PanxDebugBar/Variables.php";

use Tracy\Debugger;
if(empty($GLOBALS["CONFIG"]["debug"]["PDB_USERNAME"]) || $GLOBALS["CONFIG"]["debug"]["PDB_USERNAME"] == $GLOBALS["auth"]->user('name')) {
    Tracy\Debugger::getBar()->addPanel(new PDB_ActualRoute);
    Tracy\Debugger::getBar()->addPanel(new PDB_Alias);
    Tracy\Debugger::getBar()->addPanel(new PDB_Auth);
    Tracy\Debugger::getBar()->addPanel(new PDB_Controller);
    Tracy\Debugger::getBar()->addPanel(new PDB_Cookies);
    Tracy\Debugger::getBar()->addPanel(new PDB_Database);
    Tracy\Debugger::getBar()->addPanel(new PDB_Functions);
    Tracy\Debugger::getBar()->addPanel(new PDB_Lang);
    Tracy\Debugger::getBar()->addPanel(new PDB_Logs);
    Tracy\Debugger::getBar()->addPanel(new PDB_Mail);
    Tracy\Debugger::getBar()->addPanel(new PDB_Middlewares);
    Tracy\Debugger::getBar()->addPanel(new PDB_Redirects);
    Tracy\Debugger::getBar()->addPanel(new PDB_Request);
    Tracy\Debugger::getBar()->addPanel(new PDB_RouteParams);
    Tracy\Debugger::getBar()->addPanel(new PDB_Routes);
    Tracy\Debugger::getBar()->addPanel(new PDB_Session);
    // Tracy\Debugger::getBar()->addPanel(new PDB_Variables);
}