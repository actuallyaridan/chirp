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
        var referrer = document.referrer || ""; // Set referrer to empty string if no referrer exists
        console.log("Referrer: " + referrer); // Log the referrer for debugging
    
        if (referrer.includes('chirp.aridan.net')) { // Checks if the referrer contains 'chirp.aridan.net'
            console.log("Navigating back in history");
            window.history.back();
        }
        if (referrer.includes('127.0.0.1')) { // Checks if the referrer contains 'chirp.aridan.net'
            console.log("Navigating back in history");
            window.history.back();
        } else {
            console.log("Redirecting to https://chirp.aridan.net");
            window.location.href = 'https://chirp.aridan.net';
        }
    }, 250);
}

function slideDownPost() {
    var element = document.getElementById('feedCompose');
    element.classList.add('slideDown');
}

   

function back() {
    var referrer = document.referrer || ""; // Set referrer to empty string if no referrer exists
    console.log("Referrer: " + referrer); // Log the referrer for debugging

    if (referrer.includes('chirp.aridan.net')) { // Checks if the referrer contains 'chirp.aridan.net'
        console.log("Navigating back in history");
        window.history.back();
    }
    if (referrer.includes('127.0.0.1')) { // Checks if the referrer contains 'chirp.aridan.net'
        console.log("Navigating back in history");
        window.history.back();
    } else {
        console.log("Redirecting to https://chirp.aridan.net");
        window.location.href = 'https://chirp.aridan.net';
    }
}
