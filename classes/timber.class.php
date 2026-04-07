<?php

namespace App\Classes;

use Timber\Site;
use Timber\Timber;
use Twig\TwigFunction;

/**
 * Class StarterSite
 */
class StarterSite extends Site
{
	public function __construct()
	{
		$this->get_image_folder_path();

		add_action('after_setup_theme', array($this, 'theme_supports'));
		add_action('init', array($this, 'register_post_types'));
		add_action('init', array($this, 'register_taxonomies'));

		add_filter('timber/context', array($this, 'add_to_context'));
		add_filter('timber/twig', array($this, 'add_to_twig'));
		add_filter('timber/twig/environment/options', [$this, 'update_twig_environment_options']);

		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types()
	{
		// register_post_type( 'post_type_slug',
		//     array (
		//         'label'                 => __( 'Post Name', 'gd_start_theme' ),
		//         'description'           => __( 'Post Type Description', 'gd_start_theme' ),
		//         'supports'              => array('title', 'thumbnail', 'editor', 'revisions'),
		//         'taxonomies'            => array( 'taxonomy_name' ),
		//         'hierarchical'          => false,
		//         'public'                => true,
		//         'menu_position'         => 5,
		//         'menu_icon'             => 'dashicons-media-text',
		//         'has_archive'           => true,
		//         'exclude_from_search'   => false,
		//     )
		// );
	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies()
	{
		// register_taxonomy('taxonomy_name', 'post_type_slug',
		//     array(
		//         'hierarchical' => true,
		//         'label' => 'Taxonomy name',
		//         'query_var' => true,
		//         'rewrite' => array(
		//             'slug' => 'taxonomy_name',
		//             'with_front' => false
		//         )
		//     )
		// );
	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context($context)
	{
		$context['sidebar'] = Timber::get_sidebar('sidebar.php');
		$context['top_fejlec_szoveg'] = get_field('top_fejlec_szoveg', 'options');
		$context['lablec_hatterkep'] = get_field('lablec_hatterkep', 'options');
		$context['telefonszam'] = get_field('telefonszam', 'options');
		$context['email'] = get_field('email', 'options');
		$context['menu']  = Timber::get_menu();
		$context['lablec_menu']  = Timber::get_menu('lablec-menu');
		$context['site']  = $this;

		return $context;
	}

	public function theme_supports()
	{
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support('menus');
	}

	/** Images folder url
	 */
	protected function get_image_folder_path()
	{
		$this->image_url = get_template_directory_uri() . '/resources/images';
	}

	public function fishingfanatic_is_favourite( $product_id ) {
		if ( ! is_user_logged_in() ) return false;
		$favs = get_user_meta( get_current_user_id(), '_favourites', true );
		if ( ! is_array( $favs ) ) return false;
		return in_array( (int) $product_id, $favs );
	}

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @param Twig\Environment $twig get extension.
	 */
	public function add_to_twig($twig)
	{
		/**
		 * Required when you want to use Twig’s template_from_string.
		 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
		 */
		// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFunction(new TwigFunction('fishingfanatic_is_favourite', array($this, 'fishingfanatic_is_favourite')));

		return $twig;
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * @param array $options An array of environment options.
	 *
	 * @return array
	 */
	function update_twig_environment_options($options)
	{
		// $options['autoescape'] = true;

		return $options;
	}
}
