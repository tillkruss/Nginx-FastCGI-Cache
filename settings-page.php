
<div class="wrap">

	<h2><?php _e( 'Nginx Cache', 'nginx' ); ?></h2>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'nginx-cache' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Cache Zone Path', 'nginx' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="nginx_cache_path" value="<?php echo esc_attr( get_option( 'nginx_cache_path' ) ); ?>" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<?php echo get_submit_button( null, 'primary large', 'submit', false ); ?>
			&nbsp;
			<a href="<?php echo admin_url( add_query_arg( 'action', 'purge-cache', $this->admin_page ) ); ?>" class="button button-secondary button-large delete<?php if ( is_wp_error( $this->is_valid_path() ) ) : ?> disabled<?php endif; ?>"><?php _e( 'Purge Cache', 'nginx' ); ?></a>
		</p>

	</form>

</div>
