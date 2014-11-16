<?php
/*
Plugin Name: Nginx Cache
Plugin URI: http://wordpress.org/plugins/nginx-cache/
Description: ...
Version: 1.0
Text Domain: nginx-cache
Domain Path: /languages
Author: Till Krüss
Author URI: http://till.kruss.me/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

class NginxCache {

	protected $admin_page = 'tools.php?page=nginx-cache';

	public function __construct() {

		load_plugin_textdomain( 'nginx-cache', false, 'nginx-cache/languages' );

		add_filter( 'option_nginx_cache_path', 'sanitize_text_field' );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 100 );
		add_action( 'load-tools_page_nginx-cache', array( $this, 'do_admin_actions' ) );
		add_action( 'load-tools_page_nginx-cache', array( $this, 'add_settings_notices' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_link' ) );

	}

	public function register_settings() {

		register_setting( 'nginx-cache', 'nginx_cache_path', 'sanitize_text_field' );

	}

	public function add_settings_notices() {

		$path_error = $this->is_valid_path();

		if ( isset( $_GET[ 'message' ] ) ) {

			// show cache purge success message
			if ( $_GET[ 'message' ] === 'cache-purged' ) {
				add_settings_error( '', 'nginx_cache_path', __( 'Cache purged.', 'nginx-cache' ), 'updated' );
			}

			// show cache purge failure message
			if ( $_GET[ 'message' ] === 'purge-cache-failed' ) {
				add_settings_error( '', 'nginx_cache_path', sprintf( __( 'Cache could not be purged. %s', 'nginx-cache' ), wptexturize( $path_error ) ) );
			}

		} elseif ( is_wp_error( $path_error ) ) {

			// show cache path problem message
			add_settings_error( '', 'nginx_cache_path', wptexturize( $path_error->get_error_message( 'fs' ) ) );

		}

	}

	public function do_admin_actions() {

		// purge cache
		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'purge-cache' ) {

			$result = $this->purge_zone();
			wp_safe_redirect( admin_url( add_query_arg( 'message', is_wp_error( $result ) ? 'purge-cache-failed' : 'cache-purged', $this->admin_page ) ) );
			exit;

		}

	}

	public function add_admin_bar_node( $wp_admin_bar ) {

		// add "Nginx" node to admin-bar
		$wp_admin_bar->add_node( array(
			'id' => 'nginx-cache',
			'title' => 'Nginx',
			'href' => admin_url( $this->admin_page )
		) );

		// add "Purge Cache" to "Nginx" node
		$wp_admin_bar->add_node( array(
			'parent' => 'nginx-cache',
			'id' => 'purge-cache',
			'title' => 'Purge Cache',
			'href' => admin_url( add_query_arg( 'action', 'purge-cache', $this->admin_page ) )
		) );

	}

	public function add_admin_menu_page() {

		// add "Tools" sub-page
		add_management_page(
			'Nginx Cache',
			'Nginx',
			'manage_options',
			'nginx-cache',
			array( $this, 'show_settings_page' )
		);

	}

	public function show_settings_page() {
		require_once plugin_dir_path( __FILE__ ) . '/settings-page.php';
	}

	public function add_plugin_actions_links( $links ) {

		// add settings link to plugin actions
		return array_merge(
			array( '<a href="' . admin_url( $this->admin_page ) . '">Settings</a>' ),
			$links
		);

	}

	private function is_valid_path() {

		global $wp_filesystem;

		if ( $this->initialize_filesystem() ) {

			$path = get_option( 'nginx_cache_path' );

			if ( ! $wp_filesystem->exists( $path ) ) {
				return new WP_Error( 'fs', __( '"Cache Zone Path" does not exist.', 'nginx-cache' ) );
			}

			if ( ! $wp_filesystem->is_dir( $path ) ) {
				return new WP_Error( 'fs', __( '"Cache Zone Path" is not a directory.', 'nginx-cache' ) );
			}

			if ( ! $wp_filesystem->is_writable( $path ) ) {
				return new WP_Error( 'fs', __( '"Cache Zone Path" is not writable.', 'nginx-cache' ) );
			}

			$list = $wp_filesystem->dirlist( $path, true, true );
			if ( ! $this->validate_dirlist( $list ) ) {
				return new WP_Error( 'fs', __( '"Cache Zone Path" does not appear to be a Nginx cache zone directory.', 'nginx-cache' ) );
			}

			return true;

		}

		return new WP_Error( 'fs', __( 'Filesystem API could not be initialized.', 'nginx-cache' ) );

	}

	private function validate_dirlist( $list ) {

		foreach ( $list as $item ) {

			// validate subdirectories recursively
			if ( $item[ 'type' ] === 'd' && ! $this->validate_dirlist( $item[ 'files' ] ) ) {
				return false;
			}

			// abort if file is not a md5 hash
			if ( $item[ 'type' ] === 'f' && ( strlen( $item[ 'name' ] ) !== 32 || ! ctype_xdigit( $item[ 'name' ] ) ) ) {
				return false;
			}

		}

		return true;

	}

	private function purge_zone() {

		global $wp_filesystem;

		$path = get_option( 'nginx_cache_path' );
		$path_error = $this->is_valid_path();

		// abort if cache zone path is not valid
		if ( is_wp_error( $path_error ) ) {
			return $path_error;
		}

		// remove cache directory (recursively)
		$wp_filesystem->rmdir( $path, true );

		return true;

	}

	private function initialize_filesystem() {

		ob_start(); // buffer output

		if ( ( $credentials = request_filesystem_credentials( '' ) ) === false ) {
			ob_end_clean(); // prevent display of filesystem credentials form
			return false;
		}

		if ( ! WP_Filesystem( $credentials ) ) {
			return false;
		}

		return true;

	}

}

new NginxCache;