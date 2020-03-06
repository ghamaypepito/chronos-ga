<?php

class BrizyPro_Main {

	private $assetsUrl;

	public function __construct() {
		$this->assetsUrl = BRIZY_PRO_PLUGIN_URL . '/public';
	}

	public function run() {

		$this->registerCustomPosts();

		if ( is_admin() ) {
			if ( ! defined( 'BRIZY_VERSION' ) ) {
				// show a notice if the free version of the plugin is not installed
				add_action( 'admin_notices', array( $this, 'inactivePlugin' ) );

				return;
			}

			if ( defined( 'BRIZY_VERSION' ) && version_compare( BRIZY_VERSION, BRIZY_REQUIRED_VERSION ) < 0 ) {
				// show a notice if the free version of the plugin is not installed
				add_action( 'admin_notices', array( $this, 'updateNeeded' ) );

				return;
			}

			add_action( 'wp_loaded', array( $this, 'wordpressLoaded' ), 11 ); // we need to run this after free plugin
		}

		if ( ! defined( 'BRIZY_VERSION' ) ) {
			return;
		}

		add_filter( 'brizy_editor_config_texts', array( $this, 'filterBrizyEditorConfigTexts' ) );
		add_filter( 'brizy_editor_config', array( $this, 'filterBrizyEditorConfig' ) );
		add_filter( 'brizy_editor_config', array( $this, 'addConfigDynamicContent' ) );
		add_action( 'brizy_editor_enqueue_scripts', array( $this, 'actionBrizyEditorEnqueueScripts' ) );

		add_filter( 'brizy_compiler_params', array( $this, 'filterBrizyCompilerParams' ) );
		add_filter( 'brizy_providers', array( $this, 'brizy_placeholders' ), 10, 2 );
		add_filter( 'brizy_context_create', array( $this, 'createDynamicContentContext' ), 10, 2 );
		add_filter( 'brizy_loop_context_create', array( $this, 'createDynamicContentLoopContext' ), 10, 2 );
		add_action( 'init', array( $this, 'wordpressInit' ) );
		add_action( 'init', array( $this, 'loadShortcodes' ) );
		add_action( 'wp_loaded', array( $this, 'flushRewriteRules' ) );
		add_filter( 'rewrite_rules_array', array( $this, 'addPostLoopPaginationRewriteRules' ) );
	}

	public function registerCustomPosts() {
		// register all custom posts here
	}

	public function wordpressInit() {
		try {
			new BrizyPro_Admin_Forms_Proxy();
			new BrizyPro_Forms_ApiExtender();

			BrizyPro_Admin_Settings::_init();
			BrizyPro_Admin_Integrations::_init();
			BrizyPro_Admin_License::_init();
			BrizyPro_Admin_WhiteLabel::_init();
		} catch ( Exception $e ) {
		}
	}

	public function loadShortcodes() {

		new BrizyPro_Shortcode_Breadcrumbs();
	}

	public function wordpressLoaded() {

		BrizyPro_Admin_Updater::_init( BrizyPro_Config::UDPATE_LICENSE, 'brizy-pro/brizy-pro.php' );
	}

	public function brizy_placeholders( $providers, $context ) {

		if ( class_exists( 'acf' ) ) {
			$providers[] = new BrizyPro_Content_Providers_Acf( $context );
		}

		if ( function_exists( 'pods' ) ) {
			$providers[] = new BrizyPro_Content_Providers_Pods( $context );
		}

		if ( class_exists( 'Types_Main' ) ) {
			$providers[] = new BrizyPro_Content_Providers_Toolset( $context );
		}

		if ( class_exists( 'RWMB_Loader' ) ) {
			$providers[] = new BrizyPro_Content_Providers_Metabox( $context );
		}

//		if ( class_exists( 'WooCommerce' ) ) {
//			$this->providers[] = new BrizyPro_Content_Providers_Woocommerce( $context );
//		}

		$providers[] = new BrizyPro_Content_Providers_Wp( $context );

		return $providers;
	}

