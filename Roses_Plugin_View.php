
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> 

<div class="container_plugin">
    <h1>US Chambray Natation ° Plugin des Roses <img src="<?= plugin_dir_url(__FILE__) . 'lib/logo.jpg' ?>" class='logo_admin'> </h1>

    <h4><b>Panneau d'administration</b></h4>
    <hr>

    <!--
<script> // js submit form (values checked in js)
    var msg = <?php //json_encode($this->msgGlobal) 
                ?>

    if (msg.error) {
        display_error(msg.error)
    } else if (msg.message) {
        display_success(msg.message);
    }
</script>
-->

    <?php
    if (isset($this->msgGlobal['message'])) {
        echo '<div id="alert_perso" class="alert alert-success alert_perso alert-dismissible">' . $this->msgGlobal['message'] . '</div>';
    } elseif (isset($this->msgGlobal['error'])) {
        echo '<div id="alert_perso" class="alert alert-danger alert_perso alert-dismissible">' . $this->msgGlobal['error'] . '</div>';
    }
    ?>

    <div id="alert_placeholder" class="alert_placeholder"></div>

    <div class="">
        <h3>Ajouter une compétition</h3>
        <hr>
        <div class="row">

            <div class="col-5">
                <form method="post" name="competition_add" action="">
                    <div class="row field_container">
                        <div class="col-5">
                            <label class="label_perso" for="competition_nom">Nom</label>
                        </div>
                        <div class="col-7">
                            <input type="text" class="field_perso" name="competition_nom" id="competition_nom" placeholder="Nom" value="" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" for="competition_type">Type</label>
                        </div>
                        <div class="col-7">
                            <input type="text" class="field_perso" name="competition_type" id="competition_type" placeholder="Type" value="" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" for="competition_lieu">Lieu</label>
                        </div>
                        <div class="col-7">
                            <input type="text" class="field_perso" name="competition_lieu" id="competition_lieu" placeholder="Lieu" value="" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" for="competition_lieuRdv">Lieu de rendez-vous</label>
                        </div>
                        <div class="col-7">
                            <input type="text" class="field_perso" name="competition_lieuRdv" id="competition_lieuRdv" placeholder="Lieu de rendez-vous" value="" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" for="competition_date">Date</label>
                        </div>
                        <div class="col-7">
                            <input type="date" class="field_perso" name="competition_date" id="competition_date" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" for="competition_dateRdv">Date de rendez-vous</label>
                        </div>
                        <div class="col-7">
                            <input type="date" class="field_perso" name="competition_dateRdv" id="competition_dateRdv" />
                        </div>
                        <div class="col-5">
                            <label class="label_perso" style="color:darkred" for="competition_dateMax">Date max d'inscription</label>
                        </div>
                        <div class="col-7">
                            <input type="date" class="field_perso" name="competition_dateMax" id="competition_dateMax" />
                        </div>
                    </div>
            </div>
            <div class="col-7">
                <div class="row">
                    <div class="col-6">
                        <textarea style="height:100%;" name="competition_commentaire" id="competition_commentaire" placeholder="Commentaire ..." row="50" cols="30" value=""></textarea>
                    </div>
                    <div class="col-5 nage_list">
                        <?php foreach (Roses_Config::getNages() as $nage) : ?>
                            <div class="container">
                                <input type="checkbox" id="<?= $nage->nage_field ?>" name="<?= $nage->nage_field ?>">
                                <label for="<?= $nage->nage_field ?>"><?= $nage->label ?></label>
                                <a class="btn btn-danger btn-sm btn_delete_nage" onclick="deleteNage(<?= $nage->id ?>)">X</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-12">
                        <input id="add_nage_button" onclick="add_nages()" type="button" class="btn btn-sm btn-success btn_perso_sm" value="Ajouter" />
                    </div>
                </div><br><br>
                <div class="row field_container">
                    <input type="hidden" name="action" id="action" value="competition_form_add" /><br>
                    <input id="submit_button" type="submit" class="btn btn-primary btn_form" value="Enregistrer la compétition" />
                </div>
            </div>
            </form>
        </div><br>
        <hr>

        <h3>Compétitions</h3>

        <hr><br>
        <div class="row">
            <div class=" col-12 table-wrapper-scroll-y">
                <table class="table table-striped perso_container col-6 table-wrapper-scroll-y">
                    <thead>
                        <tr>
                            <th class="tdAdmin">
                                <center>Id</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Nom</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Type</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Lieu</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Date</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Lieu rdv</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Date rdv</center>
                            </th>
                            <th class="tdAdmin">
                                <center>Date max<br> d'inscription</center>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (Roses_Config::getCompetitions(true) as $competition) :

                            $date = strtotime($competition->date);
                            $date = date('d/m/y', $date);

                            $dateRdv = strtotime($competition->date_rdv);
                            $dateRdv = date('d/m/y', $dateRdv);

                            $dateMax = strtotime($competition->date_max);
                            $dateMax = date('d/m/y', $dateMax);
                        ?>
                            <tr>
                                <td class="tdAdmin"><?= $competition->id ?></td>
                                <td class="tdAdmin"><?= $competition->nom ?></td>
                                <td class="tdAdmin"><?= $competition->type ?></td>
                                <td class="tdAdmin"><?= $competition->lieu ?></td>
                                <td class="tdAdmin"><?= $date ?></td>
                                <td class="tdAdmin"><?= $competition->lieu_rdv ?></td>
                                <td class="tdAdmin"><?= $dateRdv ?></td>
                                <td class="tdAdmin"><?= $dateMax ?></td>
                                <td class="tdAdmin admin_td_btn_container">
                                    <div class="row">
                                        <a style="color: white;" class="btn btn-success btn-sm admin_td_btn" onclick="update_competition('show_competition', <?= $competition->id ?>)"><i class="fa fa-eye fa-2" aria-hidden="true"></i></a>
                                        <a style="color: white;" class="btn btn-info btn-sm admin_td_btn" onclick="update_competition('show_edit', <?= $competition->id ?>)"><i class="fa fa-pencil fa-2" aria-hidden="true"></i></a>
                                        <a style="color: white;" class="btn btn-danger btn-sm admin_td_btn" onclick="update_competition('delete', <?= $competition->id ?>)"><i class="fa fa-trash fa-2" aria-hidden="true"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT COMPETITION -->

    <div class="modal fade" id="edit-competition-modal">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_competition_title">Modification</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <form method="post" name="competition_edit" action="">
                                <div class="row field_container">
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_nom">Nom</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="field_perso" name="new_competition_nom" id="new_competition_nom" placeholder="Nom" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_type">Type</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="field_perso" name="new_competition_type" id="new_competition_type" placeholder="Type" value="" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_lieu">Lieu</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="field_perso" name="new_competition_lieu" id="new_competition_lieu" placeholder="Lieu" value="" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_lieuRdv">Lieu de rendez-vous</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="field_perso" name="new_competition_lieuRdv" id="new_competition_lieuRdv" placeholder="Lieu de rendez-vous" value="" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_date">Date</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="date" class="field_perso" name="new_competition_date" id="new_competition_date" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_dateRdv">Date de rendez-vous</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="date" class="field_perso" name="new_competition_dateRdv" id="new_competition_dateRdv" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" style="color:darkred" for="new_competition_dateMax">Date max d'inscription</label>
                                    </div>
                                    <div class="col-7">
                                        <input type="date" class="field_perso" name="new_competition_dateMax" id="new_competition_dateMax" />
                                    </div>
                                    <div class="col-5">
                                        <label class="label_perso" for="new_competition_commentaire">Commentaire</label>
                                    </div>
                                    <div class="col-7">
                                        <textarea name="new_competition_commentaire" id="new_competition_commentaire" placeholder="Rdv à 8h ..." row="2" cols="20" value=""></textarea>
                                    </div>
                                </div>

                                <input type="hidden" name="id_competition" id="id_competition" value="" />
                                <input type="hidden" name="action" id="action" value="competition_form_edit" /><br>
                                <div class="row field_container">
                                    <input id="submit_button" type="submit" class="btn btn-primary btn_form" value="Modifier" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- END MODAL EDIT COMPETITION -->

    <!-- MODAL SHOW COMPETITION -->

    <div class="modal fade" id="show-competition-modal">
        <div class="modal-dialog modal-xl modal-dialog-centered custom-modal-xl">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="modal-title" id="modal_show_competition_title"></h4>
                            <a id="modal_show_competition_type"></a>
                        </div>
                        <div class="col-3">
                            <b><a id="modal_show_competition_date"></a></b><br>
                            <a id="modal_show_competition_lieu"></a>
                        </div>
                        <div class="col-1">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                    </div>
                </div>

                <!-- Modal body -->
                <div class="modal-body">

                    <div id="alert_modal_placeholder" class="alert_placeholder"></div>

                    <div class=" col-12 table-wrapper-scroll-y custom-table-show-competition">
                        <table class="table table-striped col-12" id="table-show-competition">
                            <thead>
                                <tr>
                                    <th>
                                        <h4><b>Nageurs</b></h4>
                                    </th>
                                    <th>
                                        <h3><b>Courses</b></h3>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="modal_table_body">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="action" value="competition_export">
                        <input type="hidden" name="id_competition" id="id_competition_show" value="">
                        <input type="hidden" name="competition_participations" id="competition_participations" value="">
                        <button type="submit" class="btn btn-success" id="btn_export">Exporter</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- END MODAL SHOW COMPETITION -->

    <!-- MODAL ADD NAGE -->

    <div class="modal fade" id="add-nage-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_competition_title">Ajouter une nage</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">

                    <div id="alert_modal_add_nage" class="alert_placeholder"></div>
                    <a style="font-size: 11px; color:maroon"><b>En enregistrant, le contenu du formulaire de création de compétition sera vidé</b></a>
                    <form method="post" name="add_nage" action="">
                        <div class="row field_container">
                            <div class="col-3">
                                <p class="note"><br><b>Nage_field</b></p>
                            </div>
                            <div class="col-3">
                                <p class="note"><br><b>Nage</b></p>
                            </div>
                            <div class="col-3">
                                <p class="note"><br><b>Distance</b></p>
                            </div>
                            <div class="col-3">
                                <p class="note"><b>Label</b><br>
                                    <p style="font-size: 11px;">(Affiché à l'utilisateur)</p>
                                </p>
                            </div>
                        </div>
                        <div class="row field_container">
                            <div class="col-3">
                                <input type="text" class="field_nage" name="nage_field" id="nage_field" placeholder="50m_Dos" value="" />
                            </div>
                            <div class="col-3">
                                <input type="text" class="field_nage" name="nage" id="nage" placeholder="NL" value="" />
                            </div>
                            <div class="col-3">
                                <input type="text" class="field_nage" name="nage_distance" id="nage_distance" placeholder="200" value="" />
                            </div>
                            <div class="col-3">
                                <input type="text" class="field_nage" name="nage_label" id="nage_label" placeholder="200m Dos" value="" />
                            </div>
                        </div>
                        <div class="row field_container">
                            <div class="col-3">
                                <p class="note"><br><b>Format :</b><br><br>100m_4_nages<br>50m_NL<br>50m_Brasse<br>50m_Papillon<br>50m_Dos</p>
                            </div>
                            <div class="col-3">
                                <p class="note"><br><b>Format :</b><br><br>4 Nages<br>NL<br>Brasse<br>Papillon<br>Dos</p>
                            </div>
                            <div class="col-3">
                                <p class="note"><br><b>Format :</b><br><br>50<br>100<br>200<br>400<br>1500</p>
                            </div>
                            <div class="col-3">
                                <p class="note"><br><b>Format :</b><br><br>100m 4 nages<br>50m Nage Libre<br>50m Brasse<br>50m Papillon<br>50m Dos</p>
                            </div>
                        </div>
                        <input type="hidden" name="action" id="action" value="add_nage" /><br>
                        <div class="row field_container">
                            <input id="submit_button" type="submit" class="btn btn-primary btn_form" value="Ajouter" />
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

<!-- END MODAL ADD NAGE -->

<script>
 
    var competitions = <?= json_encode($competitions, JSON_PRETTY_PRINT); ?>;
    var nonce = '<?= $_SESSION['competition_get_nonce']; ?>';

    document.getElementById("nage_field").value = '';
    document.getElementById("nage").value = '';
    document.getElementById("nage_distance").value = '';
    document.getElementById("nage_label").value = '';
    
</script>