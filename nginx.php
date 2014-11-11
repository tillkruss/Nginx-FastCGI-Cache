<?php
/*
Plugin Name: Nginx
Plugin URI:
Description:
Author: Till Krüss
Version: 1.0
Author URI: http://till.kruss.me/
*/

class Nginx {

	protected $admin_url = 'tools.php?page=nginx';

	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'load-tools_page_nginx', array( $this, 'foobar' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 100 );

	}

	public function register_settings() {

		register_setting( 'nginx', 'nginx_settings' );

	}

	public function get_cache_zone_path() {

		$settings = get_option( 'nginx_settings' );

		return isset( $settings[ 'zone_path' ] ) ? $settings[ 'zone_path' ] : false;

	}

	public function foobar() {

		if ( ! is_writable( $this->get_cache_zone_path() ) ) {

			add_settings_error(
				'nginx_settings',
				'zone_path',
				__( 'Warning: &ldquo;Cache Zone Path&rdquo; is not writable.', 'nginx' )
			);

		}

		if ( isset( $_GET[ 'message' ] ) && $_GET[ 'message' ] === 'zone-purged' ) {

			add_settings_error(
				'nginx_settings',
				'zone_path',
				__( 'Cache zone purged.', 'nginx' ),
				'updated'
			);

		}

		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'purge-zone' ) {

			$this->purge( array(
				'action' => 'purge-zone',
				'cache' => 'till.kruss.me'
			) );

			wp_safe_redirect( admin_url( add_query_arg( 'message', 'zone-purged', $this->admin_url ) ) );

		}

	}

	public function add_admin_bar_node( $wp_admin_bar ) {

		$wp_admin_bar->add_node( array(
			'id' => 'nginx',
			'title' => 'Nginx Cache',
			'href' => admin_url( $this->admin_url )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'nginx',
			'id' => 'purge-zone',
			'title' => 'Purge Cache Zone',
			'href' => admin_url( add_query_arg( 'action', 'purge-zone', $this->admin_url ) )
		) );

	}

	public function add_admin_menu_page() {

		add_management_page(
			'Nginx',
			'Nginx',
			'manage_options',
			'nginx',
			array( $this, 'show_settings_page' )
		);

	}

	public function show_settings_page() {
		require_once __DIR__ . '/admin.php';
	}

	public function purge( $parameters ) {

		if ( isset( $parameters[ 'action' ] ) && file_exists( $this->get_cache_zone_path() ) ) {

			if ( $parameters[ 'action' ] === 'purge-zone' ) {

				exec( 'rm -rf ' . $this->get_cache_zone_path() );

			} elseif ( $parameters[ 'action' ] === 'purge-url' && isset( $parameters[ 'url' ] ) ) {

				$url = parse_url( $parameters[ 'url' ] );
				$method = isset( $parameters[ 'method' ] ) ? $parameters[ 'method' ] : 'GET';

				if ( $url !== false ) {

					$hash = md5( $url[ 'scheme' ] . $url[ 'host' ] . $method . $url[ 'path' ] );
					$url_path = realpath( sprintf( '%s/%s/%s/%s', $this->get_cache_zone_path(), substr( $hash, -1 ), substr( $hash, -3, 2 ), $hash ) );

					if ( $url_path !== false ) {
						exec( 'rm -f ' . $url_path );
					}

				}

			}

		}

		return false;

	}

}

new Nginx;
