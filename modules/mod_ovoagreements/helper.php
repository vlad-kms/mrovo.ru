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

//define('DS', DIRECTORY_SEPARATOR); 
//require_once JPATH_LIBRARIES.DS.'avvlib.php';

jimport('joomla.log.logger.formattedtext');
 
class ModOvoAgreementsHelper
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
        $cat='getData';
		$wordSec=AvvCommon::getOvoSecurityWord($params);
		AvvLog::logMsg(['msg'=>['function getData ==============================================================================='], 'category'=>$cat], (boolean)$params['IsLog'], NULL, 'ovoagreements.log' );
		if ( !$user->guest) {
			// запрос к 1с за данными по присоединенным договорам
			$url=$params->get("url1c").'?iduser='.$user->id.'&paramsecuryallow='.$wordSec;
			AvvLog::logMsg(['msg'=>
				[
				 'url='.$url,
				 'user.id='.$user->id,
				 'user.name='.$user->name,
				 'user.username='.$user->username
				 ],
				'category'=>$cat], (boolean)$params['IsLog'], NULL, 'ovoagreements.log' );
			$dataAgr = file_get_contents ($url);
			$dataAgr = AvvCommon::deleteBOM($dataAgr);
			if ( $dataAgr ) {
				$xml = simplexml_load_string($dataAgr);
			}
			AvvLog::logMsg(['msg'=>['dataAgr='.$dataAgr], 'category'=>$cat], (boolean)$params['IsLog'], NULL, 'ovoagreements.log' );
			$result = $xml;
		} else {
			$result = null;
		}
		//AvvLog::logMsg(['msg'=>['$_SERVER:', $_SERVER],	'category'=>$cat], (boolean)$params['IsLog'], NULL, 'ovoagreements.log');
		AvvLog::logMsg(['msg'=>['function getData END ==========================================================================='], 'category'=>$cat], (boolean)$params['IsLog'], NULL, 'ovoagreements.log' );
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

	public static function getInvoiceAjax() {
		/*
			?ls=1001001&modid=99&userid=598&Itemid=2
		*/
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$modid = $app->input->getInt('modid', 0);
		$conf = ModOvoAgreementsHelper::__getConfigForModule($modid);
		$agrnum = rawurlencode($app->input->getVar('ls', '', 'string'));
		$islog = (boolean)$conf['IsLog'];
		$inpUserid = $app->input->getInt('userid', 0);
		$userid=$user->id;
		$wordSec=AvvCommon::getOvoSecurityWord($conf);
		//$config = new JConfig;
		//echo $user->id . '---' . $user->name;
		$result = "Пользователь не авторизован";
		AvvLog::logMsg(['msg'=>
			['function getInvoice ==========================================================================='],
			'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
		if ( $user->guest ) {
			AvvLog::logMsg(['msg'=>
				[
					'Пользователь не авторизован на сайте (гость).'
				],
				'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
		} elseif ($inpUserid <=0) {
			AvvLog::logMsg(['msg'=>
				[
					'inpUserid: '.$inpUserid,
					'userid   : '.$user->id,
					'Пользователь (переданный с броузера) '.$inpUserid.' не может быть числом неположительным.'
				],
				'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
		} elseif ($inpUserid != $userid) {
			AvvLog::logMsg(['msg'=>
				[
					'inpUserid: '.$inpUserid,
					'userid   : '.$user->id,
					'Пользователь (переданный с браузера) '.$inpUserid.' не совпадает с зарегистрированным на сайте '.$userid
				],
				'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
		} elseif (is_null($agrnum) || ($agrnum == '') ) {
			$result='Неверный лицевой счет (номер договора) '.$agrnum;
			AvvLog::logMsg(['msg'=>
				[
					'agrnum: '.$agrnum,
					'Передан неверный лицеовй счет (номер договора) '.$agrnum.'.'
				],
				'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
		} else {
			$urlSI = $conf->get('url1cinvoice', '');
			$urlSI=$urlSI . '?ls='.$agrnum.'&userid='.$userid.'&paramsecuryallow='.$wordSec;
			//$fn2 = DS.'tmp'.DS.$agrnum.'.pdf';
			//$tfn2 = JPATH_ROOT.$fn2;
			$tfn = tempnam(JPATH_ROOT.'/tmp', 'inv'.$agrnum);
			unlink($tfn);
			$tfn = $tfn.'.pdf';
			$fn = '/tmp/'.pathinfo($tfn, PATHINFO_BASENAME);
			$dataAgr = file_get_contents ($urlSI);
			AvvLog::logMsg(['msg'=>
				[
					'$_SERVER[SERVER_ADDR]: '.$_SERVER['SERVER_ADDR'],
					'$_SERVER[SERVER_NAME]: '.$_SERVER['SERVER_NAME'],
					'urlSI:  '.$urlSI,
					'agrnum: '.$agrnum,
					'modid:  '.$modid,
					'userid: '.$userid,
					//'dataAgr: '.$dataAgr,
					'tfn:    '.$tfn,
					'fn(url file on site): '.$fn
				],
				'category'=>'getInv'], $islog, NULL, 'ovoagreements.log' );
			file_put_contents($tfn, $dataAgr);
			$result = $fn;
		}
		AvvLog::logMsg(['msg'=>['function getInvoice END ======================================================================='], 'category'=>'getInv'],
			$islog, NULL, 'ovoagreements.log' );
		return $result;
	}
	
	public static function delFileAjax() {
		/*
			?fn=filenamedelete&modid=99&userid=598&Itemid=2
		*/
		$app = JFactory::getApplication();
		$modid = $app->input->getInt('modid', 0);
		$conf = ModOvoAgreementsHelper::__getConfigForModule($modid);
		$islog = (boolean)$conf['IsLog'];
		$cat='delFile';
		AvvLog::logMsg(['msg'=>'function delFile ===========================================================================', 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		$user = JFactory::getUser();
		
		if ( !$user->guest) {
			$inpUserid = $app->input->getInt('userid', 0);
			if ($inpUserid != $user->id) {
				AvvLog::logMsg(['msg'=>'Error. Переданный ID пользователя не соответствует зарегестрированному на сайте', 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
				return "ERROR";
			}
			AvvLog::logMsg(['msg'=>
				[
					'inpUserid: '.$inpUserid,
					'user->id: '.$user->id
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
			$fn = rawurlencode($app->input->getVar('fn', '', 'string'));
			AvvLog::logMsg(['msg'=>
				[
					'fn: '.$fn,
					'modid: '.$modid,
					//'inpUserid:'.$inpUserid,
					//'conf: '.$conf,
					'file for delete: '.JPATH_ROOT.$fn
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
			unlink(JPATH_ROOT.'/tmp/'.$fn);
		}	
		AvvLog::logMsg(['msg'=>'function delFile END =======================================================================', 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
	}
	
	public static function removeLinkAjax() {
		/*
			?ls=1234567&modid=99&userid=598&Itemid=2&action=0
			action =0-открепить; =1-удалить
		*/
		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$result = "";
		$agrnum = rawurlencode($app->input->getString('ls', ''));
		$userid = $app->input->getInt('uid', 0);
		$action = rawurlencode($app->input->getString('action', '0'));
		$modid  = $app->input->getInt('modid', 0);
		$conf   = ModOvoAgreementsHelper::__getConfigForModule($modid);
		$urlSI  = $conf->get('url1cremovelink', '');
		$islog  = (boolean)$conf['IsLog'];
		$wordSec=AvvCommon::getOvoSecurityWord($conf);
		AvvLog::logMsg(['msg'=>
			[
				'function removeLink ===========================================================================',
				'urlSI: '.$urlSI,
				'agrnum: '.$agrnum,
				'modid: '.$modid,
				'wordSec: '.$wordSec,
				'userid: '.$userid
			],
			'category'=>'remLnk'], $islog, NULL, 'ovoagreements.log'
		);
/**/
		if ( !$user->guest) {
			if ( !(empty($urlSI) or empty($agrnum) or empty($userid)) ) {
				$urlSI = $urlSI.'?action='.$action.'&iduser='.$userid.'&ls='.$agrnum.'&paramsecuryallow='.$wordSec;
				if ( ($userid > 0) && ($userid == $user->id) ) {
					AvvLog::logMsg(['msg'=>['urlSI: '.$urlSI], 'category'=>'remLnk'], $islog, NULL, 'ovoagreements.log');
					$result = file_get_contents ($urlSI);
				} else {
					AvvLog::logMsg(['msg'=>['Пользователь или не существует, или не зарегистрирован на сайте, или зарегистрирован другой'], 'category'=>'remLnk'], $islog, NULL, 'ovoagreements.log');
					$result = "Запрос запрещен";
				}
			}
		} // if ( !$user->guest)
/**/
		AvvLog::logMsg(['msg'=>['function removeLink END ======================================================================='], 'category'=>'remLnk'], $islog, NULL, 'ovoagreements.log');
		return $result;
	} // function removeLinkAjax
	
	/*
		Добавить пользователя
	*/
	public static function addUserFrom1CAjax() {
		/*
			modid=99 - id модуля
			&userid=598&Itemid=2&action=0
			?ls=1234567&modid=99&userid=598&Itemid=2&action=0
			action =0-открепить; =1-удалить
		*/
		$app = JFactory::getApplication();
		$modid = $app->input->getInt('modid', 99);
		$conf = ModOvoAgreementsHelper::__getConfigForModule($modid);
		$islog = $conf['IsLog'];
		$user = JFactory::getUser();
		//$glPost=$app->input->getArray($_POST);
		$glPost=$app->input->post->getArray();
		$c = count($app->input->get);
		$cat='addUser';
		AvvLog::logMsg(['msg'=>
				[
					'function addUserFrom1C ===========================================================================',
					'modid: '.$modid,
					'islog: '.$islog,
					'input.count: '.$app->input->count,
					'input->method: '.$app->input->getStr("method"),
					'count(input->get) : '.$c,
					'===================================='
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		foreach ($g as $key => $value) {
			AvvLog::logMsg(['msg'=>$key.' = '.$value, 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		}
		AvvLog::logMsg(['msg'=>'====================================', 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		AvvLog::logMsg(['msg'=>
				[
					'count(input->post): '.count($glPost),
					'===================================='
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		foreach ($glPost as $key => $value) {
			AvvLog::logMsg(['msg'=>$key.' = '.$value, 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		}
		
		if ( $user->guest) {
			// Программная авторизация пользователя.
			$credentials = ['username'=>$g['loguser'], 'password'=>$g['logpassw']];
			//В этом массиве параметры авторизации! в данном случае это установка запоминания пользователя
			$options = array( 'remember'=>false );
			//выполняем авторизацию
/*
			if( JFactory::getApplication()->login( $credentials, $options )){
				$user = JFactory::getUser();
				//if ($islog) {
				//	$log->message='Вы успешно авторизированны';
				//	$jl->addEntry( $log );
				//	$log->message='user =====================================';
				//	$jl->addEntry( $log );
				//	$log->message='id :'.$user->id;
				//	$jl->addEntry( $log );
				//	$log->message='username :'.$user->username;
				//	$jl->addEntry( $log );
				//	$log->message='name :'.$user->name;
				//	$jl->addEntry( $log );
				//	$log->message='password :'.$user->password;
				//	$jl->addEntry( $log );
				//}
				AvvLog::logMsg(['msg'=>
					[
						'Вы успешно авторизированны',
						'id :'.$user->id,
						'username :'.$user->username,
						'password :'.$user->password
					],
					'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
			}
			// Программная авторизация пользователя. ОКОНЧАНИЕ
*/
		}
		$result = '{';
		$newUser = new JUser;
		$idG = (integer)$glPost['id'];
		if ($idG) {
            $newUser->id = $idG;
        }
		$userData = array(
			'name'      => $glPost['name'],
			'username'  => $glPost['login'],
			'password'  => $glPost['passw'],
			'password2' => $glPost['passw'],
			'email'     => $glPost['email'],
			//'requireReset' => 1,
			'groups'    => array( 2 )
		);
		$rb = $newUser->bind( $userData );
		AvvLog::logMsg(['msg'=>
				[
					'NEW USER ===========================',
					'newUser->id: '.$newUser->id,
					'newUser->name: '.$newUser->name,
					'newUser->password: '.$newUser->password,
					'newUser->password2: '.$newUser->password2,
					'res bind: '.$rb,
					'===================================='
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		
		$rs = $newUser->save();
		if ( $rs ) {
			$result .= '"mess":"User ' . $newUser->username . ' success registered!", "id":"'.(string)$newUser->id.'"';
			AvvLog::logMsg(['msg'=>
				[
					'Успешно зарегистрирован: '.$newUser->username,
					'newUser->id: '.$newUser->id
				],
				'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		} else {
			$result .= '"mess":"User ' . $newUser->username . ' NOT success registered!", "id":"0"';
			AvvLog::logMsg(['msg'=>'Не зарегистрирован: '.$newUser->username, 'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		}
		AvvLog::logMsg(['msg'=>'function addUserFrom1C END =======================================================================',
			'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
		$result .='}';
		return $result;
	} // function addUserFrom1C
	
	public static function dispatch1cserviceAjax(){
		$app = JFactory::getApplication();
	}
}
