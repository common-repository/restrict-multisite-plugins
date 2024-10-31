<?php
/*
Plugin Name: Restrict Multisite Plugins
Description: Replicates some of the per-site theme toggling functionality for plugins. Affects all sites activated on.
Version: 1.1.3
Author: Adam Harley
Author URI: http://adamharley.co.uk
Plugin URI: http://adamharley.co.uk/wordpress-plugins/restrict-multisite-plugins/
*/


if( !class_exists('RestrictAllowedPlugins') ) {
class RestrictAllowedPlugins {

function filter($plugins) {
	if( current_user_can( 'manage_network_plugins' ) ) // Disable restrictions for network admins
		return $plugins;

	$allowed_plugins = get_site_option( 'allowedplugins' );
	
	if( !is_array( $allowed_plugins ) )
		return $plugins;

	unset( $plugins[ plugin_basename(__FILE__) ] );

	foreach( array_keys($plugins) as $key ) {
		$plugin_key = str_replace( '/', ':', $key );
		if( !isset( $allowed_plugins[ $plugin_key ] ) )
			unset( $plugins[ $key ] );
	}

	return $plugins;
}


function setup_admin() {
	if( function_exists('is_network_admin') )
		$page = add_plugins_page( __( 'Restricted Plugins', 'restrict-multisite-plugins' ), __( 'Restrictions', 'restrict-multisite-plugins' ), 'manage_network_plugins', 'ms-plugins', array('RestrictAllowedPlugins','admin') );
	else
		$page = add_submenu_page( 'ms-admin.php', __( 'Restricted Plugins', 'restrict-multisite-plugins' ), __( 'Plugins', 'restrict-multisite-plugins' ), 'manage_network_plugins', 'ms-plugins', array('RestrictAllowedPlugins','admin') );

	add_contextual_help( $page,
		'<p>' . __( 'This screen enables and disables the inclusion of plugins available to choose in the Plugins menu for each site. It does not activate or deactivate which plugin a site is currently using.', 'restrict-multisite-plugins' ) . '</p>' .
		'<p>' . __( 'If the network admin disables a plugin that is in use, it will remain activated on any sites using it but cannot be disabled by site admins.', 'restrict-multisite-plugins' ) . '</p>'
	);
}


function admin() {
	if( isset($_POST['plugin']) ) {
		$plugin_states = array();
		foreach( (array)$_POST['plugin'] as $plugin => $plugin_state ) {
			if( $plugin_state == 'enabled' )
				$plugin_states[ $plugin ] = 1;
			else
				unset( $plugin_states[ $plugin ] );
		}
		$updated = update_site_option( 'allowedplugins', $plugin_states );
	}

	if ( isset($updated) && $updated ) {
		?>
		<div id="message" class="updated"><p><?php _e( 'Site plugins saved.', 'restrict-multisite-plugins' ) ?></p></div>
		<?php
	}

	$plugins = get_plugins();
	unset( $plugins[ plugin_basename(__FILE__) ] );
	foreach ( $plugins as $plugin_file => $plugin_data ) {
		if ( $plugin_data['Network'] === true || is_plugin_active_for_network( $plugin_file ) )
			unset( $plugins[ $plugin_file ] );
		else
			$plugins[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
	}
	$allowed_plugins = get_site_option( 'allowedplugins' );
?>
<div class="wrap">
	<form method="post">
		<?php screen_icon('plugins') ?>
		<h2><?php _e( 'Restricted Plugins', 'restrict-multisite-plugins' ) ?></h2>
		<p><?php _e( 'Plugins must be enabled for your network before they will be available to individual sites.', 'restrict-multisite-plugins' ) ?></p>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes', 'restrict-multisite-plugins' ) ?>" /></p>
		<table class="widefat">
			<thead>
				<tr>
					<th style="width:15%;"><?php _e( 'Enable', 'restrict-multisite-plugins' ) ?></th>
					<th style="width:25%;"><?php _e( 'Plugin', 'restrict-multisite-plugins' ) ?></th>
					<th style="width:10%;"><?php _e( 'Version', 'restrict-multisite-plugins' ) ?></th>
					<th style="width:60%;"><?php _e( 'Description', 'restrict-multisite-plugins' ) ?></th>
				</tr>
			</thead>
			<tbody id="plugins">
			<?php
			$total_plugin_count = $allowed_plugins_count = 0;
			$class = '';
			foreach ( (array) $plugins as $key => $plugin ) {
				$total_plugin_count++;
				$plugin_key = str_replace( '/', ':', $key );
				$plugin_key = esc_html( $plugin_key );
				$class = ( 'alt' == $class ) ? '' : 'alt';
				$class1 = $enabled = $disabled = '';
				$enabled = $disabled = false;

				if ( isset( $allowed_plugins[ $plugin_key ] ) == true ) {
					$enabled = true;
					$allowed_plugins_count++;
					$class1 = 'active';
				}
				else
					$disabled = true;
				?>
				<tr valign="top" class="<?php echo $class . ' ' . $class1; ?>">
					<td>
						<label><input name="plugin[<?php echo $plugin_key ?>]" type="radio" id="enabled_<?php echo $plugin_key ?>" value="enabled" <?php checked( $enabled ) ?> /> <?php _e( 'Yes', 'restrict-multisite-plugins' ) ?></label>
						&nbsp;&nbsp;&nbsp;
						<label><input name="plugin[<?php echo $plugin_key ?>]" type="radio" id="disabled_<?php echo $plugin_key ?>" value="disabled" <?php checked( $disabled ) ?> /> <?php _e( 'No', 'restrict-multisite-plugins' ) ?></label>
					</td>
					<th scope="row" style="text-align:left;"><?php echo $plugin['Name'] ?></th>
					<td><?php echo $plugin['Version'] ?></td>
					<td><?php echo $plugin['Description'] ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes', 'restrict-multisite-plugins' ) ?>" />
		</p>
	</form>

	<h3><?php _e( 'Total', 'restrict-multisite-plugins' ) ?></h3>
	<p>
		<?php printf( __( 'Plugins Installed: %d', 'restrict-multisite-plugins' ), $total_plugin_count ) ?>
		<br />
		<?php printf( __( 'Plugins Enabled: %d', 'restrict-multisite-plugins' ), $allowed_plugins_count ) ?>
	</p>
</div>
<?php
}

}
}


if ( is_admin() ) {
	add_filter( 'all_plugins', array('RestrictAllowedPlugins','filter') );

	if ( ! function_exists( 'is_plugin_active_for_network' ) )
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	if ( function_exists('is_network_admin') && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
		add_action( 'network_admin_menu', array('RestrictAllowedPlugins','setup_admin') );
	else
		add_action( 'admin_menu', array('RestrictAllowedPlugins','setup_admin') );
}