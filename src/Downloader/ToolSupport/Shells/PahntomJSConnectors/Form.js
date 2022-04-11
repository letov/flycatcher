exports.fillForm =function fillForm() {
    if ('GET' !== inputArgs['method'] || 0 === Object.keys(inputArgs['data-parsed']).length) {
        return;
    }
    console.log('fill form');
    page.evaluate(function(inputArgs){
        var keys = Object.keys(inputArgs['data-parsed']);
        for (var key in keys) {
            var name = keys[key];
            var elem = document.querySelector('[name=' + name + ']');
            if (null !== elem) {
                elem.value = inputArgs['data-parsed'][name];
            }
        }
    }, inputArgs);
}
