<?php
// No direct access
defined('_JEXEC') or die; ?>

<?php
$config = new JConfig;
$user = JFactory::getUser();
$lang  = JFactory::getLanguage();
$deb =   $config->debug;
$debl =   $config->debug_lang;
$itemId = '';
$activeMI = JFactory::getApplication()->getMenu()->getActive();
if ($activeMI) {
	$itemId = $activeMI->id;
	if( !empty($itemId) ) {
		$itemId = 'Itemid='.$itemId;
	}
}
if ( !$user->guest ) {
	// $params - параметры с конфига модуля
	// $data   - данные о пользователе (массив)
	// $user   - JUser
	
	$islog = (boolean)$params->get('IsLog', 0);
	if ( $islog ) {
		$cat = 'tmpl';
		AvvLog::logMsg(['msg'=>
			[
				'вызов шаблона (вывод модуля) ===============================================================================',
				'itemId='.$itemId,
				'user.username='.$user->username,
				'user.id='.$user->id,
				'user.name: '.$user->name,
				'вызов шаблона (вывод модуля) END ===========================================================================',
			],
			'category'=>$cat], $islog, NULL, 'ovoclients.log' );
		echo "<div class=\"debug\">";
/*
	    echo "<pre>";
//		var_dump($params);
	    echo "</pre>";
    	echo "<pre>";
	    var_dump($data);
    	echo "</pre>";
	    echo "<pre>";
//		var_dump($user);
	    echo "</pre>";
	    echo "<pre>";
//    	var_dump($config);
	    echo "</pre>";
//		$module = JModuleHelper::getModule('mod_ovoclients');
//		var_dump($module->id);
*/
		echo "</div>";
	}
?>

<div class="ovoclient">
<?php
// формирование и вывод блока информации о пользователе
	if ($params->get('showuserinfo', false)) {
		echo $hello;
	}
// формирование и вывод кнопок
//echo $menu;
?>
	<nav class="menu_nav">
		<div class="div">
			<a href="<?php echo $params->get('pagelistagreements', '/');?>" onclick="gotoListAgreement()">Список договоров</a>
<!--			<a href="<?php echo $params->get('pagelistagreements', '/');?>">Список договоров</a> -->
		</div>
		<div class="clear_avv line_avv"></div>
		<label  for="agr_number" class="hasPopover required invalid" title="" data-content="Введите, пожалуйста, лицевой счет (№ договора)" data-original-title="Лицевой счет (№ договора)">Лицевой счет (№ договора)<span class="star">&nbsp;*</span></label>
		<input type="text" name="agrnum" class="validate-email required invalid" id="agr_number" value="" required="required" aria-required="true">
		<div class="div" onclick="linkAgreement(<?php echo $module->id;?>, <?php echo $user->id;?>)">Привязать договор</div>
		<div class="clear_avv line_avv"></div>
		<div id="resultlink" class="result"></div>
	</nav>
</div>

<script type="text/javascript">
function linkAgreement(modid, iduser) {
	agrnum = document.getElementById('agr_number').value;
	if ( agrnum == '' || iduser=='') {
		alert('Лицевой счет не может быть пустым.');
	} else {
		url="/?option=com_ajax&amp;module=ovoclients&amp;method=linkAgreement&amp;format=raw&amp;<?php echo $itemId;?>&amp;ls="+agrnum+"&amp;modid="+modid+"&amp;uid="+iduser;
		//console.log("url:"+url);

		var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
		var xhr = new XHR();
		xhr.open('GET', url, true);
		localStorage.removeItem("returnTextLink");
		xhr.onload = function() {
			console.log(this.responseText);
			localStorage.setItem("returnTextLink", this.responseText);
				//обновить страницу
			window.location.reload();
			//sleep(2);
			//resdiv = document.getElementById('resultlink');
			//if (resdiv) {
			//	resdiv.innerText=this.responseText;
			//}
		}
		xhr.onerror = function() {
			//console.log(this.responseText);
			//console.log(this.status);
			localStorage.setItem("retTextLink", '<p>Договор № '+agrnum+' не смогли привязать.</p><p>'+this.status+'</p>');
			window.location.reload();
			//sleep(2);
			//resdiv = document.getElementById('resultlink');
			//if (resdiv) {
			//	resdiv.innerText='<p>Договор № '+agrnum+' не смогли привязать.</p><p>'+this.status+'</p>';
			//}
		}
		xhr.send(null);
	}
}

function gotoListAgreement(){
	localStorage.removeItem("returnTextLink");
	document.location.href = "<?php echo $params->get('pagelistagreements', '/');?>";
}

/*
window.onload = function(){
	var retTL = localStorage.getItem("returnTextLink");
	if ( retTL != 'undefined' ) {
		resdiv = document.getElementById('resultlink');
		if (resdiv) {
			resdiv.innerText=retTL;
		}
	}
}
*/
onReady(function() {
		var retTL = localStorage.getItem("returnTextLink");
		if ( retTL != 'undefined' ) {
			resdiv = document.getElementById('resultlink');
			if (resdiv) {
				resdiv.innerText=retTL;
			}
		}
	}
)

</script>

<?php
} /* if ( !$user->guest ) */
