<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
*  Meta box - Rules
*
*  This template file is used when editing a redirection and creates the interface for editing redirections rules.
*
*  @type	template
*  @since	2.0
*/
do_action( 'geot/metaboxes/before_rules', $post );
?>

<table class="geot_table widefat" id="geot-rules">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e( "Rules", 'geot' ); ?></label>
			<p class="description"><?php echo $params['desc']; ?></p>
		</td>
		<td>
			<div class="rules-groups">

				<?php if ( is_array( $groups ) ): ?>
					<?php foreach ( $groups as $group_id => $group ):
						$group_id = 'group_' . $group_id;
						?>
						<div class="rules-group" data-id="<?php echo $group_id; ?>">
							<?php if ( $group_id == 'group_0' ): ?>
								<h4><?php echo $params['title']; ?></h4>
							<?php else: ?>
								<h4 class="rules-or"><span><?php _e( "OR", 'geot' ); ?></span></h4>
							<?php endif; ?>
							<?php if ( is_array( $group ) ): ?>
								<table class="geot_table widefat">
									<tbody>
									<?php foreach ( $group as $rule_id => $rule ):
										$rule_id = 'rule_' . $rule_id;
										?>
										<tr data-id="<?php echo $rule_id; ?>">
											<td class="param"><?php

												$choices = GeotWP_R_ules::get_rules_choices();

												// create field
												$args = [
													'group_id' => $group_id,
													'rule_id'  => $rule_id,
													'name'     => 'geot_rules[' . $group_id . '][' . $rule_id . '][param]',
													'value'    => $rule['param'],
												];

												GeotWP_Helper::print_select( $args, $choices );


												?></td>
											<td class="operator"><?php

												$args = [
													'group_id' => $group_id,
													'rule_id'  => $rule_id,
													'name'     => 'geot_rules[' . $group_id . '][' . $rule_id . '][operator]',
													'value'    => $rule['operator'],
													'param'    => $rule['param'],

												];
												GeotWP_Helper::ajax_render_operator( $args );

												?></td>
											<td class="value"><?php
												$args = [
													'group_id' => $group_id,
													'rule_id'  => $rule_id,
													'value'    => ! empty( $rule['value'] ) ? $rule['value'] : '',
													'name'     => 'geot_rules[' . $group_id . '][' . $rule_id . '][value]',
													'param'    => $rule['param'],
												];
												GeotWP_Helper::ajax_render_rules( $args );

												?></td>
											<td class="add">
												<a href="#"
												   class="rules-add-rule button"><?php _e( "+ AND", 'geot' ); ?></a>
											</td>
											<td class="remove">
												<a href="#" class="rules-remove-rule rules-remove-rule">-</a>
											</td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>

					<h4 class="rules-or"><span><?php _e( "OR", 'geot' ); ?></span></h4>

					<a class="button rules-add-group" href="#"><?php _e( "Add rule group (+ OR)", 'geot' ); ?></a>

				<?php endif; ?>
				<p> <?= sprintf( __( 'Learn more about Geo Blocker and compatible cache plugins <a href="%s" target="_blank">here</a>', 'geot' ), 'https://geotargetingwp.com/docs/geo-blocker/cache' ); ?> </p>
			</div>
		</td>
	</tr>
	</tbody>
</table>
