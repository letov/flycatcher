var system = require('system');
var page = require('webpage').create();

page.onConsoleMessage = function(msg) {
    system.stderr.writeLine( msg );
};

var inputArgs = {};
inputArgs.filePath = '';
inputArgs.url = '';
inputArgs.timeout = 30;
inputArgs.method = 'GET';
inputArgs.data = '';
inputArgs.captchaApiKey = '';
inputArgs.captchaSign = '';
inputArgs.captchaImageSelector = '';
inputArgs.captchaInputSelector = '';
inputArgs.captchaFormSelector = '';
inputArgs.viewportWidth = 800;
inputArgs.viewportHeight = 480;
inputArgs.customHeaders = {};

for (var i in system.args) {
    if (0 === i) {
        continue;
    }
    var arg = system.args[i];
    var name = arg.slice(2,arg.indexOf('='));
    var value = arg.slice(arg.indexOf('=') + 1, arg.length);
    switch (name) {
        case 'file-path':
            inputArgs.filePath = value;
            break;
        case 'url':
            inputArgs.url = value;
            break;
        case 'connect-timeout':
            inputArgs.timeout = value;
            break;
        case 'header':
            var header = value.split(': ');
            inputArgs.customHeaders[header[0]] = header[1];
            break;
        case 'method':
            inputArgs.method = value;
            break;
        case 'data':
            inputArgs.data = value;
            break;
        case 'captcha-sign':
            inputArgs.captchaSign = value;
            break;
        case 'captcha-api-key':
            inputArgs.captchaApiKey = value;
            break;
        case 'captcha-image-selector':
            inputArgs.captchaImageSelector = value;
            break;
        case 'captcha-input-selector':
            inputArgs.captchaInputSelector = value;
            break;
        case 'captcha-form-selector':
            inputArgs.captchaFormSelector = value;
            break;
        case 'viewport-width"':
            inputArgs.viewportWidth = value;
            break;
        case 'viewport-height':
            inputArgs.viewportHeight = value;
            break;
    }
}

if (0 === inputArgs.filePath.length || 0 === inputArgs.url.length) {
    close('filePath or url empty');
}

setTimeout(function () {
    close('phantomjs global timeout');
}, 1000 * inputArgs.timeout);
page.customHeaders = inputArgs.customHeaders;
page.viewportSize = {
    width: inputArgs.viewportWidth,
    height: inputArgs.viewportHeight
};

page.open(inputArgs.url, inputArgs.method, inputArgs.data, function(status) {
    if ("success" === status) {
        var content = page.content;
        if ('' !== inputArgs.captchaSign && content.indexOf(inputArgs.captchaSign) > 0) {
            if (0 === inputArgs.captchaApiKey.length ||
                0 === inputArgs.captchaImageSelector.length ||
                0 === inputArgs.captchaInputSelector.length ||
                0 === inputArgs.captchaFormSelector.length) {
                close('empty one of captcha arg');
            }
            var captchaImageSelector = inputArgs.captchaImageSelector;
            var clipRect = page.evaluate(function(captchaImageSelector) {
                return document.querySelector(captchaImageSelector).getBoundingClientRect();
            }, captchaImageSelector);
            page.clipRect = {
                top:    clipRect.top,
                left:   clipRect.left,
                width:  clipRect.width,
                height: clipRect.height
            };
            var imgBase64 = page.renderBase64('jpg');
            page.clipRect = {
                top:    0,
                left:   0,
                width:  page.viewportSize.width,
                height: page.viewportSize.height
            };
            var captchaApiKey = inputArgs.captchaApiKey;
            var taskId = page.evaluate(function(captchaApiKey, getTaskId, imgBase64) {
                return getTaskId(captchaApiKey, imgBase64)
            }, captchaApiKey, getTaskId, imgBase64);
            if (false === taskId) {
                close('response empty taskId');
            }
            for (var tryNumber = 0; i < 20; i++) {
                setTimeout(function () {
                    trySolution();
                }, 5000 * tryNumber);
            }
            function trySolution() {
                var solution = page.evaluate(function(captchaApiKey, getSolution, taskId) {
                    return getSolution(captchaApiKey, taskId);
                }, captchaApiKey, getSolution, taskId);
                if (false !== solution) {
                    var captchaInputSelector = inputArgs.captchaInputSelector;
                    var captchaFormSelector = inputArgs.captchaFormSelector;
                    page.evaluate(function(solution, captchaInputSelector, captchaFormSelector) {
                        $(captchaInputSelector).val(solution);
                        $(captchaFormSelector).submit();
                    }, solution, captchaInputSelector, captchaFormSelector);
                    setTimeout(function () {
                        successClose(inputArgs.filePath);
                    }, 10000);
                }
            }
        } else {
            successClose(inputArgs.filePath);
        }
    } else {
        close('status ' + status);
    }
});

function getTaskId(captchaApiKey, imgBase64) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'https://api.anti-captcha.com/createTask', false);
    xhr.setRequestHeader("Content-Type", "application/json");
    var payload = {
        'clientKey' : captchaApiKey,
        'task' : {
            'type': "ImageToTextTask",
            'body': imgBase64,
            "phrase": false,
            "case": false,
            "numeric": 0,
            "math": false,
            "minLength": 0,
            "maxLength": 0
        }
    };
    var data = JSON.stringify(payload);
    xhr.send(data);
    var responseText = xhr.responseText;
    if (!responseText) {
        return false;
    }
    var json = JSON.parse(responseText);
    if (json.errorId !== 0) {
        return false;
    }
    var taskId = json.taskId;
    return taskId;
}

function getSolution(captchaApiKey, taskId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'https://api.anti-captcha.com/getTaskResult', false);
    xhr.setRequestHeader("Content-Type", "application/json");
    var payload = {
        'clientKey' : captchaApiKey,
        'taskId' : taskId
    };
    var data = JSON.stringify(payload);
    xhr.send(data);
    var responseText = xhr.responseText;
    if (!responseText) {
        return false;
    }
    var json = JSON.parse(responseText);
    if (json.errorId !== 0) {
        return false;
    }
    if (json.status !== 'ready') {
        return false

    }
    var solution = json.solution.text;
    return solution;
}

function successClose(filePath) {
    var content = page.content;
    var fs = require('fs');
    fs.write(filePath, content, 'w');
    console.log('SUCCESS');
    close('');
}

function close(msg) {
    console.log(msg);
    page.close();
    phantom.exit(1);
}