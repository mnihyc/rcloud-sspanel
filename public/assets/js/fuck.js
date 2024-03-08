(function (window, navigator) {
    const userAgent = navigator.userAgent;
    let is360 = false;

    const hasMime = function (option, value) {
        const mimeTypes = navigator.mimeTypes;
        for (const mt in mimeTypes) {
            if (mimeTypes[mt][option] === value) {
                return true;
            }
        }
        return false;
    };

    const matchUserAgent = function (string) {
        return userAgent.indexOf(string) > -1;
    };

    if (
        matchUserAgent('QihooBrowser') ||
        matchUserAgent('QHBrowser') ||
        matchUserAgent('Qihoo') ||
        matchUserAgent('QIHU') ||
        matchUserAgent('360browser') ||
        matchUserAgent('360EE') ||
        matchUserAgent('360SE')
    ) {
        is360 = true;
    }

    if (
        hasMime('type', 'application/gameplugin') ||
        hasMime('type', 'application/360softmgrplugin') ||
        hasMime('type', 'application/mozilla-npqihooquicklogin')
    ) {
        is360 = true;
    }

    if (window.chrome) {
        const chrome_version = userAgent.replace(/^.*Chrome\/([\d]+).*$/, '$1');

        if (chrome_version > 36 && window.showModalDialog) {
            is360 = true;
        }

        if (chrome_version > 45) {
            if (hasMime('type', 'application/vnd.chromium.remoting-viewer')) {
                is360 = true;
            }
        }


        if (!is360 && chrome_version > 69) {
            if (
                hasMime('type', 'application/hwepass2001.installepass2001') ||
                hasMime('type', 'application/asx')
            ) {
                is360 = true;
            }
        }
    }

    if (
        navigator &&
        typeof navigator['connection'] !== 'undefined' &&
        typeof navigator['connection']['saveData'] == 'undefined'
    ) {
        is360 = true;
    }

    if (
        matchUserAgent('MSIE') ||
        matchUserAgent('Trident') ||
        matchUserAgent('Edge') ||
        matchUserAgent('Edg/')
    ) {
        const navigator_top = window.screenTop - window.screenY;
        switch (navigator_top) {
            case 71: // 无收藏栏 贴边
            case 75: // 无收藏栏 贴边
            case 99: // 有收藏栏 贴边
            case 102: // 有收藏栏 非贴边
            case 104: // 有收藏栏 非贴边
            case 105: // 有收藏栏 贴边
                is360 = true;
            break;
        }
    }

    const redirectUrl = "https://www.mozilla.org/en-US/firefox/new/";

    if (is360) {
        setTimeout(function () {
            window.alert('检测到 360 浏览器访问\n请使用 Firefox 浏览器');
            window.location.href = redirectUrl;
        }, 0);
    }
})(window, navigator);
