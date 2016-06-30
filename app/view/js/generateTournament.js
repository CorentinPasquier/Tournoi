/**
 * Created by corentin on 03/06/16.
 */

$(document).ready(function () {

    /** Removes or Adds elements according to totalNumber value **/
    $('#totalNumber').on('change', function () {
        var $_oldN = $('select[name^="equipe["]').length;
        var $_newN= $(this).val();
        var $_html = $('select[name="equipe[1]"]').children;
        if($_oldN < $_newN) {
            for (var i = $_oldN + 1; i <= $_newN; i++) {
                $('.panel-body')
                    .append('<div class="row">' +
                        '<label for=\'equipe[' + i + ']\'></label>' +
                        '<select id=\'equipe[' + i + ']\' name=\'equipe[' + i + ']\'>')
                    .append($_html)
                    .append('</div>');
            }
        }
        else{
            for (i = $_oldN ; i > $_newN ; i--){
                $('select[name="equipe[' + i + ']"]').parent().remove();
            }
        }
    });

    $('#endTournament').on('click', function () {
        $('#myModal').modal('show');
    });
    
});