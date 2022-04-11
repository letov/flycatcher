exports.solveCaptcha = function solveCaptcha(inputArgs) {
    if (!hasCaptchaSign(inputArgs)) {
        return;
    }
    if (!validCaptchaArgs(inputArgs)) {
        close('captcha arg error');
    }
    console.log('solve captcha start');
    var taskId = tryRequestTaskId(inputArgs);
    var tryNumber = 15;
    do {
        var solution = tryRequestSolution(taskId);
        tryNumber--;
        if (tryNumber <= 0) {
            console.log('too many tries');
            exports.solveCaptcha(inputArgs);
        }
    } while (!solution);
    checkSolution(inputArgs, solution, taskId);
}

function hasCaptchaSign(inputArgs) {
    return inputArgs.hasOwnProperty('captcha-sign') &&
        '' !== inputArgs['captcha-sign'] &&
        page.content.indexOf(inputArgs['captcha-sign']) > 0;
}

function incorrectCaptchaSolution(inputArgs, taskId) {
    console.log('captcha solution incorrect');
    if (1 === inputArgs['captcha-incorrect-report']) {
        page.evaluate(function(inputArgs, reportIncorrectImageCaptcha, taskId) {
            reportIncorrectImageCaptcha(inputArgs['captcha-api-key'], taskId);
        }, inputArgs, reportIncorrectImageCaptcha, taskId);
    }
    exports.solveCaptcha(inputArgs);
}


function loadedAfterCaptchaSubmit(taskId) {
    console.log('loaded after submit');
    var hasCaptchaSign = page.evaluate(function(inputArgs){
        return document.body.innerHTML.indexOf(inputArgs['captcha-sign']) > 0;
    }, inputArgs);
    if (hasCaptchaSign) {
        incorrectCaptchaSolution(inputArgs, taskId);
    }
}

function checkSolution(inputArgs, solution, taskId) {
    console.log('check solution');
    if (false === submitCaptchaForm(inputArgs, solution)) {
        close('captcha selectors error');
    }
    waitForCaptcha(waitLoadPageAfterCaptchaSubmit, loadedAfterCaptchaSubmit, inputArgs, taskId,20000, 1000);
}

function waitLoadPageAfterCaptchaSubmit() {
    console.log('wait load page after submit');
    return page.evaluate(function(){
        return null !== document.querySelector('body');
    });
}

function waitForCaptcha(testFx, onReady, inputArgs, taskId, timeOutMillis, nextCheckTimeoutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3000,
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                condition = testFx();
            } else {
                if(!condition) {
                    console.log('waitForCaptcha timeout');
                    exports.solveCaptcha(inputArgs);
                } else {
                    onReady(taskId);
                    clearInterval(interval);
                }
            }
        }, nextCheckTimeoutMillis);
}

function getCaptchaBase64(inputArgs) {
    console.log('image to base64');
    if (!snapshot.changeClipRect(inputArgs['captcha-image-selector'])) {
        close('captcha-image-selector error');
    }
    return page.renderBase64('jpg');
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


function submitCaptchaForm(inputArgs, solution) {
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
