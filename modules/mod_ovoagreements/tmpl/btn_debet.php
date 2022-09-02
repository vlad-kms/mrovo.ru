<?php
if ( $usebalance && ($status == '1') ):
//if ( $usebalance ):
?>
	<button class="button-div submit" onclick="historyDebet(this.parentNode.parentNode, <?php echo $user->id; ?>)"> Начисления и оплата </button>
<?php
endif;