<?php
/**
 * Hello World! Module Entry Point
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @license    GNU/GPL, see LICENSE.php
 * @link       http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

//$language = $params->get('lang', '1');
$data = modOvoClientsHelper::getData($params);
$hello = modOvoClientsHelper::getHello($params);
$menu = modOvoClientsHelper::getMenu($params);
$module = JModuleHelper::getModule('mod_ovoclients');

//echo "<pre>";
//echo $hello;
//echo "</pre>";

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

//require JModuleHelper::getLayoutPath('mod_ovoclients');
require JModuleHelper::getLayoutPath('mod_ovoclients', $params->get('layout', 'default'));