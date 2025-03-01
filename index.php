<?php
session_start();
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">


    <link href="/src/styles/styles.css" rel="stylesheet">
    <link href="/src/styles/timeline.css" rel="stylesheet">
    <link href="/src/styles/menus.css" rel="stylesheet">
    <link href="/src/styles/responsive.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Cloudflare Web Analytics -->
    <script defer src='https://static.cloudflareinsights.com/beacon.min.js'
        data-cf-beacon='{"token": "04bd8091c3274c64b334b30906ea3c10"}'></script><!-- End Cloudflare Web Analytics -->
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Home - Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp" onclick="playChirpSound()">
                <a href="/" class="activeDesktop"><img src="/src/images/icons/house.svg" alt=""> Home</a>
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
            <!--<a href="https://sidebox.net/?ref=chirp" target="_blank"><img src="https://raw.githubusercontent.com/xkcdstickfigure/sidebox/main/banner.png" style="position: absolute; bottom: 96px; width: 256px; height: unset; margin: unset; border-radius: 8px; opacity: 0.8"></a>-->
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
            <div id="timelineSelect" class="extraBlur">
                <div class="TL">
                    <a class="menuMobileTL"> <img class="userPicTL"
                            src="<?php echo isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic']) : '/src/images/users/guest/user.svg'; ?>"
                            alt="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>"></a>
                    <div id="iconChirp" onclick="playChirpSound()">
                        <img src="/src/images/icons/chirp.svg" alt="Chirp">
                    </div>
                    <a class="menuMobileTL" href="/settings/account">⚙️</a>
                </div>
                <div>
                    <a id="forYou" class="selected" href="/">For you</a>
                    <a id="following" href="following">Following</a>
                </div>
            </div>
            <div id="highTraffic">
                <p></p>
            </div>
            <div id="chirps" data-offset="0">
                <div id="cookieConsent">
                    <div>
                        <p>🍪 Here, have some cookies!</p>
                        <p class="subText">Chirp uses cookies to improve your experience, to personalize content, and to
                            keep you signed in.
                            If you decline all cookies*, you can still use Chirp, but some features may not work as
                            intended.
                        </p>
                        <div>
                            <button class="button" type="button" onclick="acceptCookies()">Accept all cookies</button>
                            <button class="button following" type="button" onclick="acceptCookies()">Accept only
                                essential cookies</button>
                            <button type="button" class="button cancel" onclick="declineCookies()">Decline all
                                cookies*</button>
                        </div>
                    </div>
                </div>
                <!-- Chirps will be loaded here -->
            </div>
            <div id="noMoreChirps" style="display: none;">
                <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </main>

    <aside id="sideBar">
        <?php include 'include/sideBar.php'; ?>

    </aside>
    <footer>
        <div class="mobileCompose">
            <?php if (isset($_SESSION['username'])): ?>
                <button class="newchirpmobile" onclick="openNewChirpModal()">Chirp</button>
            <?php endif; ?>
        </div>
        <div class="mobileMenuFooter">
            <a href="/" class="active"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <?php include 'include/compose.php'; ?>
    <div id="signInModal" class="modal" style="display: none;">
        <div class="modal-content signIn">
            <h2>Sign in to Chirp</h2>
            <input type="text" id="username" placeholder="Email or username" required>
            <div class="modal-buttons">
                <button class="button followButton" id="okButton" onClick="closeWannaTalkAboutItModal()">Next</button>
                <a target="_blank" rel="nooepner noreferer" href="https://www.wannatalkaboutit.com/"
                    class="followButton following">Forgot password?</a>              
            </div>
            <div class="textButtons">
                    <a class="noAccountInModal">Don't have an account? Sign up</a>
                    <button onClick="closeWannaTalkAboutItModal()" class="noAccountInModal">Use Chirp as a guest instead</button>
                    </div>
        </div>
    </div>
    <script defer src="/src/scripts/loadChirps.js"></script>

</body>

</html>