let loadingChirps = false; // Flag to track if chirps are currently being loaded

function showLoadingSpinner() {
    console.log('Showing loading spinner');
    document.getElementById('noMoreChirps').style.display = 'block';
}

function hideLoadingSpinner() {
    console.log('Hiding loading spinner');
    document.getElementById('noMoreChirps').style.display = 'none';
}

function loadChirps() {
    console.log('loadChirps function called');
    if (loadingChirps) {
        console.log('Chirps are already being loaded, exiting');
        return; // If already loading, exit
    }

    const chirpsContainer = document.getElementById('chirps');
    const offset = parseInt(chirpsContainer.getAttribute('data-offset'));
    console.log(`Current offset: ${offset}`);

    loadingChirps = true; // Set loading flag
    console.log('Setting loadingChirps flag to true');
    showLoadingSpinner(); // Show loading spinner

    setTimeout(() => {
        console.log('Fetching chirps from server');
        fetch(`/fetch_chirps.php?offset=${offset}`)
            .then(response => {
                console.log('Received response from server');
                return response.json();
            })
            .then(chirps => {
                console.log('Successfully parsed chirps:', chirps);
                chirps.forEach(chirp => {
                    console.log(`Processing chirp with ID: ${chirp.id}`);

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
                    console.log(`Appended chirp with ID: ${chirp.id} to the container`);
                });

                chirpsContainer.setAttribute('data-offset', offset + 12); // Correctly increment the offset
                console.log(`Updated offset to: ${offset + 12}`);

                updatePostedDates();
                twemoji.parse(chirpsContainer);
            })
            .catch(error => {
                console.error('Error fetching chirps:', error);
            })
            .finally(() => {
                loadingChirps = false; // Reset loading flag
                console.log('Setting loadingChirps flag to false');
                hideLoadingSpinner(); // Hide loading spinner
            });
    }, 295);
}

// Function to handle button click animation
function handleButtonClick(button) {
    console.log('Button clicked, adding animation');
    button.classList.add('button-clicked'); // Add the animation class
    setTimeout(() => {
        console.log('Removing animation from button');
        button.classList.remove('button-clicked'); // Remove the animation class after 100ms
    }, 100);
}

// Add event listeners to each button
document.querySelectorAll('.reply, .rechirp, .like').forEach(button => {
    button.addEventListener('click', () => {
        console.log('Button event listener triggered');
        handleButtonClick(button); // Call the animation function
    });
});

function updateChirpInteraction(chirpId, action, button) {
    console.log(`Updating chirp interaction for chirp ID: ${chirpId}, action: ${action}`);
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
        .then(response => {
            console.log('Received response from interaction update');
            return response.json();
        })
        .then(data => {
            console.log('Interaction update response data:', data);
            if (data.success) {
                const countElement = button.querySelector(`.${action}-count`);
                const currentCount = parseInt(countElement.textContent.trim());

                if (action === 'like') {
                    button.querySelector('img').src = data.like ? '/src/images/icons/liked.svg' :
                        '/src/images/icons/like.svg';
                    button.classList.toggle('liked', data.like);
                    countElement.textContent = data.like_count;
                    button.style.color = data.like ? '#D92D20' : '';
                } else if (action === 'rechirp') {
                    button.querySelector('img').src = data.rechirp ? '/src/images/icons/rechirped.svg' :
                        '/src/images/icons/rechirp.svg';
                    button.classList.toggle('rechirped', data.rechirp); // Toggle 'rechirped' class
                    countElement.textContent = data.rechirp_count;
                    button.style.color = data.rechirp ? '#12B76A' : ''; // Set color based on rechirp status
                }
            } else if (data.error === 'not_signed_in') {
                console.log('User not signed in, redirecting to sign-in page');
                window.location.href = '/signin/';
            }
        })
        .catch(error => {
            console.error('Error updating interaction:', error);
        });
}

loadChirps();

window.addEventListener('scroll', () => {
    console.log('Scroll event detected');
    if (window.innerHeight + window.scrollY >= (document.body.offsetHeight - 120)) {
        console.log('Near bottom of page, loading more chirps');
        loadChirps();
    }
});

setInterval(updatePostedDates, 1000);