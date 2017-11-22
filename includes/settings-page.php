
<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">

	<h1><?php _e( 'Nginx Cache', 'nginx' ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'nginx-cache' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Cache Zone Path', 'nginx-cache' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="nginx_cache_path" placeholder="/data/nginx/cache" value="<?php echo esc_attr( get_option( 'nginx_cache_path' ) ); ?>" />
					<p class="description"><?php _e( 'The absolute path to the location of the cache zone, specified in the Nginx <code>fastcgi_cache_path</code> or <code>proxy_cache_path</code> directive.', 'nginx-cache' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Purge Cache', 'nginx-cache' ); ?></th>
				<td>
					<label for="nginx_auto_purge">
						<input name="nginx_auto_purge" type="checkbox" id="nginx_auto_purge" value="1" <?php checked( get_option( 'nginx_auto_purge' ), '1' ); ?> />
						<?php _e( 'Automatically flush the cache when content changes', 'nginx-cache' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<p class="submit">
			<?php echo get_submit_button( null, 'primary large', 'submit', false ); ?>
			&nbsp;
			<a href="<?php echo wp_nonce_url( admin_url( add_query_arg( 'action', 'purge-cache', $this->admin_page ) ), 'purge-cache' ); ?>" class="button button-secondary button-large delete<?php if ( is_wp_error( $this->is_valid_path() ) ) : ?> disabled<?php endif; ?>"><?php _e( 'Purge Cache', 'nginx-cache' ); ?></a>
		</p>

	</form>

</div>
