
function displayModal(idCompetition) {

    var competition = JSON.parse(competitions).find(x => x.id == idCompetition);
    var action = 'ajax_request_handler';
    var action_type = 'competition_get_nages';

    if (document.getElementById('modal_footer_subscription')) {
        document.getElementById('modal_footer_subscription').style.display = 'block';
        document.getElementById('modal_footer_subscription_alert').style.display = 'none';
    }

    if (document.getElementById('btn_sub_ok')) {
        document.getElementById('btn_sub_ok').disabled = '';
    }

    if (document.getElementById("modal_nages_list")) {
        document.getElementById("modal_nages_list").innerHTML = '';
    }

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: action,
            action_type: action_type,
            id_competition: competition.id,
            nonce: nonce
        },
        success: function(response) {

            if (response.participation !== 'not_swimmer') {

                if (response.participation === 0) {

                    var div = document.createElement('div');
                    div.classList = ['alert_perso_modal alert-warning alert'];
                    div.innerHTML = 'Vous ne serez pas présent';
                    document.getElementById("modal_nages_list").appendChild(div);

                    document.getElementById('id_competition').value = id_competition;
                    document.getElementById('btn_sub_ok').disabled = 'disabled';
                    document.getElementById('btn_sub_ko').innerHTML = 'Changer d\'avis';
                    document.getElementById('btn_sub_ko').onclick = function() {
                        submitForm(false);
                    };

                    if (response.nages && response.nages.length !== 0) {
                        response.nages.forEach(nage => {
                            if (nage) {
                                var p = document.createElement('p');
                                p.classList = ['modal_nage_item'];
                                p.innerHTML = '<span class="badge badge-pill badge-info">' + nage.label + '</span>';
                                document.getElementById("modal_nages_list").appendChild(p);
                            }
                        });
                    }

                } else {

                    if (response.participation == false) {

                        var dateMax = new Date(competition.date_max);
                        var month = (dateMax.getMonth() + 1 );

                        response.nages.forEach(nage_array => {
                            let id_nage_competition = nage_array.id_nage_competition;
                            let nage = nage_array.nages;

                            var span = document.createElement('span');
                            span.classList = ['badge badge-pill badge-info pill_checkbox'];
                            span.id = 'span_' + id_nage_competition;

                            // don't display checkboxes if subscription is over
                            if (dateMax.getTime() > new Date().getTime()) {
                                var checkbox = document.createElement('input');
                                checkbox.type = "checkbox";
                                checkbox.name = nage.nage_field + '_' + id_nage_competition;
                                checkbox.classList = ['modal_checkbox'];
                                checkbox.id = nage.nage_field + '_' + id_nage_competition;
                            }
                            var label = document.createElement('label')
                            label.classList = ['label_checkbox'];
                            label.htmlFor = nage.nage_field + '_' + id_nage_competition;
                            label.appendChild(document.createTextNode(nage.label));

                            document.getElementById("modal_nages_list").appendChild(span);
                            // don't display checkboxes if subscription is over
                            if (dateMax.getTime() > new Date().getTime()) {
                                document.getElementById("span_" + id_nage_competition).appendChild(checkbox);
                            }
                            document.getElementById("span_" + id_nage_competition).appendChild(label);
                        });
                        document.getElementById('id_competition').value = id_competition;

                    } else {
                        response.nages.forEach(nage => {
                            var p = document.createElement('p');
                            p.classList = ['modal_nage_item'];
                            p.innerHTML = '<span class="badge badge-pill badge-info">' + nage.label + '</span>';
                            document.getElementById("modal_nages_list").appendChild(p);
                        });
                        document.getElementById('id_competition').value = id_competition;
                        document.getElementById('btn_sub_ok').disabled = 'disabled';
                        document.getElementById('btn_sub_ok').innerHTML = 'Déjà inscrit';
                        document.getElementById('btn_sub_ko').innerHTML = 'Désinscription';
                    }
                }
            } else {

                if (response.nages && response.nages.length !== 0) {
                    response.nages.forEach(nage => {
                        if (nage) {
                            var p = document.createElement('p');
                            p.classList = ['modal_nage_item'];
                            p.innerHTML = '<span class="badge badge-pill badge-info">' + nage.label + '</span>';
                            document.getElementById("modal_nages_list").appendChild(p);
                        }
                    });
                }

            }

            // Put dates in form
            var dateMax = new Date(competition.date_max);
            var month = (dateMax.getMonth() + 1 );

            var date = new Date(competition.date);
            var month = (date.getMonth() + 1 );

            // check max subscription date
            if (dateMax.getTime() < new Date().getTime()) {
                if (document.getElementById('modal_footer_subscription')) {
                    document.getElementById('modal_footer_subscription').style.display = 'none';
                    document.getElementById('modal_footer_subscription_alert').style.display = 'block';
                }
            }

            var months = [];
            months[1] = 'Janvier';
            months[2] = 'Février';
            months[3] = 'Mars';
            months[4] = 'Avril';
            months[5] = 'Mai';
            months[5] = 'Mai';
            months[6] = 'Juin';
            months[7] = 'Juillet';
            months[8] = 'Août';
            months[9] = 'Septembre';
            months[10] = 'Octobre';
            months[11] = 'Novembre';
            months[12] = 'Décembre';

            if (document.getElementById('id_competition')) {
                document.getElementById('id_competition').value = idCompetition;
            }

            document.getElementById('modal_competition_date').innerHTML = date.getDate() + ' ' + months[month] + ' ' + date.getUTCFullYear();
            document.getElementById('modal_competition_title').innerHTML = competition.nom;
            document.getElementById('modal_competition_type').innerHTML = competition.type;
            document.getElementById('modal_competition_lieu').innerHTML = competition.lieu;

            $("#competition-modal").modal();

        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
            console.log(xhr.status);
            console.log(thrownError);
            console.log(xhr.statusText);
        }
    });
}

