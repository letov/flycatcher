phantom.injectJs('Bootstrap.js');

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
        if (inputArgs['save-content-mime-filter'].length > 0 &&
            -1 !== inputArgs['save-content-mime-filter'].indexOf(response.contentType)) {
            console.log('Add to save list ' + response.contentType);
            cache.includeResource(response);
        }
    };
}

page.open(inputArgs['url'], inputArgs['method'], inputArgs['data'], function(status) {
    if ("success" === status) {
        form.fillForm();
        snapshot.makeSnapshot();
        clicks.makeClicks();
        captcha.solveCaptcha(inputArgs);
        success();
    } else {
        close('status ' + status);
    }
});

function success() {
    var content = page.content;
    fs.write(inputArgs['file-path'], content, 'w');
    if (inputArgs['save-content']) {
        console.log('Wait page load ' + inputArgs['save-content-wait']);
        setTimeout(function () {
            for (index in cache.cachedResources) {
                var filePath = inputArgs['save-content-path'] + cache.cachedResources[index].cacheFileNoPathBeauty;
                var content = cache.cachedResources[index].getContents();
                if (false !== content &&
                    -1 === filePath.indexOf(';base64,')) {
                    console.log('Save page content ' + filePath);
                    fs.write(filePath, content, 'b');
                }
            }
            close('SUCCESS');
        }, inputArgs['save-content-wait'] * 1000);
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