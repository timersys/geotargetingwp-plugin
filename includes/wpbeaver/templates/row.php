<?php $container_element = ( ! empty( $row->settings->container_element ) ? $row->settings->container_element : 'div' ); ?>

<?php ob_start(); ?>
<<?php echo $container_element; ?><?php FLBuilder::render_row_attributes( $row ); ?>>
	<div class="fl-row-content-wrap geot-row">
		<?php FLBuilder::render_row_bg( $row ); ?>
		<?php do_action( 'fl_builder_render_node_layers', $row ); ?>
		<div class="<?php FLBuilder::render_row_content_class( $row ); ?>">
			<?php
				// $groups received as a magic variable from template loading.
				foreach ( $groups as $group ) {
					FLBuilder::render_column_group( $group );
				}
			?>
		</div>
	</div>
</<?php echo $container_element; ?>>

<?php
	$out = ob_get_clean();
	echo apply_filters( 'geot/wpbeaver/template/row', $out, $row );
?>