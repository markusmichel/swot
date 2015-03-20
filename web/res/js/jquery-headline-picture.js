$.fn.setHeadlinePicture = function(imagePath, isThing) {
    if (isThing) var defaultImagePath = "http://localhost/swot/web/res/gfx/thing-inverted.jpg";
    else var defaultImagePath = "http://localhost/swot/web/res/gfx/person-inverted.jpg";
    var me = this;

    var image = new Image();
    image.src = imagePath;

    $(image)
        .load(function() {
            me.css('background-image', 'url(' + imagePath + ')');
        })
        .error(function() {
            me.css('background-image', 'url(' + defaultImagePath + ')');
        });
};