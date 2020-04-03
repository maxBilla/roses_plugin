<?php

add_action('wp_ajax_ajax_request_handler', 'ajax_request_handler');
add_action('wp_ajax_nopriv_ajax_request_handler', 'ajax_request_handler');

include plugin_dir_path(__FILE__) . 'Roses_Config.php';

function ajax_request_handler()
{
    // vérification des nonces 
    // competition_edit_nonce
    // competition_get_nonce

    $user_id = get_current_user_id();

    if (isset($_POST['id_competition'])) {
        $id_competition = $_POST['id_competition'];
    }
    if (isset($_POST['id_nage'])) {
        $id_nage = $_POST['id_nage'];
    }

    switch ($_POST['action_type']) {

        case 'competition_sign_up':

            if (isset($_POST['id_nages_competition'])) {
                $id_nages_competition = $_POST['id_nages_competition'];
            } else {
                break;
            }

            foreach ($id_nages_competition as $id_nage_competition) {
                Roses_Config::addParticipation($user_id, $id_nage_competition, $id_competition);
            }
            echo json_encode(['message' => 'Inscription effectuée avec succès !']);
            exit;

            break;

        case 'competition_unsub':

            $is_unsub = Roses_Config::getNageurUnsubState($id_competition);

            // If is not unsub from competition
            if (empty($is_unsub)) {

                $participations = Roses_Config::getNageurParticipation($user_id, $id_competition);

                if ($participations != false) {
                    foreach ($participations as $part) {
                        Roses_Config::deleteParticipation($part->id);
                    }
                    echo json_encode(['message' => 'Participation mise à jour !']);
                    exit;
                } else { // no participation, set participation to 0

                    $res = Roses_Config::setParticipationNone($id_competition);

                    if ($res == 'false' || $res == false) {
                        echo json_encode(['error' => 'Une erreur est survenue lors de l\'enregistrement ...']);
                        exit;
                    }
                    echo json_encode(['message' => 'Enregistrement effectué avec succès !']);
                    exit;
                    break;
                }
            } else {
                Roses_Config::deleteUnsubParticipation($id_competition);
                echo json_encode(['message' => 'Modification de l\'inscription ...']);
                exit;
            }

            break;

        case 'competition_get_nages':

            // check if user has decided not to come to competition
            $is_unsub = Roses_Config::getNageurUnsubState($id_competition);

            if (in_array('nageur', wp_get_current_user()->roles) && empty($is_unsub)) {

                $nages = [];
                $participations = Roses_Config::getNageurParticipation($user_id, $id_competition);

                if ($participations != false) { // no participations

                    foreach ($participations as $part) {
                        $nage_competition = Roses_Config::getNageCompetition($part->id_nage_competition);
                        $nage = Roses_Config::getNage((int) $nage_competition->id_nage);
                        array_push($nages, $nage);
                    }

                    echo json_encode(
                        [
                            'participation' => $participations,
                            'nages' => $nages,
                            'id_competition' => $id_competition
                        ]
                    );
                    exit;
                    break;
                } else { // get participations

                    $nages_competition = Roses_Config::getNagesCompetition($id_competition);

                    foreach ($nages_competition as $nage) {
                        $data = [
                            'nages' => Roses_Config::getNage($nage->id_nage),
                            'id_nage_competition' => $nage->id
                        ];
                        array_push($nages, $data);
                    }

                    echo json_encode(
                        [
                            'participation' => $participations,
                            'nages' => $nages,
                            'id_competition' => $id_competition
                        ]
                    );
                    exit;
                    break;
                }
            } else if (in_array('nageur', wp_get_current_user()->roles) && !empty($is_unsub)) {

                $nages = [];
                $nages_competition = Roses_Config::getNagesCompetition($id_competition);

                foreach ($nages_competition as $nage) {
                    $data =  Roses_Config::getNage($nage->id_nage);
                    array_push($nages, $data);
                }

                echo json_encode(
                    [
                        'participation' => 0,
                        'nages' => $nages,
                        'id_competition' => $id_competition
                    ]
                );
                exit;
                break;
            } else { // if user is not a swimmer, display nage_competition only

                $nages = [];
                $nages_competition = Roses_Config::getNagesCompetition($id_competition);

                foreach ($nages_competition as $nage) {
                    $data =  Roses_Config::getNage($nage->id_nage);
                    array_push($nages, $data);
                }

                echo json_encode(
                    [
                        'participation' => 'not_swimmer',
                        'nages' => $nages,
                        'id_competition' => $id_competition
                    ]
                );
                exit;
                break;
            }

            break;

        case 'admin_delete_competition':

            Roses_Config::deleteCompetition($id_competition);
            echo json_encode(true);
            exit;
            break;

        case 'admin_delete_nage':

            Roses_Config::deleteNage($id_nage);
            echo json_encode(true);
            exit;
            break;

        case 'admin_get_competition':

            $competition = Roses_Config::getCompetition($id_competition);
            echo json_encode($competition);
            exit;
            break;

        case 'admin_get_competition_participations':

            $swimmers = get_users(['role' => 'nageur']);
            $participations = [];

            foreach ($swimmers as $swimmer) {

                $swimmer_participations = Roses_Config::getNageurParticipation($swimmer->ID, $id_competition);

                if ($swimmer_participations != false) {
                    $data = [];
                    foreach ($swimmer_participations as $part) {
                        $nage_competition =  Roses_Config::getNageCompetition($part->id_nage_competition);
                        $nage = Roses_Config::getNage($nage_competition->id_nage);
                        array_push($data, $nage->label);
                    }
                    $data_to_send = [
                        'nom' => $swimmer->data->display_name,
                        'nages' => $data
                    ];
                    array_push($participations, $data_to_send);
                }
            }

            echo json_encode($participations);
            exit;
            break;

        default:

            echo json_encode('default');
            exit;
            break;
    }
}
