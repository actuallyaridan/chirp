window.onload = function () {
    // Parses the document body and    
    // inserts <img> tags in place of Unicode Emojis    
    twemoji.parse(document.body,
        { folder: 'svg', ext: '.svg' } // This is to specify to Twemoji to use SVGs and not PNGs
    );
    document.querySelector('#feedCompose').classList.add('swipe-up');
}

function showMenuSettings(){
    document.getElementById("menuSettings").classList.toggle("visible");
    document.getElementById("settingsButtonWrapper").classList.toggle("clickedDown");
}

function slideDown() {
    var element = document.getElementById('feedCompose');
    element.classList.add('slideDown');
    // Wait for the duration of the animation (0.25 seconds) before redirecting
    setTimeout(function() {
        // Check if the previous URL contains 'chirp.aridan.net' or '127.0.0.1'
        var referrer = document.referrer;
        if (referrer.indexOf('chirp.aridan.net') !== -1 || referrer.indexOf('127.0.0.1') !== -1) {
            // If yes, go back in history
            window.history.back();
        } else {
            // If not, redirect to chirp.aridan.net
            window.location.href = 'https://chirp.aridan.net';
        }
    }, 250); // 250 milliseconds = 0.25 seconds
}

function back() {
    var element = document.getElementById('feed');
    element.classList.add('slideDown');
    // Wait for the duration of the animation (0.25 seconds) before redirecting
    setTimeout(function() {
        // Check if the current URL contains 'chirp.aridan.net' or '127.0.0.1'
        var currentURL = window.location.href;
        if (currentURL.indexOf('chirp.aridan.net') === -1 && currentURL.indexOf('127.0.0.1') === -1) {
            // If not, redirect to chirp.aridan.net
            window.location.href = 'https://chirp.aridan.net';
        } else {
            // If yes, go back in history
            window.history.back();
        }
    }, 250); // 250 milliseconds = 0.25 seconds
}
