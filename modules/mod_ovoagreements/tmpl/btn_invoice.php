<?php
if ($useinvoice) { /* если разрешен запрос квитанций */
?>
		<button class="button-div submit" onclick="getInvoice(this.parentNode.parentNode, <?php echo $user->id; ?>)"> Квитанция </button>
<?php
}
?>