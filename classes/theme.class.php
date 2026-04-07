<?php

namespace App\Classes;

class GallaidesignTheme
{
    private string $theme_url;
    private string $theme_assets;
	private string $theme_images;
    private array $urls; // Url array to javascript
	private bool $useSlider = true; // Toggle slider

	/**
	 * Theme constructor
	 */
	public function __construct()
	{
		$this->theme_url = get_template_directory_uri();
		$this->theme_assets = $this->theme_url . '/resources/';
		$this->theme_images = $this->theme_assets . 'images/';

		$this->urls = [
			'baseUrl' => get_home_url(),
			'themeUrl' => $this->theme_url,
			'assetsUrl' => $this->theme_assets,
			'imagesUrl' => $this->theme_images
		];

		if (!is_admin()) {
			$this->init();
		} else {
			add_action('admin_init', [$this, 'includeAjaxFiles']);
			add_action('admin_enqueue_scripts', [$this, 'includeAdminScripts']);
			// add_filter('image_size_names_choose', [$this, 'getCustomImageSizes']);
		}
	}

    	/**
	 * Initialize theme methods
	 */
	public function init()
	{
		if (is_user_logged_in()) {
			show_admin_bar(true);
		} else {
			show_admin_bar(false);
		}

		if ($this->useSlider === true) {
			add_action('wp_enqueue_scripts', [$this, 'includeCarouselAssets']);
		}

		add_action('wp_enqueue_scripts', [$this, 'includeStyles']);
		add_action('wp_enqueue_scripts', [$this, 'includeScripts']);
		add_action('init', [$this, 'disable_emojis']);
		// add_action('init', [$this, 'setCustomImageSizes']);
	}

    /**
	 * Include styles
	 */
	public function includeStyles()
	{
		wp_enqueue_style('app-style', get_template_directory_uri() . '/public/styles/app.css', array(), filemtime(get_template_directory() . '/public/styles/app.css'), 'all');
	}

    /**
	 * Include scripts
	 */
	public function includeScripts()
	{
		wp_enqueue_script('nav-js', get_template_directory_uri() . '/public/scripts/navigation.js', array('jquery'), filemtime(get_template_directory() . '/public/scripts/navigation.js'), true);
		wp_enqueue_script('app-js', get_template_directory_uri() . '/public/scripts/app.js', array('jquery'), filemtime(get_template_directory() . '/public/scripts/app.js'), true);
		wp_localize_script('app-js', 'urls', $this->urls);

		if ( is_user_logged_in() ) {
			wp_localize_script('app-js', 'FF_Favourites', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('ff_favourites_nonce'),
			]);
		}
	}

    /**
	 * Include admin scripts
	 */
	public function includeAdminScripts()
	{
		wp_enqueue_script('admin-js', get_template_directory_uri() . '/public/scripts/admin.js', array('jquery'), filemtime(get_template_directory() . '/public/scripts/admin.js'), true);
		wp_localize_script('admin-js', 'urls', $this->urls);
	}

    /**
	 * Include slider assets
	 */
	public function includeCarouselAssets()
	{
		wp_enqueue_style('slider-style', 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css');
		wp_enqueue_script('slider-js', 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js');
	}

    /**
	 * Include ajax files
	 */
	public function includeAjaxFiles()
	{
        if ($handle = opendir(__DIR__ . '/../handlers')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					include __DIR__ . '/../handlers/' . $file;
					$filename = explode('.', $file)[0];
	
					add_action('wp_ajax_'.$filename, $filename);
					add_action('wp_ajax_nopriv_'.$filename, $filename);
				}
			}

			closedir($handle);
		}
	}

    /**
	 * Deleting emoji css
	 */
	public function disable_emojis()
	{
		// remove from frontend
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');

		// remove from admin
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('admin_print_styles', 'print_emoji_styles');

		// remove from other locations (rss etc...)
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	}

    /**
	 * Adding custom image sizes
	 */
	public function setCustomImageSizes()
	{
		add_image_size('gallery_thumbnail', 300, 300, ['center', 'center']);
	}

	/**
	 * Displaying custom image sizes in admin
	 */
	public function getCustomImageSizes($old_sizes)
	{
		$new_sizes = array(
			'gallery_thumbnail' => __('Galéria thumbnail', 'gd-wordpress-theme'),
		);

		return array_merge($old_sizes, $new_sizes);
	}
}

?>