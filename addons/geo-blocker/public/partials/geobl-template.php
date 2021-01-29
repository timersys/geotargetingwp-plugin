<?php $opts = GeotWP_Bl_Helper::get_options( $id ); ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php __( 'Access denied', 'geot' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		div.geobl-container {
			position: fixed;
			width: 100%;
			height: 100%;
			background: #fff;
			top: 0;
			left: 0;
			z-index: 9999999999;
			color: #000;
		}

		div.geobl-container img {
			display: block;
			margin: auto;
		}

		div.geobl-container div.geobl-section {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			margin: auto;
			width: 280px;
			height: 280px;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			text-align: center;
		}
	</style><!-- Icon made by Freepik from >www.flaticon.com is licensed by CC 3.0 BY-->
</head>
<body>
<!-- Geo Blocker plugin https://geotargetingwp.com-->
<div class="geobl-container">
	<div class="geobl-section">
		<img src="<?php echo GEOTWP_BL_PLUGIN_URL; ?>/public/img/stop.svg" alt="Stop"/><br>
		<?php echo $opts['block_message']; ?>
	</div>
</div>
</body>
</html>