<div id="caleandar"></div>

<!-- The Modal -->
<div class="modal fade" id="competition-modal">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="modal_competition_title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <div class="row">
                    <div class="col-4">
                        <div class="row justify-content-center">
                            <b>
                                <p id="modal_competition_type"></p>
                            </b>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="row justify-content-center">
                            <p id="modal_competition_date"></p>
                            <span>&nbsp;-&nbsp;</span>
                            <p id="modal_competition_lieu"></p>
                        </div>
                    </div>
                </div>
                <div id="alert_placeholder" class="alert_placeholder"></div>
                <div class="row modal_nages_list" id="modal_nages_list"></div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="alert alert-danger" id="modal_footer_subscription_alert">
                    <center>Inscriptions terminées</center>
                </div>
                <div class="row" id="modal_footer_subscription">
                    <form>
                        <center>
                            <input type="hidden" name="id_competition" id="id_competition" value="" />
                            <?php if (in_array('nageur', wp_get_current_user()->roles)) : ?>
                                <button type="button" class="btn btn-success" id="btn_sub_ok" onclick="submitForm(true)">S'inscrire</button>
                                <button type="button" class="btn btn-danger" id="btn_sub_ko" onclick="submitForm(false)">Je ne suis pas là</button>
                            <?php endif; ?>
                        </center>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var competitions = <?= json_encode($competitions, JSON_PRETTY_PRINT); ?>;

    competitionsCalendar = [];

    for (var i = 0; i < competitions.length; i++) {

        var date = new Date(competitions[i].date);
        var month = (date.getMonth() /*+ 1*/ );

        date = new Date(date.getUTCFullYear(), date.getMonth(), date.getDate());

        //var date = new Date(competitions[i].date);
        var nom = competitions[i].nom;
        var id = competitions[i].id;
        content = '<h5 class="card-title title_competition">' + nom + '</h5>' + '<button class="btn btn-sm btn-block btn-info" onclick="displayModal(' + id + ')">Voir</button>';

        var event = {
            'Date': date,
            'Title': content,
            'Link': function() {}
        }

        competitionsCalendar.push(event);
    }

    var settings = {
        /*Color: '#999',*/
        LinkColor: '#333',
        /*EventTargetWholeDay: true,*/
        /*ModelChange: new Date(date.getUTCFullYear(), date.getMonth(), date.getUTCDate(), 1),*/
    };

    var element = document.getElementById('caleandar');
    caleandar(element, competitionsCalendar, settings);

    var nonce = '<?= $_SESSION['competition_get_nonce']; ?>';
</script>