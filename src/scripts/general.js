window.onload = function () {
    // Parses the document body and    
    // inserts <img> tags in place of Unicode Emojis    
    twemoji.parse(document.body,
        { folder: 'svg', ext: '.svg' } // This is to specify to Twemoji to use SVGs and not PNGs
    );
    document.querySelector('#feedCompose').classList.add('swipe-up');
}

function showMenuSettings() {
    document.getElementById("menuSettings").classList.toggle("visible");
    document.getElementById("settingsButtonWrapper").classList.toggle("clickedDown");
}

function slideDown() {
    var element = document.getElementById('feedCompose');
    element.classList.add('slideDown');
    setTimeout(function () {
        var referrer = document.referrer;
        if (referrer.indexOf('chirp.aridan.net') !== -1) {
            window.history.back();
        } else {
            window.location.href = 'https://chirp.aridan.net';
        }
    }, 250);
}

function back() {
    var referrer = document.referrer || "";
    if (referrer.indexOf('chirp.aridan.net') !== -1) {
        window.history.back();
    } else {
        window.location.href = 'https://chirp.aridan.net';
    }
}