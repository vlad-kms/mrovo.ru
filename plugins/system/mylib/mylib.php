<?php
/**
 * @package     Mylib plugin
 * @author      Dmitry Rekun
 * @copyright   Copyright (C) 2013 Dmitry Rekun. All rights reserved.
 * @license     GNU General Public License version 3 or later.
 */

defined('_JEXEC') or die;

/**
 * Mylib plugin class.
 *
 * @package  Mylib plugin
 */
class plgSystemMylib extends JPlugin
{
	/**
	 * Method to register custom library.
	 *
	 * return  void
	 */
	public function onAfterInitialise()
	{
		JLoader::registerPrefix('Avv', JPATH_LIBRARIES.'/avv');
		$_SERVER['OVO_SECURITYWORD']='WordSecGuard';
	}
}
