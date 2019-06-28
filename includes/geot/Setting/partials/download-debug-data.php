<?php
if ( isset( $_POST['geot-debug-button'] ) &&
     isset( $_POST['geot-debug-content'] ) &&
     isset( $_POST['geot-debug-nonce'] )
) {
	header( "Content-type: text/plain" );
	header( "Content-Disposition: attachment; filename=debug-data.txt" );

	echo $_POST['geot-debug-content'];
}
?>