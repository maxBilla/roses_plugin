<?php

class Roses_Config
{

    /************ Const *************/

    const COMPETITION_FIELDS = [
        ['field_name' => 'competition_nom'],
        ['field_name' => 'competition_type'],
        ['field_name' => 'competition_lieu'],
        ['field_name' => 'competition_date'],
        ['field_name' => 'competition_lieuRdv'],
        ['field_name' => 'competition_dateRdv'],
        ['field_name' => 'competition_dateMax'],
        ['field_name' => 'competition_commentaire']
    ];

    const NEW_COMPETITION_FIELDS = [
        ['field_name' => 'new_competition_nom'],
        ['field_name' => 'new_competition_type'],
        ['field_name' => 'new_competition_lieu'],
        ['field_name' => 'new_competition_date'],
        ['field_name' => 'new_competition_lieuRdv'],
        ['field_name' => 'new_competition_dateRdv'],
        ['field_name' => 'new_competition_dateMax'],
        ['field_name' => 'new_competition_commentaire']
    ];

    /************ End Const *************/




    /************ Add *************/

    static function addNage($field, $nage, $distance, $label)
    {

        global $wpdb;

        $alreadyIn = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_nage WHERE nage_field = '" . $field . "' ORDER BY nage DESC, distance ASC");

        if (empty($alreadyIn)) {
            try {
                $wpdb->insert(
                    $wpdb->prefix . 'roses_nage',
                    [
                        'nage_field' => esc_attr($field),
                        'nage' => esc_attr($nage),
                        'distance' => esc_attr($distance),
                        'label' => esc_attr($label)
                    ]
                );
            } catch (Exception $e) {
                return ['error' => 'Exception addNage : ' .  $e->getMessage()];
                exit();
            }
            return ['message' => 'Nage ajoutée avec succès !'];
        } else {
            return ['error' => 'Nage déjà existante ...'];
        }
    }

    static function addCompetition($data, $table)
    {
        global $wpdb;

        try {
            $wpdb->insert(
                $wpdb->prefix . $table,
                [
                    'nom' => esc_attr($data['competition_nom']),
                    'type' => esc_attr($data['competition_type']),
                    'lieu' => esc_attr($data['competition_lieu']),
                    'date' => date($data['competition_date']),
                    'lieu_rdv' => esc_attr($data['competition_lieuRdv']),
                    'date_rdv' => date($data['competition_dateRdv']),
                    'date_max' => date($data['competition_dateMax']),
                    'commentaire' => esc_attr($data['competition_commentaire']),
                    'created_at' => date("Y-m-d H:i:s", strtotime('+1 hour')),
                    'last_updated_at' => date("Y-m-d H:i:s", strtotime('+1 hour'))
                ]
            );
        } catch (Exception $e) {

            exit('Exception reçue : ' .  $e->getMessage() . "\n");
        }
        return $wpdb->insert_id;
    }

    static function addNageCompetition($idCompetition, $idNage, $table)
    {
        global $wpdb;

        try {
            $wpdb->insert(
                $wpdb->prefix . $table,
                [
                    'id_competition' => esc_attr($idCompetition),
                    'id_nage' => esc_attr($idNage)
                ]
            );
        } catch (Exception $e) {
            exit('Exception reçue : ' .  $e->getMessage() . "\n");
            return false;
        }
        return true;
    }

    static function addParticipation($user_id, $id_nage_competition, $id_competition)
    {

        global $wpdb;
        $table = 'roses_participation';

        try {
            $wpdb->insert(
                $wpdb->prefix . $table,
                [
                    'id_nage_competition' => $id_nage_competition,
                    'id_competition' => $id_competition,
                    'id_user' => $user_id,
                    'participation' => 1,
                    'created_at' => date("Y-m-d H:i:s", strtotime('+1 hour'))
                ]
            );
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
            exit('Exception reçue : ' .  $e->getMessage() . "\n");
            return false;
        }
        return true;
    }

    /************ End Add *************/



    /************ Update *************/

