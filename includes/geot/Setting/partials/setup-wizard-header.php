<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php esc_html_e( 'Geotargeting &rsaquo; Setup Wizard', 'geot' ); ?></title>
	<?php do_action( 'admin_enqueue_scripts' ); ?>
	<?php wp_print_scripts( 'geot-setup' ); ?>
	<?php do_action( 'admin_print_styles' ); ?>
	<?php do_action( 'admin_head' ); ?>
</head>
<body class="wp-core-ui">
<h1 class="geot-logo">
	<a href="https://geotargetingwp.com/">
		<img src="<?php echo esc_url( $this->plugin_url ); ?>img/geot-logo.png" alt="Geot"/>
	</a>
</h1>

<div class="geot-setup">