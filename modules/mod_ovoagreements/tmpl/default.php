<?php
// No direct access
defined('_JEXEC') or die;
?>

<?php

//$scriptAJAX = "/modules/mod_ovoagreements/getinvoice.php";
//echo '<pre>';
//var_dump(__DIR__);
//var_dump(JPATH_BASE);
//echo '</pre>';
$config = new JConfig;
$user = JFactory::getUser();
$guest = $user->guest;
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
//$urlinvoice      = $params->get('url1cinvoice');
//$url1cremovelink = $params->get('url1cremovelink');
$showclosed = ( $params->get('showclosed') == "1" );
$showunattached = ( $params->get('showunattached') == "1" );
$showbalance = ( $params->get('showbalance') == "1" );
$useinvoice  = ( $params->get('provideinvoice') == "1" );
$useunattach = ( $params->get('provideunattach') == "1" );
$useremove   = ( $params->get('provideremove') == "1" );
$usebalance  = ( $params->get('providebalance') == "1" );
$islog  = (boolean) $params->get('IsLog', "1");

if ( empty($module) ) :
	$modid=$module->id;
else :
	$modid=$params->get('id_copy', 0);
endif

?>
<div class="ovo agreements" id="ovoagreements" modid="<?php echo $modid;?>">
<?php
if ( !$guest ) {
    foreach ($dataAgr->user[0]->agreements[0] as $agr) {
        $agrnum = $agr['number'];
        
		$active=true;
		if ($agr['active']=='1') {            
			$activestr='Действует';
		} elseif ($agr['active']=='2') {
			$activestr='Неизвестно';
		} else {
			$active=false;
			$activestr='Расторгнут';
        }
        
        $status=$agr['status'];
        $attached = false;
        $attachedstr=$agr['statusstr'];
        if ($status == '1') {
            $attached = true;
        }
        if ( (!$active and !$showclosed) or ($status=='3' and !$showunattached) ) {
            continue;
        }
?>
		<div class="agreement" id="<?php echo $agrnum; ?>">
			<div class="info box">
                <div class="num"><?php echo $agrnum; ?></div>
<?php
		$v= strval($agr['summadolg']);
		$v=preg_replace("/[^\d,-]/","",$v);
		$v=preg_replace("/,/",".",$v);
		if ($v >= 0 ) {
			$colval='style="color:#0000000;"';
		} else {
			$colval='style="color:#f00000;"';
		}
		if ($showbalance):
?>
				<div class="saldo">Долг:&nbsp&nbsp<span class="num" <?php echo $colval;?>><?php echo number_format($v, 2, ',', ' ');?></span></div>
<?php
		endif;
?>
				<div class="type"><?php echo $agr['type']; ?></div>
				<div class="briefcontent"><?php echo $agr['briefcontent']; ?></div>
                <div><?php echo $agr['name']; ?></div>
                <div><?php echo $attachedstr; ?></div>
                <div><?php echo $activestr; ?></div>
            </div> <!-- info box -->

            <div class="action box">
<?php
		/*
			Договор прикреплен (status=1) и является действующим
			$status=0	- Новый
			$status=1	- Прикреплен
			$status=2	- На рассмотрении
			$status=3	- Откреплен
			
			$active		- 1, значит договор действующий; 0 - договор закрыт (пометка удаления); 2-неизвестно, нет указателя на справочник ДоговорОхраны
			$attached	- истина, значит договор прикреплен
		*/
		$cat='tmpl';
		AvvLog::logMsg(['msg'=>
			[
				'вызов шаблона (вывод модуля) ===============================================================================',
				'itemId: '.$itemId,
				'agrnum: '.$agrnum,
				'user.username: '.$user->username,
				'status: '.$status,
				'active: '.$active,
				'attached: '.$attached,
				'useunattach: '.$useunattach,
				'useremove: '.$useremove,
				'текущее сальдо: '.$agr['summadolg'],
				'вызов шаблона (вывод модуля) END ==========================================================================='
			],
			'category'=>$cat], $islog, NULL, 'ovoagreements.log' );
        if ($active) { /* т.е. договор не закрыт */
			if ( $status == '0' or $status=='2' ) {
				//status=0 - статус в 1с Новый
				//status=2 - статус в 1с На рассмотрении
?>
                <div class="newagr error">
                    <p>Договор не связан с сайтом.</p>
                    <p>На рассмотрении администратора.</p>
                    <p>Обратитесь в отдел.</p>
                </div>
<?php
        		include(dirname(__FILE__)."/btn_remove.php");
			}
			if ($status == '1') {
				//status=1 - статус в 1с Прикреплен
        		include(dirname(__FILE__)."/btn_invoice.php");
        		include(dirname(__FILE__)."/btn_unattach.php");
        		include(dirname(__FILE__)."/btn_remove.php");
        		// только здесь кнопки начислено оплачено
				include(dirname(__FILE__)."/btn_debet.php");
			}
			if ($status=='3') {
				//status=3 - статус в 1с Откреплен
				/* показать кнопку удалить все связи договора охраны 1с8 с сайтом */
        		include(dirname(__FILE__)."/btn_remove.php");
			}
        } else { /* if ($active) */
?>
				<div class="error">
					<p>Договор расторгнут.</p>
				</div>
<?php
			include(dirname(__FILE__)."/btn_remove.php");
		} /* if ($active) */
?>
			</div> <!-- action box -->
		</div> <!-- agreement -->
<?php   
	} /* foreach ($dataAgr->user[0]->agreements[0] as $agr) */
} else {
?>
	<div>
		Войдите или зарегистрируйтесь на сайте.
	</div>
<?php
}
?>
</div> <!-- ovo agreement -->

