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
        window.history.back();
    }, 100); // 250 milliseconds = 0.25 seconds
}