<?php
/**
 * Helper class for Hello World! module
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
class ModOvoBalanceHelper
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getData($params)
    {
		$user = JFactory::getUser();
		$config = new JConfig;
        $deb  = $config->debug;
        $debl = $config->debug_lang;
        $result='';
		if ( !$user->guest) {
			// запрос к 1с за данными по присоединенным договорам
			$url=$params->get("url1c").'?iduser='.$user->id;
			$dataAgr = file_get_contents ($url);
			$dataAgr = AvvCommon::deleteBOM($dataAgr);
			if ( $dataAgr ) {
				$xml = simplexml_load_string($dataAgr);
			}
			$result = $xml;
		} else {
			$result = null;
		}
		return $result;
	}
	
	private static function __getConfigForModule($modid)
	{
			// Module table
		$modTables = new  JTableModule(JFactory::getDbo());
			// Load module
		$modTables->load(array('id'=>$modid));
		$params = new JRegistry();
		$params->loadString($modTables->params);
		$param = $params->get('param', 'def');
		return $params;
	}
	
	public static function getBalanceAjax() {
		$user = JFactory::getUser();
		$result = "";
		if ( !$user->guest) {
			$app = JFactory::getApplication();
			$agrnum = rawurlencode($app->input->getString('ls', ''));
			$ye = $app->input->getString('yeare', '');
			$me = $app->input->getString('monthe', '');
			$de = $app->input->getString('daye', '');
			$yb = $app->input->getString('yearb', '');
			$mb = $app->input->getString('monthb', '');
			$db = $app->input->getString('dayb', '');

			$modid = $app->input->getInt('modid', 0);
			$conf = ModOvoBalanceHelper::__getConfigForModule($modid);
			$urlSI = $conf->get('url1cgetbalance', '');
			$userid=$user->id;
			//$result=$urlSI;
			$wordSec=AvvCommon::getOvoSecurityWord($conf);
			
			$islog = (boolean)$conf['IsLog'];
			$cat='getbalance';
			AvvLog::logMsg(['msg'=>
				[
					'function getBalanceAjax =============================================================================================================',
					'agrnum   :'.$agrnum,
					'modid    :'.$modid,
					'userid   :'.$userid,
					'urlSI    : '.$urlSI,
					'dateBegin: '.$datebegin,
					'dateEnd  : '.$dateend,
					'wordSec: '.$wordSec,
					'conf: '.$conf
				],
				'category'=>$cat], $islog, NULL, 'ovobalance.log' );
			// запрос
			if ($userid != $user->id) {
				$result = "Запрос запрещен";
			}
			elseif ( !(empty($urlSI) or empty($agrnum) or empty($userid)) ) {
				$urlSI = $urlSI.'?iduser='.$userid.'&ls='.$agrnum.'&yearb='.$yb.'&monthb='.$mb.'&dayb='.$db.'&yeare='.$ye.'&monthe='.$me.'&daye='.$de.'&paramsecuryallow='.$wordSec;
				$result = file_get_contents ($urlSI);
			}
			else {
				$result = "Запрос запрещен";
			}
			AvvLog::logMsg(['msg'=>
				[
					'urlSI after set params: '.$urlSI,
					//'result: '.$result,
					'function getBalanceAjax END ========================================================================================================='
				],
				'category'=>$cat], $islog, NULL, 'ovobalance.log'
			);
		}
		return $result;
	}
}