<div id="filepdf" style="/*display:none;*/">
<?php
/*
echo $dataAgr;
echo '<pre>';
var_dump($dataAgr);
echo '</pre>';
*/
?>
</div>

<script type="text/javascript">
function getInvoice(ls, iduser){
	agrnum=ls.attributes['id'].value;
	el=document.getElementById('ovoagreements');
	modid=el.attributes['modid'].value;
	url="/?option=com_ajax&amp;module=ovoagreements&amp;method=getInvoice&amp;format=raw&amp;<?php echo $itemId;?>&amp;ls="+agrnum+"&amp;modid="+modid+"&amp;userid="+iduser;
	console.log('url: '+url);
	if (!agrnum || !modid) {
		return;
	}
    var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
    var xhr = new XHR();
    // (2) запрос на другой домен :)
    xhr.open('GET', url, true);
    xhr.onload = function() {
	    // функция вызываемая при успешном вызове
        fname=this.responseText;
		if (fname) {
			arr=fname.split('/');
			fn=arr[arr.length-1];
			newWindow = window.open(fname, 'newWindow');
			url="/?option=com_ajax&amp;module=ovoagreements&amp;method=delFile&amp;format=raw&amp;<?php echo $itemId;?>&amp;modid="+modid+"&amp;userid="+iduser+"&amp;fn="+fn;
			console.log('url: '+url);
			var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
			var xhr1 = new XHR();
    	    xhr1.open('GET', url, true);
    	    //задержка
			var ms = new Date().getTime()+3000;
			//while (new Date() < ms){}
    	    // выполнить запрос
			//xhr1.send();
		}
	}
    // функция вызываемая в случае ошибки
    xhr.onerror = function() {
        alert( 'Ошибка ' + this.status );
    }
    // послать запрос
    xhr.send();
}

function removeLink(element, action, iduser){
	if (!action) {
		action=0;
	}
	agrnum=element.attributes['id'].value;
	el=document.getElementById('ovoagreements');
	modid=el.attributes['modid'].value;
	if ( !agrnum || !iduser || !modid) {
		return;
	}
	console.log("modid ="+modid);
    url="/?option=com_ajax&amp;module=ovoagreements&amp;method=removeLink&amp;format=raw&amp;<?php echo $itemId;?>&amp;ls="+agrnum+"&amp;action="+action+"&amp;uid="+iduser+"&amp;modid="+modid;
	var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
	var xhr = new XHR();
	xhr.open('GET', url, true);
    xhr.onload = function() {
		//var el = document.getElementById('filepdf');
        //el.innerText=this.responseText;    
		window.location.reload();
    }
    xhr.onerror = function() {
		window.location.reload();
    }
    xhr.send(null);
}

function historyDebet(element, iduser) {
	agrnum=element.attributes['id'].value;
	el=document.getElementById('ovoagreements');
//	modid=el.attributes['modid'].value;
<?php
if ($deb):
?>	
	console.log('agrnum:'+agrnum);
	console.log('iduser:'+iduser);
//	console.log('modid:'+modid);
<?php
endif;
?>	
	localStorage.setItem("agrBalanceIdAgr", agrnum);
	window.location.href = "<?php echo $params->get('pagebalanceagreement', '/');?>";
}
</script>
