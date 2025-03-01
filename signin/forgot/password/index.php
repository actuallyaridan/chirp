<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="mobile-web-app-capable" content="yes">
    

    <link href="/src/styles/styles.css" rel="stylesheet">
    <link href="/src/styles/timeline.css" rel="stylesheet">
    <link href="/src/styles/menus.css" rel="stylesheet">
    <link href="/src/styles/responsive.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Reset password - Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp" onclick="playChirpSound()">
                <a href="/"><img src="/src/images/icons/house.svg" alt=""> Home</a>
                <a href="/discover"><img src="/src/images/icons/search.svg" alt=""> Discover</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/notifications"><img src="/src/images/icons/bell.svg" alt=""> Notifications</a>
                <a href="/messages"><img src="/src/images/icons/envelope.svg" alt=""> Direct Messages</a>
                <a
                    href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>">
                    <img src="/src/images/icons/person.svg" alt=""> Profile
                </a>
                    <button class="newchirp" onclick="openNewChirpModal()">Chirp</button>
                <?php endif; ?>
            </nav>
            <div id="menuSettings">
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'chirp'): ?>
                <a href="/admin">🛡️ Admin panel</a>
                <?php endif; ?>
                <a href="/settings/account">⚙️ Settings</a>
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
                <p class="selected">Forgot password</p>
            </div>
            <div id="signUp">
                <p>Forgot your password? We'll get you back on Chirp!</p>
                <form id="signupForm" method="post" action="/signin/forgot/password/performReset.php">
                    <div id="inviteCode">
                        <p class="subText">We'll get your right back. First of all, let us know what account this is for.</p>
                        <div id="inputSignup">
                            <input type="text" id="user" name="usernameResetPassword" placeholder="Username" required>
                            <button type="button" class="followButton"
                                onclick="showNextSection('nameUser')">Next</button>
                        </div>
                    </div>
                    <div id="nameUser">
                        <p class="subText">Are you really you?<br>Please provide the invite code you used to create your account.</p>
                        <div id="inputSignup">
                        <input type="text" id="code" name="inviteResetPassword" placeholder="Invite code" required>
                            <button type="button" class="followButton"
                                onclick="showNextSection('pwordUser')">Next</button>
                        </div>
                    </div>
                    <div id="pwordUser">
                        <p class="subText">Okay, let's set your new password! You might be logged out from other sessions after it.</p>
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
                    <p id="invalidCodeError">Invalid or incorrect invite code</p>
                    <p id="usernameTakenError">User not found</p>
                    <p id="passwordMismatchError">Passwords do not match</p>
                </div>
            </div>
        </div>
    </main>
    <aside id="sideBar">
        <?php include '../../../include/sideBar.php';?>
    </aside>
    <footer>
        <div class="mobileMenuFooter">
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <?php include '../../include/compose.php'; ?>
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