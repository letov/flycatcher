var system = require('system');
var page = require('webpage').create();

setTimeout(function () {
    phantom.exit();
}, 60000);

page.onConsoleMessage = function(msg) {
    system.stderr.writeLine(msg);
};

page.settings.resourceTimeout = system.args[3]
page.settings.userAgent = system.args[2];
var referer = system.args[4];
var antiCaptchaApiKey = system.args[6];
var captchaSign = system.args[7];
var filePath = system.args[1];
var url = system.args[2];

page.customHeaders = {
    "Referer" : referer
};

page.open(url, function(status) {
    if ("success" === status) {
        var content = page.content;
        if (content.indexOf(captchaSign) > 0) {
            var img = page.evaluate(function() {
                return document.getElementsByClassName("captcha-image")[0].querySelector("img").getAttribute("src").split(',')[1]
            });
            if (!img) {
                failClose();
            }
            var taskId = page.evaluate(function(antiCaptchaApiKey, getTaskId, img) {
                return getTaskId(antiCaptchaApiKey, img)
            }, antiCaptchaApiKey, getTaskId, img);
            if (!taskId) {
                failClose();
            }
            var cnt = 0
            fSolution = function() {
                var solution = page.evaluate(function(antiCaptchaApiKey, getSolution, taskId) {
                    return getSolution(antiCaptchaApiKey, taskId);
                }, antiCaptchaApiKey, getSolution, taskId);
                if (!solution) {
                    cnt++
                    if (cnt > 10) {
                        failClose();
                    }
                    setTimeout(fSolution, 10000);
                } else {
                    page.evaluate(function(antiCaptchaApiKey, getSolution, taskId, solution) {
                        document.getElementsByClassName("captcha_enter")[0].value = solution;
                        document.getElementsByTagName("form")[0].submit();
                    }, antiCaptchaApiKey, getSolution, taskId, solution);
                    setTimeout(function () {
                        successClose();
                    }, 10000);
                }
            }
            setTimeout(fSolution, 10000);
        } else {
            successClose();
        }
    }
});

function getTaskId(antiCaptchaApiKey, img) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'https://api.anti-captcha.com/createTask', false);
    xhr.setRequestHeader("Content-Type", "application/json");
    var payload = {
        'clientKey' : antiCaptchaApiKey,
        'task' : {
            'type': "ImageToTextTask",
            'body': img,
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

function getSolution(antiCaptchaApiKey, taskId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'https://api.anti-captcha.com/getTaskResult', false);
    xhr.setRequestHeader("Content-Type", "application/json");
    var payload = {
        'clientKey' : antiCaptchaApiKey,
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

function successClose() {
    var content = page.content;
    var fs = require('fs');
    fs.write(filePath, content, 'w');
    page.close();
    phantom.exit();
}

function failClose() {
    page.close();
    phantom.exit();
}