function submitForm(accepted) {

    dismiss_message('alert_placeholder');

    if (accepted) {
        var action_type = 'competition_sign_up';
    } else {
        var action_type = 'competition_unsub';
    }

    var id_competition = document.getElementById('id_competition').value;
    var action = 'ajax_request_handler';
    var id_nages_competition = [];

    switch (action_type) {

        case 'competition_sign_up':

            var selected = [];

            jQuery('#modal_nages_list input:checked').each(function() {
                let id_nage_competition = Number(jQuery(this).attr('name').split('_')[2]);
                selected.push(id_nage_competition);
            });

            if (selected.length == 0) {
                display_error('Merci de saisir les nages souhaitées', true, 'aler_placeholder');
                return;
            }

            id_nages_competition = selected;

            break;

        case 'competition_unsub':

            break;

        default:
            console.log('default submit form');
            return;
            break;
    }

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: action,
            action_type: action_type,
            id_competition: id_competition,
            nonce: nonce,
            id_nages_competition: id_nages_competition
        },
        success: function(response) {

            if (response.error) {
                display_error(response.error, true, 'alert_placeholder');
            } else {
                document.getElementById('btn_sub_ok').disabled = 'disabled';
                document.getElementById('btn_sub_ko').disabled = 'disabled';

                display_success(response.message, true, 'alert_placeholder');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
            console.log(xhr.status);
            console.log(thrownError);
            console.log(xhr.statusText);
        }
    });

}

function add_nages() {
    $("#add-nage-modal").modal();
}

function deleteNage(id_nage) {

    if (confirm('Supprimer cette compétition ?')) {

        var action = 'ajax_request_handler';
        var action_type = 'admin_delete_nage';

        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: ajaxurl,
            data: {
                action: action,
                action_type: action_type,
                id_nage: id_nage,
                nonce: nonce
            },
            success: function(response) {

                location.reload();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(ajaxOptions);
                console.log(xhr.status);
                console.log(thrownError);
                console.log(xhr.statusText);
            }
        });

    } else {
        return;
    }
}

