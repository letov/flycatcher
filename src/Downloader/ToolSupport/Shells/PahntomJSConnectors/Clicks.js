exports.makeClicks = function makeClicks() {
    if (0 === inputArgs['click-map'].length) {
        return;
    }
    var j = 0;
    for(var i = 0; i < inputArgs['click-map-repeat']; i++) {
        for (var key in inputArgs['click-map']) {
            var selector = inputArgs['click-map'][key];
            var elem = page.evaluate(function(selector) {
                return document.querySelector(selector);
            });
            if (null !== elem) {
                setTimeout(function(selector, elem, page) {
                    console.log('Click on ' + selector);
                    page.sendEvent('click', elem.offsetLeft, elem.offsetTop, 'left');
                }, 1 * j, selector, elem, page);
                j++;
            }
        }
    }
}