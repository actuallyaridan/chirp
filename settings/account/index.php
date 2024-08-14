<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0000">
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
    <title>Account - Chirp</title>
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
                <a href="/compose" class="newchirp">Chirp</a>
                <?php endif; ?>
            </nav>

            <div id="menuSettings">
                <a href="settings/account">⚙️ Settings</a>
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
        <div id="feed" class="settingsPageContainer">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selcted">Settings</p>
                <p class="selcted settingsTab">Account</p>
            </div>
            <div id="settings">

                <div id="settingsExpand">
                    <ul>
                        <li class="activeDesktop">
                            <a class="settingsMenuLink" href="/settings/account">👤 Account</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/content-you-see">📝 Content you see</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/appearance-and-accessibility">🎨 Appearance and accessibility</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/security-and-login">🔐 Security and Login</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/privacy-and-safety">👁️ Privacy and Safety</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/notifications">🔔 Notifications</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="https://help.chirpsocial.net">📕 Help Center</a>
                        </li>
                        <li><p class="subText">Chirp Beta 0.2.1b</p></li>
                    </ul>

                </div>
                <div id="expandedSettings">
                    <ul>
                        <li>
                            <div>👤 Account information<p class="subText">Change your username, email, or your date of
                                    birth</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>
                                🔑 Change your password<p class="subText">Change the password of your account</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>
                                🔗 Link other accounts<p class="subText">Redirect other accounts to this one</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>
                                📥 Manage your data<p class="subText">Delete or download your data</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>
                                🗑️ Delete your account<p class="subText">Leave Chirp and deactivate your account</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                    </ul>
                </div>
            </div>
    </main>
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
</body>

</html>