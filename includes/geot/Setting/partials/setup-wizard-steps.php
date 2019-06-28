<ol class="geot-setup-steps">
	<?php foreach ( $step_all as $step_key => $step_data ) : ?>
		<?php $class = $step_key == $step_current ? 'active' : 'done'; ?>


		<?php $is_completed = array_search( $step_current, array_keys( $step_all ), true ) > array_search( $step_key, array_keys( $step_all ), true ); ?>

		<?php if ( $step_key == $step_current ) : ?>
			<li class="active">
				<?php echo esc_html( $step_data['name'] ); ?>
			</li>
		<?php elseif ( $is_completed ): ?>
			<li class="done">
				<a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php echo esc_html( $step_data['name'] ); ?></a>
			</li>
		<?php else : ?>
			<li>
				<?php echo esc_html( $step_data['name'] ); ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ol>