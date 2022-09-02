<?php
// No direct access
defined('_JEXEC') or die;

?>

<?php
//$doc = JFactory::getDocument();


JHtml::_('jquery.framework', false); // чтобы первой загрузилась jQuery
////$doc->addScript(JURI::root().'modules/mod_ovobalance/tmpl/js/jquery.jqGrid.min.js');
////$doc->addScript(JURI::root().'modules/mod_ovobalance/tmpl/js/grid.locale-ru.js');
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxcore.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxdata.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxbuttons.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxscrollbar.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxmenu.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxgrid.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxgrid.selection.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxgrid.filter.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/jqxgrid.sort.js', array('version' => 'auto', 'relative' => true));
/*    
JHtml::_('script', JURI::root().'modules/mod_ovobalance/tmpl/js/demos.js', array('version' => 'auto', 'relative' => true));
*/



////$doc->addStyleSheet('modules/mod_ovobalance/tmpl/css/jquery-ui.min.css');
////$doc->addStyleSheet(JURI::root().'modules/mod_ovobalance/tmpl/css/ui.jqgrid.css');
JHtml::_('stylesheet', JURI::root().'modules/mod_ovobalance/tmpl/css/jqx.base.css?v=1', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', JURI::root().'modules/mod_ovobalance/tmpl/css/jqx.arctic.css?v=1', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', JURI::root().'modules/mod_ovobalance/tmpl/css/jqx.ui-redmond.css?v=1', array('version' => 'auto', 'relative' => true));

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
//$url1cbalance = $params->get('url1cgetbalance');
$islog  = (boolean)$params->get('IsLog', "1");

if ( empty($module) ):
	$modid=$module->id;
else :
	$modid=$params->get('id_copy', 0);
endif;
$cat='tmpl';
AvvLog::logMsg(['msg'=>
	[
		'JFactory::getApplication()->getMenu()',
		//JFactory::getApplication()->getMenu(),
		'JFactory::getApplication()->getMenu()->getActive()',
		JFactory::getApplication()->getMenu()->getActive()
	],
	'category'=>$cat], $islog, NULL, 'ovobalance.log' );
AvvLog::logMsg(['msg'=>
	[
		'вызов шаблона (вывод модуля) ===============================================================================',
		'JURI::ROOT: '.JURI::root(),
		'user.id: '.$user->id,
		'user.name: '.$user->name,
		'user.username: '.$user->username,
		//'url1cbalance: '.$url1cbalance,
		'вызов шаблона (вывод модуля) END ==========================================================================='
	],
	'category'=>$cat], $islog, NULL, 'ovobalance.log' );
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
	<!--
		<a class="div" href="index.php?option=com_content&view=article&id=2">&nbsp;Обновить&nbsp;</a>
	-->
	</nav>
	<div class="tablebalance" id="id_result">
		<div id="grid"></div>
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
	var gr =jQuery('#grid');
	gr.jqxGrid('clear');
	gr=gr.parent().parent();
	gr.addClass("requestInProgress");
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
<?php
if($deb):
?>	
		console.log("yb:"+yb);
		console.log("mb:"+mb);
		console.log("db:"+db);
		console.log("ye:"+ye);
		console.log("me:"+me);
		console.log("de:"+de);
<?php
endif;
?>
		url="/?option=com_ajax&amp;module=ovobalance&amp;method=getBalance&amp;format=raw&amp;<?php echo $itemId;?>&amp;ls="+agrnum+"&amp;modid="+modid+"&amp;uid="+userid+
			"&amp;yearb="+yb+"&amp;monthb="+mb+"&amp;dayb="+db+
			"&amp;yeare="+ye+"&amp;monthe="+me+"&amp;daye="+de;
		//console.log("url:"+url);
		// САМ ЗАПРОС
		var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
		var xhr = new XHR();
		// асинхронный запрос на другой домен :)
		xhr.open('GET', url, true);
		xhr.onload = function() {
			// функция вызываемая при успешном вызове
			gr.removeClass("requestInProgress");
			result=this.responseText;
			e=document.getElementById("id_result");
			fillTable('grid', result);
		}
		// функция вызываемая в случае ошибки
		xhr.onerror = function() {
			gr.removeClass("requestInProgress");
			alert( 'Ошибка ' + this.status );
			clearTable('#id_balance');
		}
		// послать запрос
		xhr.send();
/*
		// синхронный запрос на другой домен :)
		xhr.open('GET', url, false);
		xhr.send();
		result=xhr.responseText;
		e=document.getElementById("id_result");
		fillTable('grid', result);
*/
	}
}

