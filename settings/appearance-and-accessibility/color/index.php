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
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Color / Appearance and accessibility - Chirp</title>
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
        <div id="feed" class="settingsPageContainer">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selcted">Settings</p>
                <p class="selcted settingsTab"><button id="back" class="settingsTab" onclick="back()"><i
                            class="fa-solid fa-arrow-left"></i> Color </button></p>
            </div>
            <div id="settings">

                <div id="settingsExpand">
                    <ul>
                        <li>
                            <a class="settingsMenuLink" href="/settings/account">👤 Account</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/content-you-see">📝 Content you see</a>
                        </li>
                        <li class="activeDesktop">
                            <a class="settingsMenuLink" href="/settings/appearance-and-accessibility">🎨 Appearance
                                and
                                accessibility</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/security-and-login">🔐 Security and
                                Login</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/privacy-and-safety">👁️ Privacy and
                                Safety</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/notifications">🔔 Notifications</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="https://help.chirpsocial.net">📕 Help Center</a>
                        </li>
                    </ul>

                </div>
                <div id="expandedSettings">
                    <div class="title inlineTitle">
                        <p class="selcted">Preview</p>
                    </div>
                    <div class="chirp" id="0">
                        <a class="chirpClicker">
                            <div class="chirpInfo">
                                <div>
                                    <img class="userPic chirpPreview" src="/src/images/users/chirp/user.svg"
                                        alt="Chirp">
                                    <div>
                                        <p>Chirp
                                            <img class="verified" src="/src/images/icons/verified.svg" alt="Verified">
                                        </p>
                                        <p class="subText">@chirp</p>
                                    </div>
                                </div>
                                <div class="timestampTimeline">
                                    <p class="subText postedDate">12d ago</p>
                                </div>
                            </div>
                            <pre>At the heart of Chirp, there are short messages called chirps -just like this one- which can include photos, videos, text, links and #hashtags!</pre>
                        </a>
                        <div class="chirpInteract">
                            <button type="button" class="reply"><img alt="Reply" src="/src/images/icons/reply.svg">
                                <span class="reply-count">918</span></button>
                            <button type="button" class="rechirp"><img alt="Rechirp"
                                    src="/src/images/icons/rechirp.svg"> <span class="rechirp-count">612</span></button>
                            <button type="button" class="like"><img alt="Like" src="/src/images/icons/like.svg">
                                <span class="like-count">2.3K</span></button>
                        </div>
                    </div>
                    <div class="title inlineTitle">
                        <p class="selected">Theme</p>
                    </div>
                    <div class="theme-options">
                        <label class="accentColor auto">
                            <input type="radio" name="theme" class="theme-radio" value="auto" checked/>
                            <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />

                                Follow system
                        </label>
                        <label class="accentColor light">
                            <input type="radio" name="theme" class="theme-radio" value="light" />
                            <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />

                                Light
                        </label>
                        <label class="accentColor dark">
                            <input type="radio" name="theme" class="theme-radio" value="dark" />
                            <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />

                                Dark
                        </label>
                    </div>

                    <div class="title inlineTitle">
    <p class="selected">Accent color</p>
</div>
<div class="color-options">
    <label class="accentColor green">
        <input type="radio" name="accent_color" class="color-radio" value="#1AD063" checked/>
        <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />
        </svg>
        Green
    </label>
    <label class="accentColor blue">
        <input type="radio" name="accent_color" class="color-radio" value="#10BDF3" />
        <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />
        </svg>
        Blue
    </label>
    <label class="accentColor purple">
        <input type="radio" name="accent_color" class="color-radio" value="#7A2BFC" />
        <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />
        </svg>
        Purple
    </label>
    <label class="accentColor orange">
        <input type="radio" name="accent_color" class="color-radio" value="#FF8B1F" />
        <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />
        </svg>
        Orange
    </label>
    <label class="accentColor red">
        <input type="radio" name="accent_color" class="color-radio" value="#FC2C6A" />
        <svg class="checkmark" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17l-5-5" fill="none" stroke="" stroke-width="2" />
        </svg>
        Red
    </label>
</div>



                    <div>
                        <div>
                            🎄Festive accent colors<p class="subText">Automatically changes your accent color during
                                a holiday (like Easter, Halloween or Christmas).</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
    </main>
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
</body>

</html>