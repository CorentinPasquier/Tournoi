/**
 * Created by corentin on 16/07/16.
 */


$(document).ready(function () {

    $('#newJoueur').on('keyup', function () {
       var s = $(this).val();
        if(s.substr(s.length - 1) == ';') {
            var val = s.substr(0, s.length-1);
            var i = $(this).prevAll().length;
            $(this).before('<input class="form-control" type="text" name="joueur[' + i + ']" title="newJoueur" value="'+ val +'">');
            $(this).val('');
        }
    });

});