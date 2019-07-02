<?php
/**
 * @name AuthMiddleware.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description The list of all handlers. Part of panx-framework.
 */

/**
 * Extension => Handler
 * If you will call your handler with pattern 'Extension'Handler (e.g. LatteHandler), you do not need to include then.
 */
$handlers = [
    "latte" => 'LatteHandler',
];