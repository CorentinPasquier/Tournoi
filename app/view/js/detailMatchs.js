/**
 * Created by corentin on 12/06/16.
 */

var idSimple;
var idDouble;

$(document).ready(function(){

    // Init number of fields
    idSimple = parseInt($('#simplediv').children().last().attr('id').substr(-2, 1)) || 0;
    idDouble = parseInt($('#doublediv').children().last().attr('id').substr(-2, 1)) || 0;

    // Button to add simple match
    $('#addSimple').on('click', function () {
        idSimple = idSimple+1;
        $('#simplediv').append('' +
            '<div style="margin-bottom: 4px;" class="row" align="center" id="simple[' + idSimple + ']">' +
                '<div class="col-lg-2">' +
                    '<input placeholder="Joueur 1" class="form-control form-control2 first" id="joueurS1[' + idSimple + ']" type="text" value="" name="joueurS[' + idSimple + '][joueur_1]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input placeholder="Joueur 2" class="form-control form-control2 second" id="joueurS2[' + idSimple + ']" type="text" value="" name="joueurS[' + idSimple + '][joueur_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control first" id="scoreS1_1[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_1_1]">' +
                    '<input class="form-control second" id="scoreS1_2[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_1_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control btn-xs first" id="scoreS2_1[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_2_1]">' +
                    '<input class="form-control btn-xs second" id="scoreS2_2[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_2_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control btn-xs first" id="scoreS3_1[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_3_1]">' +
                    '<input class="form-control btn-xs second" id="scoreS3_2[' + idSimple + ']" type="number" value="" name="scoreS[' + idSimple + '][match_3_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<button class="btn btn-default red" type="button" data-idS="' + idSimple + '">-</button>' +
                '</div>' +
            '</div>');
        $('#scoreS3_1\\[' + idSimple + '\\]').hide();
        $('#scoreS3_2\\[' + idSimple + '\\]').hide();
    });

    // Button to add double match
    $('#addDouble').on('click', function () {
        idDouble = idDouble +1;
        $('#doublediv').append('' +
            '<div style="margin-bottom: 4px;" class="row" align="center" id="double[' + idDouble + ']">' +
                '<div class="col-lg-2">' +
                    '<input placeholder="Joueur 1 - Equipe 1" class="form-control form-control2 first" id="joueurD1_1[' + idDouble + ']" type="text" value="" name="joueurD[' + idDouble + '][joueur_1_1]">' +
                    '<input placeholder="Joueur 2 - Equipe 1" class="form-control form-control2 first" id="joueurD2_1[' + idDouble + ']" type="text" value="" name="joueurD[' + idDouble + '][joueur_2_1]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input placeholder="Joueur 1 - Equipe 2" class="form-control form-control2 second" id="joueurD1_2[' + idDouble + ']" type="text" value="" name="joueurD[' + idDouble + '][joueur_1_2]">' +
                    '<input placeholder="Joueur 2 - Equipe 2" class="form-control form-control2 second" id="joueurD2_2[' + idDouble + ']" type="text" value="" name="joueurD[' + idDouble + '][joueur_2_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control first" id="scoreD1_1[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_1_1]">' +
                    '<input class="form-control second" id="scoreD1_2[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_1_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control btn-xs first" id="scoreD2_1[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_2_1]">' +
                    '<input class="form-control btn-xs second" id="scoreD2_2[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_2_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<input class="form-control btn-xs first" id="scoreD3_1[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_3_1]">' +
                    '<input class="form-control btn-xs second" id="scoreD3_2[' + idDouble + ']" type="number" value="" name="scoreD[' + idDouble + '][match_3_2]">' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<button class="btn btn-default red" type="button" data-idD="' + idDouble + '">-</button>' +
                '</div>' +
            '</div>');
        $('#scoreD3_1\\[' + idDouble + '\\]').hide();
        $('#scoreD3_2\\[' + idDouble + '\\]').hide();
    });

    // Delete simple
    $(document).on('click', 'button[data-idS]', function () {
        var id = $(this).data('ids');
        $('#simple\\[' + id + '\\]').remove();
        updateScore();
    });

    // Delete double
    $(document).on('click', 'button[data-idD]', function () {
        var id = $(this).data('idd');
        $('#double\\[' + id + '\\]').remove();
        updateScore();
    });

    // Third score for simple
    $(document).on('blur', 'input[id^="scoreS"]', function () {
        var id = $(this).attr('id').substr(-2, 1);
        var score11 = $('#scoreS1_1\\[' + id + '\\]');
        var score12 = $('#scoreS1_2\\[' + id + '\\]');
        var score21 = $('#scoreS2_1\\[' + id + '\\]');
        var score22 = $('#scoreS2_2\\[' + id + '\\]');
        var score31 = $('#scoreS3_1\\[' + id + '\\]');
        var score32 = $('#scoreS3_2\\[' + id + '\\]');
        if(score11.val() != "" && score12.val() != "" && score21.val() != "" && score22.val() != "") {
            if (((parseInt(score11.val()) > parseInt(score12.val())) && (parseInt(score21.val()) < parseInt(score22.val()))) || ((parseInt(score11.val()) < parseInt(score12.val())) && (parseInt(score21.val()) > parseInt(score22.val())))) {
                score31.show();
                score32.show();
            }
            else {
                score31.hide();
                score32.hide();
            }
        }
        updateScore();
    });

    // Thirs score for double
    $(document).on('blur', 'input[id^="scoreD"]', function () {
        var id = $(this).attr('id').substr(-2, 1);
        var score11 = $('#scoreD1_1\\[' + id + '\\]');
        var score12 = $('#scoreD1_2\\[' + id + '\\]');
        var score21 = $('#scoreD2_1\\[' + id + '\\]');
        var score22 = $('#scoreD2_2\\[' + id + '\\]');
        var score31 = $('#scoreD3_1\\[' + id + '\\]');
        var score32 = $('#scoreD3_2\\[' + id + '\\]');
        if(score11.val() != "" && score12.val() != "" && score21.val() != "" && score22.val() != "") {
            if (((parseInt(score11.val()) > parseInt(score12.val())) && (parseInt(score21.val()) < parseInt(score22.val()))) || ((parseInt(score11.val()) < parseInt(score12.val())) && (parseInt(score21.val()) > parseInt(score22.val())))) {
                score31.show();
                score32.show();
            }
            else {
                score31.hide();
                score32.hide();
            }
        }
        updateScore();
    });

    // Hide third score for simple already set
    for(var i = 1; i <= idSimple; i++) {
        $(document).ready($('#scoreS1_1\\[' + i + '\\]').blur());
    }

    // Hide third score for double already set
    for(i = 1; i <= idDouble; i++) {
        $(document).ready($('#scoreD1_1\\[' + i + '\\]').blur());
    }

    while(idSimple < 4){
        $('#addSimple').click();
    }

    while(idDouble < 5){
        $('#addDouble').click();
    }

    // Update score at the begining in function of scores already set
    $(document).ready(updateScore());

    // Update top score function
    function updateScore() {
        var scoreE1 = $('#scoreE1');
        var scoreE2 = $('#scoreE2');
        var scores = $('div[id^=simple\\[]');
        scores = scores.add('div[id^=double\\[]');
        var score = 0;
        var score1 = 0;
        var score2 = 0;
        scores.each(function () {
            var children = $(this).children().first().next().next().first().children().first();
            if(children.val() != "" && children.next().val() != "") {
                if (parseInt(children.val()) > parseInt(children.next().val())) {
                    score = 1;
                }
                else {
                    score = -1;
                }
                children = $(this).children().first().next().next().first().next().children().first();
                if(children.val() != "" && children.next().val() != "") {
                    if (parseInt(children.val()) > parseInt(children.next().val())) {
                        score += 1;
                    }
                    else {
                        score += -1;
                    }
                    if (score == 0) {
                        children = $(this).children().first().next().next().first().next().next().children().first();
                        if(children.val() != "" && children.next().val() != "") {
                            if (parseInt(children.val()) > parseInt(children.next().val())) {
                                score += 1;
                            }
                            else {
                                score += -1;
                            }
                        }
                    }
                    if (score > 0) {
                        score1++;
                    }
                    else if (score < 0) {
                        score2++;
                    }
                }
            }
        });
        scoreE1.html(score1);
        scoreE2.html(score2);
    }

    // $('#jour').on('click', function () {
    //     return false;
    // })
});