<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#00001" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link href="/src/styles/styles.css" rel="stylesheet">
    <link href="/src/styles/timeline.css" rel="stylesheet">
    <link href="/src/styles/menus.css" rel="stylesheet">
    <link href="/src/styles/responsive.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js" crossorigin="anonymous">
    </script>
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Get started chirping - Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp" onclick="playChirpSound()">
                <a href="/"><img src="/src/images/icons/house.svg" alt=""> Home</a>
                <a href="/discover"><img src="/src/images/icons/search.svg" alt=""> Discover</a>
                <a href="/notifications"><img src="/src/images/icons/bell.svg" alt=""> Notifications</a>
                <a href="/messages"><img src="/src/images/icons/envelope.svg" alt=""> Direct Messages</a>
                <a
                    href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                        src="/src/images/icons/person.svg" alt=""> Profile</a>
                <a href="/compose" class="newchirp">Chirp</a>
            </nav>
            <div id="menuSettings">
                <a href="settings">⚙️ Settings</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/signout.php">🚪 Sign out</a>
                <?php else: ?>
                <a href="/signin/">🚪 Sign in</a>
                <?php endif; ?>
            </div>
            <button id="settingsButtonWrapper" type="button" onclick="showMenuSettings()">
                <img class="userPic"
                    src="<?php echo isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic']) : '/src/images/users/guest/user.svg'; ?>"
                    alt="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>">
                <div>
                    <p class="usernameMenu">
                        <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?>
                        <?php if (isset($_SESSION['is_verified']) && $_SESSION['is_verified']): ?>
                        <img class="emoji" src="/src/images/icons/verified.svg" alt="Verified">
                        <?php endif; ?>
                    </p>
                    <p class="subText">
                        @<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>
                    </p>
                </div>
                <p class="settingsButton">⚙️</p>
            </button>
        </div>
    </header>
    <main>
        <div id="feed">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selected">Sign up</p>
            </div>
            <div id="signUp">
                <p>Let's create an account for you!</p>
                <form id="signupForm" method="post" action="/signup/signup.php">
                    <div id="inviteCode">
                        <p class="subText">Chirp is invite only for the time being. You'll need an invite code to create
                            an
                            account.</p>
                        <p class="subText">Don't have an invite code? You can get one by sending a DM on Twitter to
                            @actuallyaridan or @use_chirp.</p>
                        <div id="inputSignup">
                            <input type="text" id="code" name="code" placeholder="Invite code" required>
                            <button type="button" class="followButton"
                                onclick="showNextSection('nameUser')">Next</button>
                        </div>
                    </div>
                    <div id="nameUser">
                        <p class="subText">Great! Now Chirpie just needs to get to know you and he'll set up your
                            account in
                            no time!</p>
                        <div id="inputSignup">
                            <div id="inputName">
                                <input type="text" id="name" name="name" placeholder="Name" required>
                                <input type="text" id="username" name="username" placeholder="Username" required>
                            </div>
                            <input type="email" id="email" name="email" placeholder="example@email.com" required>
                            <button type="button" class="followButton"
                                onclick="showNextSection('pwordUser')">Next</button>
                        </div>
                    </div>
                    <div id="pwordUser">
                        <p class="subText">Amazing! Now we just need to set up a password for you and you'll be all
                            done!
                        </p>
                        <div id="inputSignup">
                            <div id="inputName">
                                <input type="password" id="pword" name="pword" placeholder="Password" required>
                                <input type="password" id="pwordConfirm" name="pwordConfirm"
                                    placeholder="Confirm password" required>
                            </div>
                            <button type="submit" class="followButton">Complete</button>
                        </div>
                    </div>
                </form>
                <div id="errors">
                    <p id="invalidCodeError">Invalid invite code.<br>If you don't have an invite code, send a DM to @actuallyaridan or @use_chirp over Twitter.</p>
                    <p id="usernameTakenError">This username already in use.<br>You need to have a unique username.</p>
                    <p id="passwordMismatchError">Passwords do not match</p>
                    <p id="invalidUsernameError">Invalid username. <br>Only latin letters A to Z and numbers 0 to 9 are allowed.</p>
                    <p id="reservedUsernameError">This username is reserved.<br>You need a reserved invite code in order to register it. Obtain one by sending a DM to @actuallyaridan or @use_chirp over Twitter.</p>
                    <p id="inviteNotReservedError">You're using a reserved invite code. <br>You must register the username linked with this invite code.</p>
                </div>
            </div>
        </div>
    </main>
    <aside id="sideBar">
        <div id="trends">
            <p>Trends</p>
            <div>
                <a>gay people</a>
                <p class="subText">12 chirps</p>
            </div>
            <div>
                <a>twitter</a>
                <p class="subText">47 chirps</p>
            </div>
            <div>
                <a>iphone 69</a>
                <p class="subText">62 chirps</p>
            </div>
        </div>
        <div id="whotfollow">
            <p>Suggested accounts</p>
            <div>
                <div>
                    <img class="userPic"
                        src="https://pbs.twimg.com/profile_images/1797665112440045568/305XgPDq_400x400.png" alt="Apple">
                    <div>
                        <p>Apple <img class="verified" src="/src/images/icons/verified.svg" alt="Verified"></p>
                        <p class="subText">@apple</p>
                    </div>
                </div>
                <a class="followButton following">Following</a>
            </div>
            <div>
                <div>
                    <img class="userPic"
                        src="https://pbs.twimg.com/profile_images/1380530524779859970/TfwVAbyX_400x400.jpg"
                        alt="President Biden">
                    <div>
                        <p>President Biden <img class="verified" src="/src/images/icons/verified.svg" alt="Verified">
                        </p>
                        <p class="subText">@POTUS</p>
                    </div>
                </div>
                <a class="followButton">Follow</a>
            </div>
        </div>
        <div>
            <p class="subText">Inspired by Twitter/X. No code has been sourced from Twitter/X. Twemoji by Twitter Inc/X
                Corp is licensed under CC-BY 4.0.

                <br><br>You're running: Chirp Beta 0.0.7b
            </p>
        </div>
    </aside>
    <footer>
        <div>
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <script>
    function showNextSection(nextSectionId) {
        const sections = ['inviteCode', 'nameUser', 'pwordUser'];

        // Hide all sections first
        sections.forEach(sectionId => {
            document.getElementById(sectionId).style.display = 'none';
        });

        // Display the next section based on nextSectionId
        if (nextSectionId) {
            document.getElementById(nextSectionId).style.display = 'block';
        }
    }
    document.getElementById('signupForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Display error message
                    document.getElementById('errors').style.display = 'block';
                    document.getElementById('invalidCodeError').style.display = 'none';
                    document.getElementById('usernameTakenError').style.display = 'none';
                    document.getElementById('passwordMismatchError').style.display = 'none';
                    document.getElementById('invalidUsernameError').style.display = 'none';
                    document.getElementById('reservedUsernameError').style.display = 'none';
                    document.getElementById('inviteNotReservedError').style.display = 'none';

                    if (data.error === 'Invalid invite code') {
                        document.getElementById('invalidCodeError').style.display = 'block';
                    } else if (data.error === 'Username already in use') {
                        document.getElementById('usernameTakenError').style.display = 'block';
                    } else if (data.error === 'Passwords do not match') {
                        document.getElementById('passwordMismatchError').style.display = 'block';
                    } else if (data.error === 'Invalid username. Only letters and numbers are allowed.') {
                        document.getElementById('invalidUsernameError').style.display = 'block';
                    } else if (data.error === 'This username is reserved.') {
                        document.getElementById('reservedUsernameError').style.display = 'block';
                    } else if (data.error === 'Invite not reserved for this username') {
                        document.getElementById('inviteNotReservedError').style.display = 'block';
                    }
                } else if (data.success) {
                    // Handle success scenario, maybe redirect to a success page
                    window.location.href = '/signin/'; // Example redirect
                }
            })
            .catch(error => console.error('Error:', error));
    });
    </script>
</body>

</html>