function update_competition(action_competition, id_competition) {

    var action = 'ajax_request_handler';

    //document.getElementById('btn-container-loader').style.display = 'block';
    //document.getElementById('btn-container').style.display = 'none';

    if (document.getElementById("modal_table_body")) {
        document.getElementById("modal_table_body").innerHTML = '';
    }
    if (document.getElementById('alert_perso')) {
        setTimeout(function() {
            dismiss_message('alert_perso');
        }, 3000);
    }

    switch (action_competition) {

        case 'show_competition':

            var action_type = 'admin_get_competition_participations';

            document.getElementById('table-show-competition').style.display = 'inline-table';
            document.getElementById('btn_export').disabled = '';
            dismiss_message('alert_modal_placeholder');

            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: action,
                    action_type: action_type,
                    id_competition: id_competition,
                    nonce: nonce
                },
                success: function(response) {

                    if (response.length != 0) {

                        response.forEach(nageur => {
                            var row = '<tr><td><center><b>' + nageur.nom + '</b></center></td><td>';
                            nageur.nages.forEach(nage => {
                                // Couleurs par nage ?
                                row += '<span class="badge badge-pill badge-info">' + nage + '</span>&nbsp;';
                            });
                            row += '</td></tr>';
                            $("#table-show-competition tbody").append(row);
                        });

                        if (document.getElementById('competition_participations')) {
                            document.getElementById('competition_participations').value = JSON.stringify(response);
                        }
                    } else {

                        display_error('Aucune inscription pour le moment', false, 'alert_modal_placeholder');
                        document.getElementById('table-show-competition').style.display = 'none';
                        document.getElementById('btn_export').disabled = 'true';
                    }

                    var competition = JSON.parse(competitions).find(x => x.id == id_competition);

                    var date = new Date(competition.date);
                    if (date.getMonth() == 12) {
                        var month = 1;
                    } else {
                        var month = (date.getMonth() + 1);
                    }

                    var months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

                    if (document.getElementById('id_competition_show')) {
                        document.getElementById('id_competition_show').value = id_competition;
                    }

                    document.getElementById('modal_show_competition_date').innerHTML = date.getDate() + ' ' + months[month] + ' ' + date.getUTCFullYear();
                    document.getElementById('modal_show_competition_title').innerHTML = competition.nom;
                    document.getElementById('modal_show_competition_type').innerHTML = competition.type;
                    document.getElementById('modal_show_competition_lieu').innerHTML = competition.lieu;

                    $("#show-competition-modal").modal();

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(ajaxOptions);
                    console.log(xhr.status);
                    console.log(thrownError);
                    console.log(xhr.statusText);
                }
            });

            break;

        case 'show_edit':

            var action_type = 'admin_get_competition';

            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: action,
                    action_type: action_type,
                    id_competition: id_competition,
                    nonce: nonce
                },
                success: function(response) {

                    var competition = response[0];

                    competition.date = competition.date.substring(0, 10);
                    competition.date_rdv = competition.date_rdv.substring(0, 10);
                    competition.date_max = competition.date_max.substring(0, 10);

                    document.getElementById('new_competition_nom').value = competition.nom;
                    document.getElementById('new_competition_type').value = competition.type;
                    document.getElementById('new_competition_lieu').value = competition.lieu;
                    document.getElementById('new_competition_date').value = competition.date;
                    document.getElementById('new_competition_lieuRdv').value = competition.lieu_rdv;
                    document.getElementById('new_competition_dateRdv').value = competition.date_rdv;
                    document.getElementById('new_competition_dateMax').value = competition.date_max;
                    document.getElementById('new_competition_commentaire').value = competition.commentaire;
                    document.getElementById('id_competition').value = competition.id;

                    $("#edit-competition-modal").modal();

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(ajaxOptions);
                    console.log(xhr.status);
                    console.log(thrownError);
                    console.log(xhr.statusText);
                }
            });

            break;

        case 'delete':

            if (confirm('Supprimer cette compétition ?')) {

                var action_type = 'admin_delete_competition';

                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        action: action,
                        action_type: action_type,
                        id_competition: id_competition,
                        nonce: nonce
                    },
                    success: function(response) {

                        location.reload();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(ajaxOptions);
                        console.log(xhr.status);
                        console.log(thrownError);
                        console.log(xhr.statusText);
                    }
                });

            } else {
                return;
            }

            break;

        default:
            console.log('default_plugin_view');
            break;
    }

}

// Alert message
function display_error(message, dismiss = true, target = 'alert_placeholder') {
    document.getElementById(target).style.display = 'none';
    $('#' + target).fadeOut('slow', function() {
        $('#' + target).html('<div class="alert alert-danger"><center>' + message + '</center></div>')
    }).fadeIn("slow");

    if (dismiss) {
        setTimeout(function() {
            dismiss_message(target)
        }, 3000);
    }
}

function display_success(message, redirect = false, target) {
    document.getElementById(target).style.display = 'none';
    $('#' + target).fadeOut('slow', function() {
        $('#' + target).html('<div class="alert alert-success"><center>' + message + '</center></div>');
    }).fadeIn("slow");

    setTimeout(function() {
        dismiss_message(target);
        if (redirect) {
            location.reload();
        }
    }, 2000);
}

function dismiss_message(target) {
    $('#' + target).fadeOut('slow', function() {
        $('#' + target).html('');
    });
}

