<?php
/**
 * AWP Reviews.
 *
 * @package   AWP_Reviews
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-awp-reviews-admin.php`
 *
 *
 * @package AWP_Reviews
 * @author  Your Name <email@example.com>
 */
class AWP_Reviews {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'awp-reviews';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'register_post_type') );

		add_filter( 'template_include', array( $this, 'template_loader' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_nopriv_reviewable_plugins', array($this, 'core_plugin_query' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if( $this->is_review() ){
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.min.css', array(), '3.4.5');
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if( $this->is_review() ){
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_localize_script( $this->plugin_slug . '-plugin-script', 'ajax_object',
            	array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.min.js', array('jquery'), '3.4.5', true );
		}
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	public function template_path(){
		return trailingslashit($this->plugin_slug);
	}

	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. We look for theme
	 * overrides in /theme/ by default
	 *
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		$find = array( '' );
		$file = '';
		$template_path = trailingslashit($this->plugin_slug);

		if ( is_single() && get_post_type() == 'review' ) {

			$file 	= 'single-review.php';
			$find[] = $file;
			$find[] = $template_path . $file;

		} elseif ( is_tax( 'review_cat' ) || is_tax( 'review_tag' ) ) {

			$term = get_queried_object();

			$file 		= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $template_path . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= $template_path . $file;

		} elseif ( is_post_type_archive( 'review' ) ) {

			$file 	= 'archive-review.php';
			$find[] = $file;
			$find[] = $template_path . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template )
				$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/views/templates/' . $file;
		}

		return $template;
	}

	public function is_review(){
		if( ( is_single() && get_post_type() == 'review' ) ||
			( is_tax( 'review_cat' ) || is_tax( 'review_tag' ) ) ||
			( is_post_type_archive( 'review' ) )
		  ) return true;
	}


	public function register_post_type(){

		$labels = $this->post_type_labels( 'review' );
	    $args = array(
	      'public' => true,
	      'labels'  => $labels
	    );
	    register_post_type( 'review', $args );
	}

	public function post_type_labels( $label="" ){
		$plural = ucwords( $this->pluralize($label) );
		$single = ucwords($label);
		$labels = array(
		'name' => $plural,
		'singular_name' => $single,
		'add_new' => 'Add New',
		'add_new_item' => 'Add New '.$single,
		'edit_item' => 'Edit '.$single,
		'new_item' => 'New '.$single,
		'all_items' => 'All '.$plural,
		'view_item' => 'View '.$single,
		'search_items' => 'Search '.$plural,
		'not_found' =>  'No '.$plural.' found',
		'not_found_in_trash' => 'No '.$plural.' found in Trash', 
		'parent_item_colon' => '',
		'menu_name' => $plural
		);
		return $labels;
	}

	public function pluralize( $word ) {
		$plural = array(
			'/(quiz)$/i'                => '\1zes',
			'/^(ox)$/i'                 => '\1en',
			'/([m|l])ouse$/i'           => '\1ice',
			'/(matr|vert|ind)ix|ex$/i'  => '\1ices',
			'/(x|ch|ss|sh)$/i'          => '\1es',
			'/([^aeiouy]|qu)ies$/i'     => '\1y',
			'/([^aeiouy]|qu)y$/i'       => '\1ies',
			'/(hive)$/i'                => '\1s',
			'/(?:([^f])fe|([lr])f)$/i'  => '\1\2ves',
			'/sis$/i'                   => 'ses',
			'/([ti])um$/i'              => '\1a',
			'/(buffal|tomat)o$/i'       => '\1oes',
			'/(bu)s$/i'                 => '1ses',
			'/(alias|status)/i'         => '\1es',
			'/(octop|vir)us$/i'         => '1i',
			'/(ax|test)is$/i'           => '\1es',
			'/s$/i'                     => 's',
			'/$/'                       => 's'
		);

		$uncountable = array( 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' );

		$irregular = array(
			'person'    => 'people',
			'man'       => 'men',
			'woman'     => 'women',
			'child'     => 'children',
			'sex'       => 'sexes',
			'move'      => 'moves'
		);

		$lowercased_word = strtolower( $word );

		foreach ( $uncountable as $_uncountable ) {
			if ( substr( $lowercased_word, ( -1 * strlen( $_uncountable ) ) ) == $_uncountable ) {
				return $word;
			}
		}

		foreach ( $irregular as $_plural=> $_singular ) {
			if ( preg_match( '/('.$_plural.')$/i', $word, $arr ) ) {
				return preg_replace( '/('.$_plural.')$/i', substr( $arr[0], 0, 1 ).substr( $_singular, 1 ), $word );
			}
		}

		foreach ( $plural as $rule => $replacement ) {
			if ( preg_match( $rule, $word ) ) {
				return preg_replace( $rule, $replacement, $word );
			}
		}
		return false;
	}

	public function core_plugin_query(){
		require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
		$paged 		= $_REQUEST[ 'page' ];

		$per_page 	= 5;
		$search 	= $_REQUEST[ 'search' ];

		$args = array( 'page' => $paged, 'per_page' => $per_page, 'search' => $search );
		

		$api = plugins_api( 'query_plugins', $args );

		$response = $api;

		if ( is_wp_error( $response ) )
			$response = $api->get_error_message();

		echo json_encode($response);die;

	}
}


/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function awp_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", AWP()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( AWP()->plugin_path() . "/views/templates/{$slug}-{$name}.php" ) ) {
		$template = AWP()->plugin_path() . "/views/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", AWP()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'awp_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

