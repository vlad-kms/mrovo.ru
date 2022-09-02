<?php
?>
<script type="text/javascript">
function getInvoice(ls){
    agrnum=ls.attributes['id'].value;
    url="/?option=com_ajax&amp;module=ovoagreements&amp;method=getInvoice&amp;format=raw&amp;<?php echo $itemId;?>ls="+agrnum+"&amp;urlServiceInvoice=<?php echo $urlinvoice;?>";
    //console.log("agrnum:"+agrnum);
    //console.log("url:"+url);
    //console.log("urlinvoice:<?php echo $urlinvoice;?>")
    //return;
    var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
    var xhr = new XHR();
    // (2) запрос на другой домен :)
    xhr.open('GET', url, true);
    // функция вызываемая при успешном вызове
    xhr.onload = function() {
        //alert( this.responseText );
        //var blob=new Blob([this.responseText], {type: "Application/pdf"});
        //console.log(blob);
        //console.log(this.responseText);
        //var el = document.getElementById('filepdf');
        //el.innerText=this.responseText;
        //uriContent = "data:application/octet-stream," + encodeURIComponent((this.responseText));
        //console.log(uriContent);
        //newWindow = window.open(uriContent, 'newWindow');
        newWindow = window.open(this.responseText, 'newWindow');

        arr=this.responseText.split('/');
        fn=arr[arr.length-1];
        //console.log(fn);
        url="/?option=com_ajax&amp;module=ovoagreements&amp;method=delFile&amp;format=raw&amp;<?php echo $itemId;?>fn="+fn;
        console.log(url);
        var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
        var xhr = new XHR();
        xhr.open('GET', url, true);
        xhr.send();
//	    xhr.onerror = function() {
//    	    alert( 'Ошибка ' + this.status );
//	    }
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
    agrnum=ls.attributes['id'].value;
	if ( !agrnum || !iduser ){
		return;
	}
	console.log("agrnum="+agrnum);
	console.log("action="+action);
	console.log("iduser="+iduser);
}

</script>
<!--
var blob = new Blob(['foo', 'bar']);

console.log('size=' + blob.size);
console.log('type=' + blob.type);

var testEndings = function(string, endings) {
  var blob = new Blob([string], { type: 'plain/text',
                                  endings: endings });
  var reader = new FileReader();
  reader.onload = function(event){
    console.log(endings + ' of ' + JSON.stringify(string) +
                ' => ' + JSON.stringify(reader.result));
  };
  reader.readAsText(blob);
};

testEndings('foo\nbar',   'native');
testEndings('foo\r\nbar', 'native');
testEndings('foo\nbar',   'transparent');
testEndings('foo\r\nbar', 'transparent');
-->

<?php
