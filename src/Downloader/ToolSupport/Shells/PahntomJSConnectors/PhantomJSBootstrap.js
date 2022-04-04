var system = require('system');
var page = require('webpage').create();

page.onConsoleMessage = function(msg) {
    system.stderr.writeLine( msg );
};

var inputArgs = {};
inputArgs['captcha-incorrect-report'] = 0;
inputArgs['connect-timeout'] = 30;
inputArgs['method'] = 'GET';
inputArgs['data'] = '';
inputArgs['data-parsed'] = {};
inputArgs['viewport-width'] = 800;
inputArgs['viewport-height'] = 480;
inputArgs['headers'] = {};

for (var i in system.args) {
    if (0 === i) {
        continue;
    }
    var arg = system.args[i];
    var argPos = arg.indexOf('=');
    var name = arg.slice(2, argPos).trim();
    var value = arg.slice(argPos + 1, arg.length).trim();
    switch (name) {
        case 'header':
            var headerPos = value.indexOf(':');
            var headerName = value.slice(0, headerPos).trim();
            var headerValue = value.slice(headerPos, value.length).trim();
            inputArgs['headers'][headerName] = headerValue;
            break;
        case 'data':
            inputArgs[name] = value;
            inputArgs['data-parsed'] = parseQuery(value);
            break;
        default:
            inputArgs[name] = value;
            break;
    }
}

if (!inputArgs.hasOwnProperty('file-path') || !inputArgs.hasOwnProperty('url')) {
    close('filePath or url empty');
}

setTimeout(function () {
    close('phantomjs global timeout');
}, 1000 * inputArgs['connect-timeout']);

page.customHeaders = inputArgs['headers'];
page.viewportSize = {
    width: inputArgs['viewport-width'],
    height: inputArgs['viewport-height']
};

function parseQuery(queryString) {
    var query = {};
    var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
    }
    return query;
}