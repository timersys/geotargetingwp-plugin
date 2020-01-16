<div class="geot-setup-content">
	<form action="" method="POST">
		<?php wp_nonce_field( 'geot-setup' ); ?>

		<?php do_action( 'geot/wizard/others/before' ); ?>

		<p><?php _e( 'By enabling the <b>Send anonymous usage data</b> you can help us to understand what kind of program features matters most to you, in order to make them even better.', 'geot' ); ?></p>

		<div class="location-row">
			<label for="geo-stats" class="location-label">
				<input type="checkbox" id="geo-stats" name="geot_others[geo-stats]" value="yes"
				       class="location-checkbox" <?php echo checked( $opts['geo-stats'], true ); ?> />
				<?php _e( 'Send anonymous usage data', 'geot' ); ?>
			</label>
			<!--div class="location-help"><?php //_e('By enabling the <b>Send anonymous</b> usage data you can help us to understand what kind of program features matters most to you, in order to make them even better.', 'geot' ); ?></div-->
		</div>

		<?php do_action( 'geot/wizard/others/after' ); ?>

		<div class="location-row text-center">
			<input type="hidden" name="save_step" value="1"/>
			<button class="button-primary button button-hero button-next location-button"
			        name="geot_others[button]"><?php _e( 'Next', 'geot' ); ?></button>
		</div>
	</form>
</div>