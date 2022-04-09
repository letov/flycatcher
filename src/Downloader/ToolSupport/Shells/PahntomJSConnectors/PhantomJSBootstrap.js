var system = require('system');
var page = require('webpage').create();
var fs = require('fs');
var cache = require('./cache.js');

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
inputArgs['page-content-mime-filter'] = [];
inputArgs['page-content-wait'] = 10;
inputArgs['click-map-repeat'] = 1;
inputArgs['click-map'] = [];

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
        case 'clk':
            inputArgs['click-map'].push(value);
            break;
        case 'data':
            inputArgs[name] = value;
            inputArgs['data-parsed'] = parseQuery(value);
            break;
        case 'page-content-mime-filter':
            inputArgs['page-content-mime-filter'] = value.split(",");
            break;
        default:
            inputArgs[name] = value;
            break;
    }
}
inputArgs['save-content'] = inputArgs.hasOwnProperty('page-content-path') &&
    inputArgs.hasOwnProperty('disk-cache-path');

if (inputArgs['save-content']) {
    var dataCacheDir = false;
    for(var i = 0; i < 100; i++){
        var testDir = inputArgs['disk-cache-path'] + '/data' + i + '/';
        if (fs.isDirectory(testDir)) {
            dataCacheDir = testDir;
            break;
        }
    }
    if (false === dataCacheDir) {
        close('No data%Num% dir in --disk-cache-path');
    }
    cache.cachePath = dataCacheDir;
    page.onResourceReceived = function (response) {
        if (inputArgs['page-content-mime-filter'].length > 0 &&
            -1 !== inputArgs['page-content-mime-filter'].indexOf(response.contentType)) {
            console.log('Add to save list ' + response.contentType);
            cache.includeResource(response);
        }
    };
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

function successClose(filePath) {
    var content = page.content;
    fs.write(filePath, content, 'w');
    if (inputArgs['save-content']) {
        console.log('Wait page load ' + inputArgs['page-content-wait']);
        //page.onLoadFinished = function(status) {
        setTimeout(function () {
            for (index in cache.cachedResources) {
                var filePath = inputArgs['page-content-path'] + cache.cachedResources[index].cacheFileNoPathBeauty;
                var content = cache.cachedResources[index].getContents();
                if (false !== content &&
                    -1 === filePath.indexOf(';base64,')) {
                    console.log('Save page content ' + filePath);
                    fs.write(filePath, content, 'b');
                }
            }
            close('SUCCESS');
        }, inputArgs['page-content-wait'] * 1000);
        //};
    } else {
        close('SUCCESS');
    }
}

function close(msg) {
    if ('undefined' !== typeof msg) {
        console.log(msg);
    }
    page.close();
    phantom.exit(1)
}