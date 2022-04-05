phantom.injectJs('PhantomJSBootstrap.js');

page.open(inputArgs['url'], inputArgs['method'], inputArgs['data'], function(status) {
    if ("success" === status) {
        if ('GET' === inputArgs['method'] && Object.keys(inputArgs['data-parsed']).length > 0) {
            fillForm(inputArgs);
        }
        if (inputArgs.hasOwnProperty('snapshot-selector') && inputArgs.hasOwnProperty('snapshot-path')) {
            makeSnapshot(inputArgs);
        }
        if (hasCaptchaSign(inputArgs)) {
            if (!validCaptchaArgs(inputArgs)) {
                close('captcha arg error');
            }
            solveCaptcha(inputArgs);
        } else {
            successClose(inputArgs['file-path']);
        }
    } else {
        close('status ' + status);
    }
});

function fillForm(inputArgs) {
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

function solveCaptcha(inputArgs) {
    console.log('solve captcha start');
    var taskId = tryRequestTaskId(inputArgs);
    var tryNumber = 15;
    do {
        var solution = tryRequestSolution(taskId);
        tryNumber--;
        if (tryNumber <= 0) {
            console.log('too many tries');
            solveCaptcha(inputArgs);
        }
    } while (!solution);
    checkSolution(inputArgs, solution, taskId);
}

function incorrectCaptchaSolution(inputArgs, taskId) {
    console.log('captcha solution incorrect');
    if (1 === inputArgs['captcha-incorrect-report']) {
        page.evaluate(function(inputArgs, reportIncorrectImageCaptcha, taskId) {
            reportIncorrectImageCaptcha(inputArgs['captcha-api-key'], taskId);
        }, inputArgs, reportIncorrectImageCaptcha, taskId);
    }
    solveCaptcha(inputArgs);
}

function waitLoadPageAfterSubmit() {
    console.log('wait load page after submit');
    return page.evaluate(function(){
        return null !== document.querySelector('body');
    });
}

function loadedAfterSubmit(taskId) {
    console.log('loaded after submit');
    var hasCaptchaSign = page.evaluate(function(inputArgs){
        return document.body.innerHTML.indexOf(inputArgs['captcha-sign']) > 0;
    }, inputArgs);
    if (hasCaptchaSign) {
        incorrectCaptchaSolution(inputArgs, taskId);
    } else {
        successClose(inputArgs['file-path']);
    }
}

function checkSolution(inputArgs, solution, taskId) {
    console.log('check solution');
    if (false === submitForm(inputArgs, solution)) {
        close('captcha selectors error');
    }
    waitFor(waitLoadPageAfterSubmit, loadedAfterSubmit, inputArgs, taskId,20000, 1000);
}

function waitFor(testFx, onReady, inputArgs, taskId, timeOutMillis, nextCheckTimeoutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3000,
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                condition = testFx();
            } else {
                if(!condition) {
                    console.log('waitFor timeout');
                    solveCaptcha(inputArgs);
                } else {
                    onReady(taskId);
                    clearInterval(interval);
                }
            }
        }, nextCheckTimeoutMillis);
}

function submitForm(inputArgs, solution) {
    console.log('submit form');
    return page.evaluate(function(inputArgs, solution) {
        var input = document.querySelector(inputArgs['captcha-input-selector']);
        var form = document.querySelector(inputArgs['captcha-form-selector']);
        if (null === input || null === form) {
            return false;
        }
        input.value = solution;
        form.submit();
        return true;
    }, inputArgs, solution);
}

function getCaptchaBase64(inputArgs) {
    console.log('image to base64');
    if (!changeClipRect(inputArgs['captcha-image-selector'])) {
        close('captcha-image-selector error');
    }
    return page.renderBase64('jpg');
}

function makeSnapshot(inputArgs) {
    console.log('make snapshot ' + inputArgs['snapshot-path']);
    if (!changeClipRect(inputArgs['snapshot-selector'])) {
        close('snapshot-selector error');
    }
    page.render(inputArgs['snapshot-path']);
}

function hasCaptchaSign(inputArgs) {
    return inputArgs.hasOwnProperty('captcha-sign') &&
        '' !== inputArgs['captcha-sign'] &&
        page.content.indexOf(inputArgs['captcha-sign']) > 0;
}

function validCaptchaArgs(inputArgs) {
    return inputArgs.hasOwnProperty('captcha-api-key') &&
        inputArgs.hasOwnProperty('captcha-image-selector') &&
        inputArgs.hasOwnProperty('captcha-input-selector') &&
        inputArgs.hasOwnProperty('captcha-form-selector');
}

function tryRequestTaskId(inputArgs) {
    var imgBase64 = getCaptchaBase64(inputArgs);
    if (null == imgBase64) {
        close('image to base64 error');
    }
    var taskId = page.evaluate(function(inputArgs, requestTaskId, imgBase64) {
        return requestTaskId(inputArgs['captcha-api-key'], imgBase64)
    }, inputArgs, requestTaskId, imgBase64);
    if (false === taskId) {
        close('response empty taskId');
    }
    return taskId;
}

function tryRequestSolution(taskId) {
    return page.evaluate(function(inputArgs, requestSolution, taskId) {
        return requestSolution(inputArgs['captcha-api-key'], taskId);
    }, inputArgs, requestSolution, taskId);
}

function changeClipRect(selector) {
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

function requestTaskId(captchaApiKey, imgBase64) {
    console.log('request taskId');
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
    if (false === json || 0 !== json.errorId) {
        return false;
    }
    console.log('taskId = ' + json.taskId);
    return json.taskId;
}

function requestSolution(captchaApiKey, taskId) {
    console.log('try to request solution');
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
    if (false === json || 0 !== json.errorId || 'ready' !== json.status) {
        return false;
    }
    console.log('solution text = ' + json.solution.text);
    return json.solution.text;
}

function reportIncorrectImageCaptcha(captchaApiKey, taskId) {
    console.log('report about incorrect solution');
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'https://api.anti-captcha.com/reportIncorrectImageCaptcha', false);
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
    if (false === json || 0 !== json.errorId) {
        return false;
    }
}

function successClose(filePath) {
    var content = page.content;
    var fs = require('fs');
    fs.write(filePath, content, 'w');
    console.log('SUCCESS');
    close();
}

function close(msg) {
    if ('undefined' !== typeof msg) {
        console.log(msg);
    }
    page.close();
    phantom.exit(1)
}