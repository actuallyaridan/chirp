window.onload = function () {
    // Parses the document body and    
    // inserts <img> tags in place of Unicode Emojis    
    twemoji.parse(document.body,
        { folder: 'svg', ext: '.svg' } // This is to specify to Twemoji to use SVGs and not PNGs
    );
    document.querySelector('#feedCompose').classList.add('swipe-up');
}

document.addEventListener('DOMContentLoaded', (event) => {
    const cancelChirpButton = document.querySelector('.cancelChirp');
    const saveDraftButton = document.getElementById('saveDraftButton');
    const discardDraftButton = document.getElementById('discardDraftButton');
    const cancelModal = document.getElementById('cancelModal');
    const draftsContainer = document.querySelector('.drafts-container');

    cancelChirpButton.onclick = function () {
        const chirpContent = document.querySelector('textarea[name="chirpComposeText"]').value;
        if (chirpContent.trim().length > 0) {
            cancelModal.style.display = "block";
        } else {
            slideDownPost();
        }
    };

    saveDraftButton.onclick = function () {
        const chirpContent = document.querySelector('textarea[name="chirpComposeText"]').value;
        if (chirpContent.trim().length > 0) {
            saveDraft(chirpContent);
        }
        slideDownPost();
    };

    discardDraftButton.onclick = function () {
        slideDownPost();
    };

    window.onclick = function (event) {
        if (event.target == cancelModal) {
            cancelModal.style.display = "none";
        }
    };

    function saveDraft(content) {
        let drafts = JSON.parse(localStorage.getItem('draftChirps')) || [];
        drafts.push({ id: Date.now(), content: content });
        localStorage.setItem('draftChirps', JSON.stringify(drafts));
        displayDrafts();
    }

    function displayDrafts() {
        const drafts = JSON.parse(localStorage.getItem('draftChirps')) || [];
        draftsContainer.innerHTML = '';
        if (drafts.length > 0) {
            drafts.forEach((draft, index) => {
                const draftElement = document.createElement('div');
                draftElement.classList.add('draft');
    
                const draftText = document.createElement('p');
                draftText.innerText = draft.content;
                
                draftElement.addEventListener('click', () => {
                    const textarea = document.querySelector('textarea[name="chirpComposeText"]');
                    textarea.value = draft.content;
                    deleteDraft(index);
                    displayDrafts();
                });

                const deleteButton = document.createElement('button');
                deleteButton.innerText = 'Delete';
                deleteButton.addEventListener('click', (event) => {
                    event.stopPropagation(); // Prevent the click event from bubbling up to the draft element
                    deleteDraft(index);
                    displayDrafts(); // Refresh the display
                });
    
                draftElement.appendChild(draftText);
                draftElement.appendChild(deleteButton);
                draftsContainer.appendChild(draftElement);
            });
        } else {
            const noDraftsElement = document.createElement('div');
            const noDraftsText = document.createElement('p');
            noDraftsText.classList.add('subText');
            noDraftsText.innerText = 'You have no drafts.';
            
            noDraftsElement.appendChild(noDraftsText);
            draftsContainer.appendChild(noDraftsElement);
        }
    }

    function deleteDraft(index) {
        let drafts = JSON.parse(localStorage.getItem('draftChirps')) || [];
        drafts.splice(index, 1); // Remove the draft from the array
        localStorage.setItem('draftChirps', JSON.stringify(drafts)); // Update localStorage
    }

    displayDrafts();
});





function showMenuSettings() {
    document.getElementById("menuSettings").classList.toggle("visible");
    document.getElementById("settingsButtonWrapper").classList.toggle("clickedDown");
}

function slideDownPost() {
    var element = document.getElementById('feedCompose');
    element.classList.add('slideDown');
    setTimeout(back, 400); // Waits for 2000 milliseconds (2 seconds) before running the back function
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

document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('replytotext');
    var minHeight = textarea.scrollHeight; // Capture the initial height

    // Function to adjust the height of the textarea
    function adjustHeight() {
        textarea.style.height = '1.5em'; // Reset the height to auto to shrink if needed
        var newHeight = Math.max(minHeight, textarea.scrollHeight) + 'px'; // Ensure the height is at least the initial height
        if(newHeight === '40px'){
            textarea.style.height = '1.5em'; // Set the height based on the scroll height or min height
        }else{
            textarea.style.height = newHeight; // Set the height based on the scroll height or min height
        }
    }

    // Add an input event listener to adjust the height on every input
    textarea.addEventListener('input', adjustHeight);

    // Initial adjustment in case there is pre-filled content
    adjustHeight();
});

let chirpSound = null;


function playChirpSound() {
    if (chirpSound) {
        chirpSound.pause();
        chirpSound.currentTime = 0;
    }
    chirpSound = new Audio('/src/audio/whoLetTheBirdsOut.mp3');
    chirpSound.play().catch(error => console.error('Error playing sound:', error));
}