exports.makeSnapshot = function makeSnapshot() {
    if (!inputArgs.hasOwnProperty('snapshot-selector') ||
        !inputArgs.hasOwnProperty('snapshot-path')) {
        return;
    }
    if (!exports.changeClipRect(inputArgs['snapshot-selector'])) {
        close('snapshot-selector error');
    }
    page.render(inputArgs['snapshot-path']);
    console.log('make snapshot ' + inputArgs['snapshot-path']);
}

exports.changeClipRect = function changeClipRect(selector) {
    var clipRect = page.evaluate(function(selector) {
        var element = document.querySelector(selector);
        return null === element ? false : element.getBoundingClientRect();
    }, selector);
    if (!clipRect) {
        return false;
    }
    page.clipRect = {
        top:    clipRect.top,
        left:   clipRect.left,
        width:  clipRect.width,
        height: clipRect.height
    };
    return true;
}