<div>
    <div class="modal modalCompose" id="newChirpModal" style="display: flex;">
        <div class="modal-content modalContentCompose">
            <form id="chirpForm" method="POST" action="/signin/signin.php">
                <div class="modal-header modalHeaderCompose">
                    <div class="postDrafts postDraftsCompose">
                        <button type="button" class="cancel" onclick="closeNewChirpModal()">Browse as guest</button>
                    </div>
                    <button type="submit" class="postChirp" id="postButton" onclick="replaceSignInText(event)">Sign in</button>
                </div>
                <div id="signInContent" class="composerContent">
                    <h1>Sign in to Chirp</h1>
                    <p class="subText">You've left the nest! Let's get you signed in again!</p>
                    <div id="signInCredentials">
                        <input type="text" id="username" name="username" placeholder="Username" required>
                        <input type="password" id="pWord" name="pWord" placeholder="Password" required>
                    </div>
                    <div>
                        <p class="err">❌ Wrong username or password.</p>
                        <p class="err">❌ You need to fill in both fields.</p>
                        <p class="err">❌ Something went wrong at our end, sorry!</p>
                    </div>
                    <div id="rememberMe">
                        <div>
                            <p>Remember me</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function replaceSignInText(event) {
        // Prevent the form from submitting to allow text change
        event.preventDefault();

        // Get the button and the loading spinner
        const button = document.getElementById('postButton');
        const spinner = button.querySelector('.lds-ring-inline');

        // Replace the text with the loading spinner and show it
        button.innerHTML = `
    <div class="lds-ring-inline">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>`;


        // You can also implement the form submission if needed
        // Example: simulate a delay (or submit the form)
        setTimeout(function () {
            // Optionally submit the form here, if you need to
             document.getElementById('chirpForm').submit();
        }, 500);
    }
</script>