    static function updateCompetition($datas, $table, $id_competition)
    {
        global $wpdb;

        try {
            $wpdb->update(
                $wpdb->prefix . $table,
                [
                    'nom' => esc_attr($datas["new_competition_nom"]),
                    'type' => esc_attr($datas["new_competition_type"]),
                    'lieu' => esc_attr($datas["new_competition_lieu"]),
                    'date' => $datas["new_competition_date"],
                    'lieu_rdv' => esc_attr($datas["new_competition_lieuRdv"]),
                    'date_rdv' => $datas["new_competition_dateRdv"],
                    'date_max' => $datas["new_competition_dateMax"],
                    'commentaire' => esc_attr($datas["new_competition_commentaire"]),
                    'last_updated_at' => date("Y-m-d H:i:s", strtotime('+1 hour'))
                ],
                ['id' => $id_competition]
            );
        } catch (Exception $e) {
            exit('Exception reçue : ' .  $e->getMessage() . "\n");
            return false;
        }
        return true;
    }

    /************ End Update *************/



    /************ Delete *************/

    static function deleteParticipation($id_participation)
    {

        global $wpdb;
        $table = 'roses_participation';

        return $wpdb->delete($wpdb->prefix . $table, ['id' => $id_participation]);
    }

    static function deleteUnsubParticipation($id_competition)
    {

        global $wpdb;
        $table = 'roses_participation';
        $user_id = wp_get_current_user()->ID;

        return $wpdb->delete($wpdb->prefix . $table, ['id_competition' => $id_competition, 'id_user' => $user_id, 'participation' => 0]);
    }

    static function deleteCompetition($id_competition)
    {

        global $wpdb;

        return $wpdb->delete($wpdb->prefix . 'roses_competition', ['id' => $id_competition]);
    }

    static function deleteNage($id_nage)
    {

        global $wpdb;

        return $wpdb->delete($wpdb->prefix . 'roses_nage', ['id' => $id_nage]);
        //$wpdb->delete($wpdb->prefix . 'roses_nage_competition', ['id_nage' => $id_nage]);
    }

    /************ End Delete *************/



    /************ Set *************/

    static function setParticipationNone($id_competition)
    {

        global $wpdb;
        $table = 'roses_participation';
        $user_id = wp_get_current_user()->ID;

        return $wpdb->insert(
            $wpdb->prefix . $table,
            [
                'id_nage_competition' => 0,
                'id_competition' => $id_competition,
                'id_user' => $user_id,
                'participation' => 0,
                'created_at' => date("Y-m-d H:i:s", strtotime('+1 hour'))
            ]
        );
    }

    /************ End Set *************/



    /************ Get *************/

    static function getCompetitions($orderByDate = false)
    {
        global $wpdb;

        if ($orderByDate) {
            return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_competition ORDER BY {$wpdb->prefix}roses_competition.date ASC");
        } else {
            return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_competition");
        }
    }

    static function getCompetition($idCompetition)
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_competition WHERE id = " . $idCompetition);
    }

    static function getNages()
    {
        global $wpdb;

        $datas = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_nage ORDER BY nage DESC, distance ASC");

        if (isset($datas) && !empty($datas)) {
            return $datas;
        } else {
            return false;
        }
    }

    static function getNage($id_nage)
    {

        global $wpdb;

        return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}roses_nage WHERE id = " . $id_nage);
    }

    static function getNageCompetition($id)
    {
        global $wpdb;

        $data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}roses_nage_competition WHERE id = " . $id);

        if (isset($data) && !empty($data)) {
            return $data;
        } else {
            return false;
        }
    }

    static function getNagesCompetition($id_competition)
    {
        global $wpdb;

        $datas = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_nage_competition WHERE id_competition = " . $id_competition);

        if (isset($datas) && !empty($datas)) {
            return $datas;
        } else {
            return false;
        }
    }

    static function getNageurUnsubState($id_competition)
    {

        global $wpdb;
        $table = 'roses_participation';
        $user_id = wp_get_current_user()->ID;

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}" . $table . " WHERE id_user = " . $user_id . " AND id_competition = " . $id_competition . " AND participation = 0");
    }

    static function getNageurParticipation($id_user, $id_competition)
    {
        global $wpdb;

        $res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}roses_participation WHERE id_user = " . $id_user . " AND id_competition = " . $id_competition . " AND participation = 1");

        if (empty($res)) {
            return false;
        } else {
            return $res;
        }
    }

    static function isNageur()
    {
        $user_capabilities = wp_get_current_user()->roles;

        if (in_array('nageur', $user_capabilities) || in_array('administrator', $user_capabilities)) {
            return true;
        } else {
            return false;
        }
    }

    /************ End Get *************/

    static function clean($string)
    {
        $string = str_replace(' ', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    static function debug($var, $die = false)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        if ($die) {
            exit;
        }
    }
}
