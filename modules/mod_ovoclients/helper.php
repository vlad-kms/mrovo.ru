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

//JLoader::import('joomla.user.helper');

class ModOvoClientsHelper
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getHello($params)
    {
        $user = JFactory::getUser();
        $result='';
        if (! $user->guest) {
            $result .= '<div class="ovouser">';
            $result .= "<p>Login: $user->username</p>";
            //$result .= "<p>Имя: $user->name</p>";
            $result .= "<p>E-mail: $user->email</p>";
//            JLoader::import('joomla.user.helper');
            $userprofile = JUserHelper::getProfile();
            $result .= "<p>Телефон: ".$userprofile->profile['phone']."</p>";
            $result .= '</div>';
        }
//	    echo "<pre>";
//	    echo $params;
//	    echo $plang;
//	    var_dump($user);
//      echo "</pre>";
	    return $result;
    }
    
    public static function getMenu($params)
    {
        $user = JFactory::getUser();
        if (! $user->guest) {
//            $result = '<ul class="sp-dropdown-items">';
//            $result .= '<li class="sp-menu-item"><a href="">Привязать договор</a></li>';
//            $result .= '<li class="sp-menu-item"><a href="#">Список договоров</a></li>';
//            $result .= "</ul>";
            $result = '<nav class="menu_nav">';
            $result .= '<div class="div">';
            $result .= '<a href="">Привязать договор</a>';
            $result .= '</div>';
            $result .= '<div class="div">';
            $result .= '<a href="'.$params->get('pagelistagreements', '/').'">Список договоров</a>';
            $result .= '</div>';
            $result .= '</nav>';
        } else {
            $result = "";
        }
        return $result;
    }
    
	public static function getData($params)
	{
		$user = JFactory::getUser();
		if ($user->guest) {
			$result = NULL;
		} else {
			$userprofile = JUserHelper::getProfile();
			$result = array(
				'name' => $user->name,
				'username' => $user->username,
				'fullname' => $user->fullname,
				'userid'   => $user->id,
				'mail' => $user->email,
				'userphone' => $userprofile->profile['phone']
			);
		}
		return $result;
	}
    
    private static function getConfigForModule($modid)
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
    
	public static function linkAgreementAjax() {
		$user = JFactory::getUser();
		if ( $user->guest) {
			$result = "Не зарегистрированный пользователь";
		} else {
			$app = JFactory::getApplication();
            $agrnum = rawurlencode($app->input->getString('ls', ''));
            $modid  = $app->input->getInt('modid', 0);
            $userid = $app->input->getInt('uid', 0);
            $username = rawurlencode($user->name);
            $userlogin = rawurlencode($user->username);
            $useremail = rawurlencode($user->email);
			$userprofile = JUserHelper::getProfile();
			$userphone = rawurlencode($userprofile->profile['phone']);
            
			$conf = ModOvoClientsHelper::getConfigForModule($modid);
			$url=$conf['url1clinkagreement'];
			$islog = (boolean)$conf['IsLog'];
			$cat='lnkAgr';
			$wordSec=AvvCommon::getOvoSecurityWord($conf);
			AvvLog::logMsg(['msg'=>
				[
					'function linkAgreement ===============================================================================',
					'agrnum='.$agrnum,
					'modid='.$modid,
					'userid='.$userid,
					'url: '.$url,
					'email: '.$useremail,
					'user.id: '.$user->id,
					'user.mail: '.$user->email,
					'user.name='.$user->name,
					'user.username='.$user->username,
					'phone (user->profile->phone): '.$userphone,
					'wordSec: '.$wordSec,
					'gettype(wordSec):'.gettype($wordSec),
					'gettype(conf):'.gettype($conf),
					'conf: '.$conf
				],
				'category'=>$cat], $islog, NULL, 'ovoclients.log' );
			if ($userid != $user->id) {
				$result = "Запрос запрещен";
			}
            elseif ( !(empty($url) or empty($agrnum) or empty($userid)) ) {
                $urlSI = $url.'?ls='.$agrnum.'&iduser='.$userid.'&phone='.$userphone.'&email='.$useremail.'&username='.$username.'&login='.$userlogin.'&paramsecuryallow='.$wordSec;
				$result = file_get_contents ($urlSI);
			}
			else {
				$result = "Запрос запрещен";
			}
			AvvLog::logMsg(['msg'=>
				[
					'urlSI='.$urlSI,
					'result: '.$result,
					'function linkAgreement END ==========================================================================='
				],
				'category'=>$cat], $islog, NULL, 'ovoclients.log' );
		}
		return $result;
	}

}
