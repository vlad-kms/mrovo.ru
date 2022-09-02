<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.log.logger.formattedtext');
?>

<?php
//$doc = JFactory::getDocument();


JHtml::_('jquery.framework', false); // чтобы первой загрузилась jQuery
////$doc->addScript(JURI::root().'modules/mod_ovobalance/tmpl/js/jquery.jqGrid.min.js');
////$doc->addScript(JURI::root().'modules/mod_ovobalance/tmpl/js/grid.locale-ru.js');
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jquery.jqGrid.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/grid.locale-ru.js', array('version' => 'auto', 'relative' => true));

////$doc->addStyleSheet('modules/mod_ovobalance/tmpl/css/jquery-ui.min.css');
////$doc->addStyleSheet(JURI::root().'modules/mod_ovobalance/tmpl/css/ui.jqgrid.css');
JHtml::_('stylesheet', JURI::root().'modules/mod_ovobalance/tmpl/css/jquery-ui.min.css?v=1', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', JURI::root().'modules/mod_ovobalance/tmpl/css/ui.jqgrid.css?v=1', array('version' => 'auto', 'relative' => true));

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
$itemId = JFactory::getApplication()->getMenu()->getActive()->id;
if( !empty($itemId) ):
	$itemId = 'Itemid='.$itemId;
else:
	$itemId = '';
endif;
//$url1cbalance = $params->get('url1cgetbalance');
$islog  = $params->get('IsLog', "1");

if ( empty($module) ):
	$modid=$module->id;
else :
	$modid=$params->get('id_copy', 0);
endif;

if ($islog):
	$optLog = array(
		// Имя текстового файла для логирования, по умолчанию error.php
		'text_file' => 'ajax-ovobalance.log',
		// Путь к папке с логами (если параметр отсутствует, путь возьмется из конфига)
		'text_file_path' => JPATH_ROOT.'/tmp/log/',
		// Параметр проверяющий формат, если файл .php используем тип false, если текстовый или другой формат, то true
		'text_file_no_php' => true,
		// Форматирование записываемого текста/сообщения
		'text_entry_format' => ''
	);
	$jl = new JLogLoggerFormattedtext( $optLog);

	$log = new JLogEntry(
		'шаблон вывод страницы Balance =============================================================================================================',
		JLog::INFO,
		'my_category',
		null,
		array()
	);
	//Выполняем запись в лог:
	$jl->addEntry( $log );

	$log->message='JURI::ROOT: '.JURI::root();
	$jl->addEntry( $log );
	$log->message='user->id: '.$user->id;
	$jl->addEntry( $log );
	$log->message='user->name: '.$user->name;
	$jl->addEntry( $log );
	$log->message='user->username: '.$user->username;
	$jl->addEntry( $log );
	$log->message='user->email: '.$user->email;
	$jl->addEntry( $log );
	$log->message='url1cbalance: '.$url1cbalance;
	$jl->addEntry( $log );
endif;

if ( !$user->guest):
?>
<div class="ovo balance <?php echo $moduleclass_sfx;?>" id="ovobalance" modid="<?php echo $modid;?>">
	<div class="box">Договор (л/с):&nbsp;&nbsp;<span class="num" id="id_agrnum"></span></div>
	<div class="formbalance">
		<span class="label_avv">Начальная дата</span>
		<input class="input_avv" type="date" name="datebegin" id="DateBegin">
		<span class="label_avv">Конечная дата</span>
		<input class="input_avv" type="date" name="dateend" id="DateEnd">
		<div>&nbsp</div>
	</div>
	<nav class="menu_nav">
		<div class="div" onclick="refreshTable(<?php echo $modid;?>, <?php echo $user->id;?>)">&nbsp;Обновить&nbsp;</div>
	</nav>
	<div class="tablebalance" id="id_result">
		<table id="id_balance"><tr><td></td></tr></table>
		<div id="pager"></div> 
	</div>
<?php
	if ($deb):
?>
	<div class="debug">
	
	</div>
<?php
	endif;
endif; // if ( !$user->guest)
?>
</div> <!-- class="ovo balance" -->

<script type="text/javascript">
function refreshTable(modid, userid){
	var db = document.getElementById('DateBegin').valueAsDate;
	var de = document.getElementById('DateEnd').valueAsDate;
	if ( (typeof(de)=='undefined') || (de==null) || (de.valueOf()==0) ){
		de = new Date();
		e=document.getElementById("DateEnd");
		e.value=de.getFullYear() + '-' + ('0' + (de.getMonth() + 1)).slice(-2) + '-' + ('0' + de.getDate()).slice(-2);
	}
//	if (!db) {
	if ( (typeof(db)=='undefined') || (db==null) || (db.valueOf()==0) || (db.valueOf() > de.valueOf()) ){
		db = diffMonth(de, 4);
		e=document.getElementById("DateBegin");
		e.value = db.getFullYear() + '-' + ('0' + (db.getMonth() + 1)).slice(-2) + '-' + ('0' + db.getDate()).slice(-2);
	}
	localStorage.setItem("agrDateEnd",   de.valueOf());
	localStorage.setItem("agrDateBegin", db.valueOf());
	agrnum=localStorage.getItem("agrBalanceIdAgr");
<?php
if($deb):
?>	
	console.log("db:"+db);
	console.log("de:"+de);
	console.log("typeOf(db):"+typeof(db));
	console.log("typeOf(de):"+typeof(de));
<?php
endif;
?>
	if ( (typeof(agrnum)!='undefined') && (agrnum!=null) && (agrnum!="") ) {
		yb=db.getFullYear();
		mb=db.getMonth();
		db=db.getDate();
		ye=de.getFullYear();
		me=de.getMonth();
		de=de.getDate();
		url="/?option=com_ajax&amp;module=ovobalance&amp;method=getBalance&amp;format=raw&amp;<?php echo $itemId;?>&amp;ls="+agrnum+"&amp;modid="+modid+"&amp;uid="+userid+
			"&amp;yearb="+yb+"&amp;monthb="+mb+"&amp;dayb="+db+
			"&amp;yeare="+ye+"&amp;monthe="+me+"&amp;daye="+de;
		console.log("url:"+url);
		// САМ ЗАПРОС
		var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
		var xhr = new XHR();
		// (2) запрос на другой домен :)
		xhr.open('GET', url, true);
		xhr.onload = function() {
			// функция вызываемая при успешном вызове
			result=this.responseText;
			e=document.getElementById("id_result");
			/*
			if (e) {
				if (result) {
					e.innerHTML=result;
				} else {
					e.innerHTML='';
				}
			}
			*/
			fillTable('#id_balance', result);
		}
		// функция вызываемая в случае ошибки
		xhr.onerror = function() {
			alert( 'Ошибка ' + this.status );
			clearTable('#id_balance');
		}
		// послать запрос
		xhr.send();
	}
}

function fillTable(id_el, xml_data){
	jQuery(id_el).jqGrid({
		datatype: 'xmlstring',
		datastr : xml_data,
		colNames:['Дата','Нач.остаток', 'Начсилено','Оплачено','Кон.остаток','Примечание'],
		colModel :[ 
			{name:'invid', index:'invid', width:55, sorttype:'int'}, 
			{name:'invdate', index:'invdate', width:90, sorttype:'date', datefmt:'Y-m-d'}, 
			{name:'amount', index:'amount', width:80, align:'right', sorttype:'float'}, 
			{name:'tax', index:'tax', width:80, align:'right', sorttype:'float'}, 
			{name:'total', index:'total', width:80, align:'right', sorttype:'float'}, 
			{name:'note', index:'note', width:150, sortable:false} ],
		pager: '#pager',
		rowNum:10,
		viewrecords: true,
		subGrid:true,
		caption: ''
	});
}

function clearTable(id_el){
}

function leadingZero(val, len, charZero){
}

function diffMonth(date, monthDiff){
	y=date.getFullYear();
	m=date.getMonth();
	d=1;
	if ( m >= (monthDiff-1)) {
		m -= (monthDiff-1);
	} else {
		y -= 1;
		m = 11 - (monthDiff-1-m);
	}
	return new Date(y, m, d);
}

function startModule(){
	var dateBeg = new Date(Number(localStorage.getItem("agrDateBegin")));
	var dateEnd = new Date(Number(localStorage.getItem("agrDateEnd")));
	if ( (typeof(dateEnd) == "undefined") || (dateEnd == null) || (dateEnd.valueOf() == 0) ) {
		var dateEnd = new Date();
		localStorage.setItem("agrDateEnd", dateEnd.valueOf());
	}
	if ( (typeof(dateBeg) == "undefined") || (dateBeg == null) || (dateBeg.valueOf() == 0) || (dateBeg.valueOf() > dateEnd.valueOf()) ) {
		var dateBeg = diffMonth(dateEnd, 4);
		localStorage.setItem("agrDateBegin", dateBeg.valueOf());
	}
	e=document.getElementById("DateBegin");
	e.value = dateBeg.getFullYear() + '-' + ('0' + (dateBeg.getMonth() + 1)).slice(-2) + '-' + ('0' + dateBeg.getDate()).slice(-2);
	e=document.getElementById("DateEnd");
	e.value=dateEnd.getFullYear() + '-' + ('0' + (dateEnd.getMonth() + 1)).slice(-2) + '-' + ('0' + dateEnd.getDate()).slice(-2);
	agrnum=localStorage.getItem("agrBalanceIdAgr");
	e=document.getElementById("id_agrnum");
	if (agrnum) {
		e.innerText=agrnum;
	} else {
		e.innerText="нет";
	}
}

onReady(function() {
		startModule();
    }
)

</script>

<?php