
<div class="wrap">

	<h2><?php _e( 'Nginx', 'nginx' ); ?></h2>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'nginx' ); ?>
		<?php do_settings_sections( 'nginx' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Cache Zone Path', 'nginx' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="nginx_settings[zone_path]" value="<?php echo esc_attr( get_option( 'nginx_settings' )[ 'zone_path' ] ); ?>" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<?php echo get_submit_button( null, 'primary large', 'submit', false ); ?>
			&nbsp;
			<a href="<?php echo admin_url( add_query_arg( 'action', 'purge-zone', $this->admin_url ) ); ?>" class="button button-secondary button-large delete"><?php _e( 'Purge Cache Zone', 'nginx' ); ?></a>
		</p>

	</form>

</div>
