let loadingChirps = false; // Flag to track if chirps are currently being loaded

function showLoadingSpinner() {
    document.getElementById('noMoreChirps').style.display = 'block';
}

function hideLoadingSpinner() {
    document.getElementById('noMoreChirps').style.display = 'none';
}

function loadChirps() {
    const userId = document.getElementById('posts').getAttribute('data-user-id');
    if (loadingChirps) return; // If already loading, exit

    const chirpsContainer = document.getElementById('posts');
    const offset = parseInt(chirpsContainer.getAttribute('data-offset'));

    loadingChirps = true; // Set loading flag
    showLoadingSpinner(); // Show loading spinner

    setTimeout(() => {
        fetch(`/user/likes/fetch_likes.php?offset=${offset}&user=${userId}`)
            .then(response => response.json())
            .then(chirps => {
                chirps.forEach(chirp => {


                    // Create chirp div
                    const chirpDiv = document.createElement('div');
                    chirpDiv.className = 'chirp';
                    chirpDiv.id = chirp.id;

                    // Create chirpClicker link
                    const chirpClicker = document.createElement('a');
                    chirpClicker.className = 'chirpClicker';
                    chirpClicker.href = `/chirp?id=${chirp.id}`;

                    // Create chirpInfo div
                    const chirpInfo = document.createElement('div');
                    chirpInfo.className = 'chirpInfo';

                    // User picture
                    const userPic = document.createElement('img');
                    userPic.className = 'userPic';
                    userPic.src = chirp.profilePic ? chirp.profilePic : '/src/images/users/guest/user.svg';
                    userPic.alt = chirp.name ? chirp.name : 'Guest';

                    // User details
                    const userDetails = document.createElement('div');
                    const userName = document.createElement('p');
                    userName.innerHTML = `
                ${chirp.name ? chirp.name : 'Guest'} 
                ${chirp.isVerified ? '<img class="verified" src="/src/images/icons/verified.svg" alt="Verified">' : ''}
            `;
                    const userUsername = document.createElement('p');
                    userUsername.className = 'subText';
                    userUsername.innerText = `@${chirp.username ? chirp.username : 'guest'}`;

                    userDetails.appendChild(userName);
                    userDetails.appendChild(userUsername);

                    // Add user picture and details to chirpInfo
                    const userWrapper = document.createElement('div');
                    userWrapper.appendChild(userPic);
                    userWrapper.appendChild(userDetails);
                    chirpInfo.appendChild(userWrapper);

                    // Timestamp
                    const timestampDiv = document.createElement('div');
                    timestampDiv.className = 'timestampTimeline';
                    const postedDate = document.createElement('p');
                    postedDate.className = 'subText postedDate';
                    postedDate.setAttribute('data-timestamp', chirp.timestamp);
                    timestampDiv.appendChild(postedDate);
                    chirpInfo.appendChild(timestampDiv);

                    // Preformatted chirp text
                    const chirpText = document.createElement('pre');
                    chirpText.innerHTML = chirp.chirp;

                    // Add chirpInfo and text to chirpClicker
                    chirpClicker.appendChild(chirpInfo);
                    chirpClicker.appendChild(chirpText);

                    // Create chirpInteract div
                    const chirpInteract = document.createElement('div');
                    chirpInteract.className = 'chirpInteract';

                    // Reply button
                    const replyButton = document.createElement('button');
                    replyButton.type = 'button';
                    replyButton.className = 'reply';
                    replyButton.onclick = () => location.href = `/chirp/?id=${chirp.id}`;
                    replyButton.innerHTML = `<img alt="Reply" src="/src/images/icons/reply.svg"> <span class="reply-count">${chirp.reply_count}</span>`;

                    // Rechirp button
                    const rechirpButton = document.createElement('button');
                    rechirpButton.type = 'button';
                    rechirpButton.className = 'rechirp';
                    rechirpButton.onclick = () => updateChirpInteraction(chirp.id, 'rechirp', rechirpButton);
                    rechirpButton.innerHTML = `<img alt="Rechirp" src="/src/images/icons/${chirp.rechirped_by_current_user ? 'rechirped' : 'rechirp'}.svg"> <span class="rechirp-count">${chirp.rechirp_count}</span>`;

                    // Like button
                    const likeButton = document.createElement('button');
                    likeButton.type = 'button';
                    likeButton.className = 'like';
                    likeButton.onclick = () => updateChirpInteraction(chirp.id, 'like', likeButton);
                    likeButton.innerHTML = `<img alt="Like" src="/src/images/icons/${chirp.liked_by_current_user ? 'liked' : 'like'}.svg"> <span class="like-count">${chirp.like_count}</span>`;

                    // interactLinker
                    const interactLinker = document.createElement('a');
                    interactLinker.className = 'interactLinkerPost';
                    interactLinker.href = `/chirp?id=${chirp.id}`;

                    // Append buttons to chirpInteract
                    chirpInteract.appendChild(replyButton);
                    chirpInteract.appendChild(rechirpButton);
                    chirpInteract.appendChild(likeButton);
                    chirpInteract.appendChild(interactLinker);

                    // Append chirpClicker and interact div to chirpDiv
                    chirpDiv.appendChild(chirpClicker);
                    chirpDiv.appendChild(chirpInteract);

                    // Finally append chirpDiv to the container
                    chirpsContainer.appendChild(chirpDiv);

                });

                chirpsContainer.setAttribute('data-offset', offset + 12); // Correctly increment the offset

                updatePostedDates();
                twemoji.parse(chirpsContainer);
            })
            .catch(error => {
                console.error('Error fetching chirps:', error);
            })
            .finally(() => {
                loadingChirps = false; // Reset loading flag
                hideLoadingSpinner(); // Hide loading spinner
            });
    }, 295);
}
// Function to handle button click animation
function handleButtonClick(button) {
    button.classList.add('button-clicked'); // Add the animation class
    setTimeout(() => {
        button.classList.remove('button-clicked'); // Remove the animation class after 100ms
    }, 100);
}

// Add event listeners to each button
document.querySelectorAll('.reply, .rechirp, .like').forEach(button => {
    button.addEventListener('click', () => {
        handleButtonClick(button); // Call the animation function
    });
});


function updateChirpInteraction(chirpId, action, button) {
    fetch(`/interact_chirp.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chirpId,
            action
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const countElement = button.querySelector(`.${action}-count`);
                const currentCount = parseInt(countElement.textContent.trim());

                if (action === 'like') {
                    button.querySelector('img').src = data.like ? '/src/images/icons/liked.svg' :
                        '/src/images/icons/like.svg';
                    countElement.textContent = data.like_count;
                } else if (action === 'rechirp') {
                    button.querySelector('img').src = data.rechirp ? '/src/images/icons/rechirped.svg' :
                        '/src/images/icons/rechirp.svg';
                    countElement.textContent = data.rechirp_count;
                }
            } else if (data.error === 'not_signed_in') {
                window.location.href = '/signin/';
            }
        })
        .catch(error => {
            console.error('Error updating interaction:', error);
        });
}


loadChirps();

window.addEventListener('scroll', () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
        loadChirps();
    }
});

setInterval(updatePostedDates, 1000);