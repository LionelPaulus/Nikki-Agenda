ChangeColor();
function ChangeColor(){
    $('.Menu-options-colors li div').click(function(){
        var selected = $(this);
        selected.css('border', '2px solid #000000');
        var color = $(this).data('color');
        if (color == "red") {
            $('.BackLighter').css('background-color', '#D0021B');
            $('.BackLighter').css('border', '2px solid #680101');
            $('.BackDarker').css('background-color', '#680101');
            $('.Border').css('border', '2px solid #680101');
            $('.FontLighter').css('color', '#D0021B');
            $('.FontDarker').css('color', '#680101');
        }
        if (color == "blue") {
            $('.BackLighter').css('background-color', '#4990E2');
            $('.BackLighter').css('border', '2px solid #345677');
            $('.BackDarker').css('background-color', '#345677');
            $('.Border').css('border', '2px solid #345677');
            $('.FontLighter').css('color', '#4990E2');
            $('.FontDarker').css('color', '#345677');
        }
        if (color == "green") {
            $('.BackLighter').css('background-color', '#64A41A');
            $('.BackLighter').css('border', '2px solid #417505');
            $('.BackDarker').css('background-color', '#417505');
            $('.Border').css('border', '2px solid #417505');
            $('.FontLighter').css('color', '#64A41A');
            $('.FontDarker').css('color', '#417505');
        }
        if (color == "yellow") {
            $('.BackLighter').css('background-color', '#F6A623');
            $('.BackLighter').css('border', '2px solid #735015');
            $('.BackDarker').css('background-color', '#735015');
            $('.Border').css('border', '2px solid #735015');
            $('.FontLighter').css('color', '#F6A623');
            $('.FontDarker').css('color', '#735015');
        }
        $('.Menu-options-colors li div').click(function(){
            selected.css('border', '1px solid #979797');
            $(this).css('border', '2px solid #000000');
        })
    })
}