function fillTable(idel, xmldata){
	//jQuery('#grid').jqxGrid('clear');
	var source ={
		datafields: [
			{ name: 'Date' },
			{ name: 'InitBalance' },
			{ name: 'Accured' },
			{ name: 'Paid' },
			{ name: 'FinalBalance' },
			{ name: 'Action' },
			{ name: 'Note' }
		],
		root: "rows",
		record: "row",
		datatype: "xml",
		localdata: xmldata,
		id:'idr'
	};
	var ordersSource ={
		datafields: [
			{ name: 'idr', type: 'string' },
			{ name: 'Date', type: 'string' },
			{ name: 'InitBalance', type: 'string' },
			{ name: 'Accured', type: 'string' },
			{ name: 'Paid', type: 'string' },
			{ name: 'FinalBalance', type: 'string' },
			{ name: 'Action' },
			{ name: 'Note', type: 'string' }
		],
		root: "rows",
		record: "rowd",
		datatype: "xml",
		async: false,
		localdata: xmldata
	};
	var ordersDataAdapter = new jQuery.jqx.dataAdapter(ordersSource, { autoBind: true });
	orders = ordersDataAdapter.records;
	var nestedGrids = new Array();

	wd = jQuery("#grid").parent("div").width() - 10;
	// create nested grid.
	var initrowdetails = function (index, parentElement, gridElement, record) {
		var id = record.uid.toString();
		var grid = jQuery(jQuery(parentElement).children()[0]);
		nestedGrids[index] = grid;
		var filtergroup = new jQuery.jqx.filter();
		var filter_or_operator = 1;
		var filtervalue = id;
		var filtercondition = 'equal';
		var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
			// fill the orders depending on the id.
		var ordersbyid = [];
		for (var m = 0; m < orders.length; m++) {
			var result = filter.evaluate(orders[m]["idr"]);
			if (result)
				ordersbyid.push(orders[m]);
			}

			var orderssource = { datafields: [
				{ name: 'idr', type: 'string' },
				{ name: 'Date', type: 'string' },
				{ name: 'InitBalance', type: 'string' },
				{ name: 'Accured', type: 'string' },
				{ name: 'Paid', type: 'string' },
				{ name: 'FinalBalance', type: 'string' },
				{ name: 'Note', type: 'string' }
			],
			localdata: ordersbyid
		}
		var nestedGridAdapter = new jQuery.jqx.dataAdapter(orderssource);

		if (grid != null) {
			grid.jqxGrid({
				source: nestedGridAdapter, width: 780, height: 140,
				//width:950,
				//width:getWidth('grid'),
				columns: [
					//{ text: 'idr', datafield: 'idr', width: 200 },
					{ text: 'Date', datafield: 'Date', width: 390 },
					//{ text: 'Нач.остаток', datafield: 'InitBalance', width: 200 },
					{ text: 'Начислено', datafield: 'Accured', width: 150 },
					{ text: 'Оплачено', datafield: 'Paid', width: 150 },
					//{ text: 'Кон.остаток', datafield: 'FinalBalance', width: 200 },
					{ text: 'Примечание', datafield: 'Note', width: 500 }
				]
			});
		}
	}
	
	////
	var renderer = function (row, column, value) {
		return '<span style="margin-left: 4px; margin-top: 9px; float: left;">' + value + '</span>';
	}
	
	jQuery('#grid').jqxGrid(
//	jQuery(idel).jqxGrid(
	{
		//width: getWidth('grid'),
		//height: getHeight('grid'),
		//width:1000,
		width:wd,
		height: 680,
		source: source,
		rowdetails: true,
		rowsheight: 35,
		//theme:'ui-redmond',
		theme:'arctic',
		initrowdetails: initrowdetails,
		rowdetailstemplate: { rowdetails: "<div id='grid' style='margin: 10px;'></div>", rowdetailsheight: 160, rowdetailshidden: true },
		//rowdetailstemplate: { rowdetails: "<div id='"+idel+"' style='margin: 10px;'></div>", rowdetailsheight: 220, rowdetailshidden: true },
		ready: function () {
			jQuery('#grid').jqxGrid('showrowdetails', 1);
			//jQuery(idel).jqxGrid('showrowdetails', 1);
		},
		columns: [
			{ text: 'Date', datafield:'Date', width: 250, cellsrenderer: renderer },
			{ text: 'Нач.остаток', datafield:'InitBalance', width: 150, cellsrenderer: renderer },
			{ text: 'Начислено', datafield:'Accured', width: 150, cellsrenderer: renderer },
			{ text: 'Оплачено', datafield:'Paid', width: 150, cellsrenderer: renderer },
			{ text: 'Кон.остаток', datafield:'FinalBalance', width: 150, cellsrenderer: renderer }
			//{ text: 'Что', datafield:'Action', width: 150, cellsrenderer: renderer },
			//{ text: 'Примечание', datafield:'Note', width: 150, cellsrenderer: renderer }
		]
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
	refreshTable(<?php echo $modid;?>, <?php echo $user->id;?>);
}

onReady(function() {
		startModule();
    }
)

</script>

<?php