<?php

/*
Plugin Name: Roses
Description: Plugin d'inscription pour compétitions de natation.
Version: 1.1
Author: Maxime Belbeoch
License: open
*/

class Roses_Plugin
{

    protected $msgGlobal;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        add_action('wp_head', [$this, 'myplugin_ajaxurl']);

        // disable user access to wp-admin
        //add_action('init', [$this, 'blockusers_init']);
        //add_filter( 'show_admin_bar' , [$this, 'my_function_admin_bar']);

        add_role('nageur', 'Nageur', ['read' => true]);

        register_activation_hook(__FILE__, array('Roses_Calendar', 'install'));
        register_uninstall_hook(__FILE__, array('Roses_Calendar', 'uninstall'));
        register_deactivation_hook(__FILE__, array('Roses_Calendar', 'disable'));

        include plugin_dir_path(__FILE__) . 'Roses_Calendar.php';
        include plugin_dir_path(__FILE__) . 'admin-ajax.php';

        set_error_handler([$this, '__errorHandler']);

        session_start();

        new Roses_Calendar();
    }

    public function add_admin_menu()
    {
        add_menu_page('Roses &middot; Plugin Calendrier', 'Roses', 'manage_options', 'roses', array($this, 'page_plugin_html'), 'dashicons-calendar-alt');
    }

    public function myplugin_ajaxurl()
    {
        echo '<script type="text/javascript">
                var ajaxurl = "' . admin_url('admin-ajax.php') . '";
              </script>';
    }

    public function blockusers_init()
    {
        if (
            is_admin() && !current_user_can('administrator') &&
            !(defined('DOING_AJAX') && DOING_AJAX)
        ) {
            wp_redirect(home_url());
            exit;
        }
    }

    function my_function_admin_bar($content)
    {
        return (current_user_can('administrator')) ? $content : false;
    }

    public function load_scripts()
    {
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', array('jquery'), false, true );
        wp_register_script('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array(), false, true );
        wp_register_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js', array(), false, true );
        wp_register_script('caleandar', plugin_dir_url(__FILE__) . 'lib/caleandar-master/js/caleandar.min.js', array(), false, true );
        wp_register_script('script', plugin_dir_url(__FILE__) . 'js/main.js', array(), false, true );

        wp_register_style('plugin_style', plugin_dir_url(__FILE__) . 'css/plugin_style.css', false, null, 'all');
        wp_register_style('bootstrap_style', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', false, null, 'all');
        wp_register_style('fontAwesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, null, 'all');
        wp_register_style('caleandar_style', plugin_dir_url(__FILE__) . 'lib/caleandar-master/css/theme2.css', false, null, 'all');

        //Enable ajax calls
        wp_register_script('ajaxHandle', plugin_dir_url(__FILE__) . 'js/main.js', array(), false, true);
        wp_enqueue_script('ajaxHandle');
        wp_localize_script('ajaxHandle', 'ajax_object', array('ajaxurl' => plugin_dir_url(__FILE__) . 'admin-ajax.php'));

        wp_enqueue_script('jquery');
        wp_enqueue_script('bootstrap');
        wp_enqueue_script('popper');
        wp_enqueue_script('caleandar');
        wp_enqueue_script('script');

        wp_enqueue_style('plugin_style');
        wp_enqueue_style('bootstrap_style');
        wp_enqueue_style('fontAwesome');
        wp_enqueue_style('caleandar_style');
    }

    public function page_plugin_html()
    {

        $competitions = Roses_Config::getCompetitions();
        $competitions = json_decode(json_encode($competitions), true);
        
        $_SESSION['competition_edit_nonce'] = wp_create_nonce('competition_edit_nonce');

        $this->load_scripts();

        echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
        echo '<script type="text/javascript" src="' . plugin_dir_url(__FILE__) . 'js/main.js"></script>';
        
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="' . plugin_dir_url(__FILE__) . 'css/plugin_style.css' . '">';

        if (isset($_POST) && !empty($_POST)) {
            $this->handle_post();
        }

        require('Roses_Plugin_View.php');
    }


    // submit le form de création de compet
    public function handle_post()
    {
        $this->msgGlobal = [];
        $action = '';

        if (isset($_POST['action'])) {
            $action = $_POST['action'];
        } else {
            $action = null;
        }

        if (isset($_POST['id_competition'])) {
            $id_competition = $_POST['id_competition'];
        }

        switch ($action) {

            case 'add_nage':

                if (isset($_POST['nage_field']) && $_POST['nage_field'] != '' || isset($_POST['nage']) && $_POST['nage'] != '' || isset($_POST['nage_distance']) && $_POST['nage_distance'] != '' || isset($_POST['nage_label']) && $_POST['nage_label'] != '') {
                    $res = Roses_Config::addNage(esc_attr($_POST['nage_field']), esc_attr($_POST['nage']), esc_attr($_POST['nage_distance']), esc_attr($_POST['nage_label']));
                    $this->msgGlobal = $res;
                } else {
                    $this->msgGlobal = ['error' => 'Formulaire incomplet'];
                    break;
                }

                break;

            case 'competition_form_add':

                $results = [];

                // ajout compétition
                foreach (Roses_Config::COMPETITION_FIELDS as $field) {
                    if (isset($_POST[$field['field_name']]) && $_POST[$field['field_name']] != '' || $field['field_name'] == 'competition_commentaire') {
                        $results[$field['field_name']] = $_POST[$field['field_name']];
                    } else {
                        $this->msgGlobal = ['error' => 'Formulaire incomplet'];
                        break;
                    }
                }

                if (empty($this->msgGlobal)) {

                    $results['competition_nom'] = esc_attr($results['competition_nom']);
                    $results['competition_lieu'] = esc_attr($results['competition_lieu']);
                    $results['competition_lieuRdv'] = esc_attr($results['competition_lieuRdv']);
                    $results['competition_commentaire'] = esc_attr($results['competition_commentaire']);

                    $table = 'roses_competition';

                    if ($results['competition_nom'] != '' && $results['competition_lieu'] != '' && $results['competition_lieuRdv'] != '') {
                        $idCompetition = Roses_config::addCompetition($results, $table);
                    } else {
                        $this->msgGlobal = ['error' => 'Une erreur est survenue lors de la création de la compétition'];
                        break;
                    }

                    // ajout nageCompetition
                    if (isset($idCompetition) && !empty($idCompetition)) {
                        $table = 'roses_nage_competition';
                        foreach (Roses_Config::getNages() as $nage) {
                            if (isset($_POST[$nage->nage_field]) && $_POST[$nage->nage_field] != '') {
                                $result = Roses_config::addNageCompetition($idCompetition, $nage->id,  $table);
                                if ($result == false) {
                                    $this->msgGlobal = ['error' => 'Une erreur est survenue lors de la création de la nageCompetition'];
                                    break;
                                }
                            }
                        }
                        $this->msgGlobal = ['message' => 'Compétition ajoutée !'];
                        break;
                    } else {
                        $this->msgGlobal = ['error' => 'Une erreur est survenue lors de la création de la compétition'];
                        break;
                    }
                }
                break;

            case 'competition_form_edit':

                // ajout compétition
                foreach (Roses_Config::NEW_COMPETITION_FIELDS as $field) {
                    if (isset($_POST[$field['field_name']]) && $_POST[$field['field_name']] != '' || $field['field_name'] == 'new_competition_commentaire') {
                        $results[$field['field_name']] = $_POST[$field['field_name']];
                    } else {
                        $this->msgGlobal = ['error' => 'Une erreur est survenue lors de la saisie de l\'édition de la compétition'];
                        break;
                    }
                }

                $results['new_competition_nom'] = esc_attr($results['new_competition_nom']);
                $results['new_competition_lieu'] = esc_attr($results['new_competition_lieu']);
                $results['new_competition_lieuRdv'] = esc_attr($results['new_competition_lieuRdv']);

                if (isset($results['new_competition_commentaire'])) {
                    $results['new_competition_commentaire'] = esc_attr($results['new_competition_commentaire']);
                }

                $table = 'roses_competition';

                if ($results['new_competition_nom'] != '' && $results['new_competition_lieu'] != '' && $results['new_competition_lieuRdv'] != '') {
                    Roses_config::updateCompetition($results, $table, $id_competition);
                    $this->msgGlobal = ['message' => 'Compétition mise à jour !'];
                    break;
                } else {
                    $this->msgGlobal = ['error' => 'Une erreur est survenue lors de la modification de la compétition'];
                    break;
                }

                break;

            case 'competition_export':

                if (isset($_POST['competition_participations'])) {
                    $participations = $_POST['competition_participations'];
                }
                $participations = json_decode(html_entity_decode(stripslashes($participations)), true);

                $competition = Roses_Config::getCompetition($id_competition)[0];

                $dateBrut = explode('-', explode(' ', $competition->date)[0]);
                $date = $dateBrut[2] . '/' . $dateBrut[1] . '/' . $dateBrut[0];
                $createdAt = date("d-m-Y H:i:s");

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=participations_competition_' . $competition->id . '_' . $dateBrut[2] . '_' . $dateBrut[1] . '_' . $dateBrut[0] . '.csv');

                ob_end_clean();
                $file = fopen("php://output", 'w') or die("Can't open php://output");

                fputcsv($file, [$competition->nom, $date, '', 'exportation']);
                fputcsv($file, ['', '', '', $createdAt]);
                fputcsv($file, ['', '']);
                fputcsv($file, ['Participants', '']);
                fputcsv($file, ['', '']);
                foreach ($participations as $part) {
                    fputcsv($file, [$part['nom']]);
                    fputcsv($file, $part['nages']);
                    fputcsv($file, ['', '']);
                }
                fclose($file) or die("Can't close php://output");

                die();

                break;

            default:
                break;
        }
    }

    public function __errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        echo "<b>Une erreur est survenue :</b> [$errno] $errstr <br>";
        echo "<b>dans le fichier </b> $errfile ligne $errline";
        echo '<pre>';
        var_dump($errcontext);
        echo '</pre>';
        die();
    }

    public function clean($string)
    {
        $string = str_replace(' ', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }
}

new Roses_Plugin();
