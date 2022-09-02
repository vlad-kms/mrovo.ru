<?php
if ($useunattach) { /* если разрешен запрос квитанций */
?>
		<button class="button-div submit" onclick="removeLink(this.parentNode.parentNode, 0, <?php echo $user->id; ?>)"> Открепить </button>
<?php
}
?>