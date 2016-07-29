/**
 * Created by corentin on 22/07/16.
 */

var options = {
    placement: function (context, source) {
        var position = $(source).offset();
        var body = $('body');
        var middle = body.width()/2;

        if (middle > ($(source).width() + position.left) && $(source).width() <= position.left) {
            return "left";
        }

        if (middle <= ($(source).width() + position.left) && $(source).width() + position.left + 200 < body.width()) {
            return "right";
        }

        if ($(source).height() <= position.top){
            return "top";
        }

        return "bottom";
    }
    , trigger: "click"
    , html: true
};

$(function () {
    $('[data-toggle="popover"]').popover(options);
});