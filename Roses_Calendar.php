<?php

class Roses_Calendar
{

	private $nages = [];
	private $competitions;

	public function __construct()
	{
		global $pagenow;

		//include plugin_dir_path(__FILE__) . 'Roses_Config.php';

		// no shortcode execution if in edit page 
		if ($pagenow != "post.php") {
			add_shortcode('roses_calendrier', array($this, 'render'));
			add_filter('widget_text', 'do_shortcode');
		}
	}

	public function render()
	{
		// Display calendar for 'nageur' only
		if (Roses_Config::isNageur()) {

			$_SESSION['competition_get_nonce'] = wp_create_nonce('competition_get_nonce');

			$this->load_scripts();

			echo '<script type="text/javascript" src="' . plugin_dir_url(__FILE__) . 'lib/caleandar-master/js/caleandar.js"></script>';
			echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';

			echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
				<link rel="stylesheet" href="' . plugin_dir_url(__FILE__) . 'css/plugin_style.css' . '">';

			$competitions = Roses_Config::getCompetitions();
			$competitions = json_decode(json_encode($competitions), true);

			require('Roses_Calendar_View.php');
		}
	}

	public function load_scripts()
	{
		wp_enqueue_script('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js');
		wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js');
		wp_enqueue_script('script', plugin_dir_url(__FILE__) . 'js/main.js');

		wp_register_style('plugin_style', plugin_dir_url(__FILE__) . 'css/plugin_style.css');
		wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
		wp_register_style('fontAwesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_register_style('caleandar', plugin_dir_url(__FILE__) . 'lib/caleandar-master/css/theme2.css');

		wp_enqueue_style('plugin_style');
		wp_enqueue_style('bootstrap');
		wp_enqueue_style('fontAwesome');
		wp_enqueue_style('caleandar');

		//Enable ajax calls
		wp_register_script('ajaxHandle', plugin_dir_url(__FILE__) . 'js/main.js', array(), false, true);
		wp_enqueue_script('ajaxHandle');
		wp_localize_script('ajaxHandle', 'ajax_object', array('ajaxurl' => plugin_dir_url(__FILE__) . 'admin-ajax.php'));
	}

	static function install()
	{
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}roses_competition (
			id int(11) AUTO_INCREMENT PRIMARY KEY,
			nom varchar(100) NOT NULL,
			type varchar(100) NOT NULL,
			date date NOT NULL,
			lieu varchar(100),
			lieu_rdv varchar(100),
			date_rdv datetime,
			date_max datetime,
			commentaire text(500),
			created_at datetime,
			last_updated_at datetime
		);");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}roses_nage (
			id int(11) AUTO_INCREMENT PRIMARY KEY,
			nage_field varchar(100) NOT NULL,
			nage varchar(100) NOT NULL,
			distance int(5) NOT NULL,
			label varchar(100) NOT NULL
		);");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}roses_nage_competition (
			id int(11) AUTO_INCREMENT PRIMARY KEY,
			id_competition int(11) NOT NULL,
			id_nage int(11) NOT NULL
		);");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}roses_participation (
			id int(11) AUTO_INCREMENT PRIMARY KEY,
			id_nage_competition int(11) NOT NULL,
			id_competition int(11) NOT NULL,
			id_user int(11) NOT NULL,
			participation int(1) NOT NULL,
			created_at datetime
		);");
	}

	static function uninstall()
	{
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_competition");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_nage");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_nage_competition");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_participation");
	}

	static function disable()
	{
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_competition");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_nage");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_nage_competition");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}roses_participation");
	}
}
