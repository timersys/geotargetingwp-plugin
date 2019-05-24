<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*
*  Meta box - Rules
*
*  This template file is used when editing a redirection and creates the interface for editing redirections rules.
*
*  @type	template
*  @since	2.0
*/
do_action( 'geobl/metaboxes/before_rules', $post );
?>

<table class="geot_table widefat" id="geot_rules">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Rules", 'geobl' ); ?></label>
			<p class="description"><?php _e("Create a set of rules to determine if the user will be blocked", 'geobl' ); ?></p>
		</td>
		<td>
			<div class="rules-groups">

                <?php if( is_array($groups) ): ?>
                	<?php foreach( $groups as $group_id => $group ):
                		$group_id = 'group_' . $group_id;
                		?>
                		<div class="rules-group" data-id="<?php echo $group_id; ?>">
                			<?php if( $group_id == 'group_0' ): ?>
                				<h4><?php _e("Block user if", 'geobl' ); ?></h4>
                			<?php else: ?>
                				<h4 class="rules-or"><span><?php _e("OR", 'geobl' ); ?></span></h4>
                			<?php endif; ?>
                			<?php if( is_array($group) ): ?>
                			<table class="geot_table widefat">
                				<tbody>
                					<?php foreach( $group as $rule_id => $rule ):
                						$rule_id = 'rule_' . $rule_id;
                					?>
                					<tr data-id="<?php echo $rule_id; ?>">
                					<td class="param"><?php

                						$choices = Geot_Rules::get_rules_choices();

                						// create field
                						$args = array(
                							'group_id' 	    => $group_id,
                							'rule_id'	    => $rule_id,
                							'name'		    => 'geobl_rules[' . $group_id . '][' . $rule_id . '][param]',
                							'value' 	    => $rule['param']
                						);

                						Geot_Helper::print_select( $args, $choices );


                					?></td>
                					<td class="operator"><?php

                						$args = array(
                							'group_id' 	=> $group_id,
                							'rule_id'	=> $rule_id,
                							'name'		=> 'geobl_rules[' . $group_id . '][' . $rule_id . '][operator]',
                							'value' 	=> $rule['operator'],
                							'param'		=> $rule['param'],

                						);
                						Geot_Helper::ajax_render_operator( $args );

                					?></td>
                					<td class="value"><?php
                						$args = array(
                							'group_id' 		=> $group_id,
                							'rule_id' 		=> $rule_id,
                							'value' 		=> !empty($rule['value']) ? $rule['value'] : '',
                							'name'			=> 'geobl_rules[' . $group_id . '][' . $rule_id . '][value]',
                							'param'			=> $rule['param'],
                						);
                						Geot_Helper::ajax_render_rules( $args );

                					?></td>
                					<td class="add">
                						<a href="#" class="rules-add-rule button"><?php _e("+ AND", 'geobl' ); ?></a>
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

                	<h4 class="rules-or"><span><?php _e("OR", 'geobl' ); ?></span></h4>

                	<a class="button rules-add-group" href="#"><?php _e("Add rule group (+ OR)", 'geobl' ); ?></a>

                <?php endif; ?>
				<p> <?= sprintf(__('Learn more about Geo Blocker and compatible cache plugins <a href="%s" target="_blank">here</a>','geobl'), 'https://geotargetingwp.com/docs/geo-blocker/cache');?> </p>
			</div>
		</td>
	</tr>
	</tbody>
</table>