	/**
	 * Draw a notice if the FREE plugin is not installed
	 */
	public function inactivePlugin() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Brizy Pro is inactive. Please install and activate Brizy FREE first.!' ); ?></p>
        </div>
		<?php
	}

	/**
	 * Draw a notice if the FREE plugon is not installed
	 */
	public function updateNeeded() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo sprintf( __( 'Brizy Pro %s requires Brizy FREE to be %s or newer', 'brizy' ), BRIZY_PRO_VERSION, BRIZY_REQUIRED_VERSION ); ?></p>
        </div>
		<?php
	}

	/**
	 * @param Brizy_Content_Context $context
	 * @param WP_Post|null $wp_post
	 *
	 * @return Brizy_Content_Context
	 */
	public function createDynamicContentContext( Brizy_Content_Context $context, WP_Post $wp_post = null ) {

		if ( class_exists( 'WooCommerce' ) ) {
			global $product;
			$aProduct = $product instanceof WC_Product ? $product : wc_get_product( $wp_post );
			$context->setProduct( $aProduct );
		}

		$object = array( 'object_type' => '', 'object_id' => '' );

		if ( $wp_post ) {
			$object = array( 'object_type' => 'post', 'object_id' => $wp_post->ID );
		}

		if ( is_author() ) {
			$authorId = get_queried_object_id();
			$context->setAuthor( $authorId );
			$object = array( 'object_type' => 'user', 'object_id' => $authorId );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$context->setTerm( get_queried_object()->term_id );
			$object = array(
				'object_type' => 'tax',
				'object_id'   => get_queried_object()->term_id,
				'tax'         => get_queried_object()->taxonomy
			);
		}

		$context->setObjectData( $object );

		return $context;
	}


	/**
	 * @param Brizy_Content_Context $context
	 * @param WP_Post|null $wp_post
	 *
	 * @return Brizy_Content_Context
	 */
	public function createDynamicContentLoopContext( Brizy_Content_Context $context, WP_Post $wp_post = null ) {

		if ( class_exists( 'WooCommerce' ) ) {
			global $product;
			$aProduct = $product instanceof WC_Product ? $product : wc_get_product( $wp_post );
			$context->setProduct( $aProduct );
		}

		$context->setObjectData( array( 'object_type' => 'post', 'object_id' => $wp_post->ID ) );

		return $context;
	}

	public function addPostLoopPaginationRewriteRules( $allrules ) {

		$brizyPaged = '/bpage/?([0-9]{1,})/?';
		$newRules   = array();
		foreach ( $allrules as $regex => $url ) {

			// 1. add brizyPaged reqest in $regex
			// 2. find the 'matches' count in $url
			// 2. add bpage match in $url

			$regex              = str_replace( '/?$', $brizyPaged, $regex );
			$count              = preg_match_all( '/\$matches\[\d+\]/i', $url ) + 1;
			$url                .= "&bpage=\$matches[{$count}]";
			$url                = preg_replace( '/paged=\$matches\[\d+\]/i', "paged=1", $url );
			$newRules[ $regex ] = $url;
		}


		$newRules['bpage/?([0-9]{1,})/?'] = 'index.php?bpage=$matches[1]';

		return array_merge( $newRules, $allrules );
	}

	public function flushRewriteRules() {

		add_rewrite_tag( '%bpage%', '([^&]+)' );


		$get_option = get_option( 'brizy-pro-rewrite-rules-updated', false );

		if ( $get_option ) {
			return;
		}

		flush_rewrite_rules();

		add_option( 'brizy-pro-rewrite-rules-updated', 1 );

		return;
	}

	/**
	 * @param $texts
	 *
	 * @return array
	 */
	public function filterBrizyEditorConfigTexts( $texts ) {

		$texts['Dynamic content'] = __( 'Dynamic content', 'brizy' );

		return $texts;
	}

	/**
	 * @param $config
	 *
	 * @return mixed
	 * @throws Brizy_Editor_Exceptions_NotFound
	 * @throws Exception
	 */
	public function filterBrizyEditorConfig( $config ) {

		$accessType = null;

		$accessType = 'admin';
		if ( current_user_can( Brizy_Admin_Capabilities::CAP_EDIT_WHOLE_PAGE ) ) {
			$accessType = 'admin';
		} elseif ( current_user_can( Brizy_Admin_Capabilities::CAP_EDIT_CONTENT_ONLY ) ) {
			$accessType = 'limited';
		}

		$config['user']['role'] = $accessType;


		$config['wp']['api']['getServiceAccounts']      = BrizyPro_Forms_ApiExtender::AJAX_GET_SERVICE_ACCOUNTS;
		$config['wp']['api']['deleteServiceAccount']    = BrizyPro_Forms_ApiExtender::AJAX_DELETE_SERVICE_ACCOUNT;
		$config['wp']['api']['authenticateIntegration'] = BrizyPro_Forms_ApiExtender::AJAX_AUTHENTICATE_INTEGRATION;
		$config['wp']['api']['getIntegrationLists']     = BrizyPro_Forms_ApiExtender::AJAX_GET_LISTS;
		$config['wp']['api']['getIntegrationFields']    = BrizyPro_Forms_ApiExtender::AJAX_GET_FIELDS;
		$config['wp']['api']['createIntegrationGroup']  = BrizyPro_Forms_ApiExtender::AJAX_CREATE_GROUP;
		$config['wp']['api']['createIntegrationGroup']  = BrizyPro_Forms_ApiExtender::AJAX_CREATE_GROUP;
		$config['wp']['api']['getListProperties']       = BrizyPro_Forms_ApiExtender::AJAX_GET_GROUP_PROPERTIES;
		$config['wp']['api']['getAccountProperties']    = BrizyPro_Forms_ApiExtender::AJAX_GET_ACCOUNT_PROPERTIES;

		//$config['wp']['api']['createIntegrationFields'] = BrizyPro_Forms_ApiExtender::AJAX_CREATE_FIELDS;


		return $config;
	}

	public function addConfigDynamicContent( $config ) {

		$post                     = Brizy_Editor_Post::get( $config['wp']['page'] );
		$context                  = new Brizy_Content_Context( Brizy_Editor_Project::get(), $post, $post->get_wp_post(), null );
		$provider                 = new Brizy_Content_PlaceholderProvider( $context );
		$config['dynamicContent'] = $provider->getGroupedPlaceholders();
		$config['taxonomies']     = $this->getTaxonomyList( $config );

		if ( $post->uses_editor() && Brizy_Admin_Templates::CP_TEMPLATE !== $post->get_wp_post()->post_type ) {

			if ( isset( $config['dynamicContent']['richText']['wp'] ) ) {

				foreach ( $config['dynamicContent']['richText']['wp'] as $index => $placeholder ) {

					if ( is_a( $placeholder, 'BrizyPro_Content_Placeholders_PostContent' ) ) {
						array_splice( $config['dynamicContent']['richText']['wp'], $index, 1 );
						break;
					}
				}
			}
		}

		$config['pro'] = array(
			'version' => BRIZY_PRO_EDITOR_VERSION,
			'urls'    => BrizyPro_Config::getConfigUrls()
		);

		return $config;
	}


	/**
	 * @internal
	 */
	public function actionBrizyEditorEnqueueScripts() {
		wp_enqueue_script( 'brizy-pro-editor', $this->assetsUrl . '/editor-build/js/editor.pro.js', array( 'brizy-editor' ), BRIZY_PRO_EDITOR_VERSION, true );
	}

	/**
	 * @internal
	 */
	public function filterBrizyCompilerParams( $params ) {
		$params['has_pro']     = true;
		$params['pro_version'] = BRIZY_PRO_EDITOR_VERSION;

		return $params;
	}

	/**
	 * @return array
	 */
	private function getTaxonomyList( $config ) {

		$taxs = get_taxonomies( array( 'public' => true, 'show_ui' => true ), 'objects' );

		$result = array_map( function ( $tax ) {

			$terms = (array) get_terms( array( 'taxonomy' => $tax->name, 'hide_empty' => false ) );

			return (object) array(
				'name'  => $tax->name,
				'label' => $tax->labels->name,
				'terms' => array_map( function ( $term ) {
					return (object) array(
						'id'   => $term->term_id,
						'name' => $term->name,
					);
				}, $terms )
			);

		}, $taxs );

		$taxonomies = array_values( array_filter( $result, function ( $term ) {
			return count( $term->terms ) > 0;
		} ) );

		return $taxonomies;
	}

}