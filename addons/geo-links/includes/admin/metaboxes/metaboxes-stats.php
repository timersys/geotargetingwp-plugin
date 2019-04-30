<?php
// nonce in last metabox
wp_nonce_field( 'geol_options', 'geol_options_nonce' );
?>

<?php if ( $opts['dest'] ) : ?>

<table class="wp-list-table widefat striped">
	<thead>
		<tr><th><?php _e('Destinations','geol'); ?></th><th><?php _e('Clicks','geol'); ?></th></tr>
	</thead>
	<tbody>
	<?php if( isset( $opts['dest'] ) ) : ?>
		<?php foreach ( $opts['dest'] as $key => $data ) :
			if( empty( $data['url'] ) )
				continue;
			?>
			<tr>
				<td class="geol_stats_url"><?php echo $data['url']; ?></td>
				<td class="geol_stats_count"><?php echo $data['count_dest']; ?></td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if( !empty($opts['dest_default']) ) : ?>
		<tr>
			<td class="geol_stats_url"><strong><?php _e('Default','geol'); ?> :</strong> <?php echo $opts['dest_default']; ?></td>
			<td class="geol_stats_count"><?php echo $opts['click_default']; ?></td>
		</tr>
	<?php else : ?>
		<tr>
			<td class="geol_stats_url"><strong><?php _e('Default','geol'); ?> :</strong> <?php echo site_url(); ?></td>
			<td class="geol_stats_count"><?php echo $opts['click_default']; ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<br />
<div style="text-align: right;">
	<span class="geol_msg_reset"></span>
	<button class="button-secondary geol_reset"><?php _e('Reset Stats','geol'); ?></button>
</div>

<?php else : ?>

	<h3><?php _e('Please, first save destinations to see the stats','geol'); ?></h3>

<?php endif; ?>