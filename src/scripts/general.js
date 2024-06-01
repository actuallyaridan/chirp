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
    var referrer = document.referrer || ""; // Set referrer to empty string if no referrer exists
    var backAttempted = sessionStorage.getItem('backAttempted') || "false"; // Check if back has already been attempted

    if (referrer.indexOf('chirp.aridan.net') !== -1 && backAttempted === "false") {
        sessionStorage.setItem('backAttempted', "true"); // Set flag to prevent multiple back attempts
        window.history.back();
    } else {
        sessionStorage.setItem('backAttempted', "false"); // Reset flag when redirecting
        window.location.href = 'https://chirp.aridan.net';
    }
}
