<?php
if ($useremove) { /* если разрешен запрос квитанций */
?>
		<button class="button-div submit" onclick="removeLink(this.parentNode.parentNode, 1, <?php echo $user->id; ?>)"> Удалить связь </button>
<?php
}
?>