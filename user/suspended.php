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
  
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js"
        crossorigin="anonymous"></script>
    <script src="/src/scripts/general.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Account Suspended - Chirp</title>
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
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/compose" class="newchirp">Chirp</a>
                <?php endif; ?>
            </nav>
            <div id="menuSettings">
                <a href="settings">⚙️ Settings</a>

                <a href="signin">🚪 Sign in</a>
            </div>
            <button id="settingsButtonWrapper" type="button" onclick=showMenuSettings()>
                <img class="userPic" src="/src/images/users/guest/user.svg" alt="aridan">
                <div>
                    <p>Guest</p>
                    <p class="subText">@guest</p>
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
            <div id="notFound">
                <p>This account has been suspended</p>
                <p class="subText">Chirp regularly suspends accounts that break <a href="rules">our rules</a>.</p>
            </div>
        </div>
    </main>
    <aside id="sideBar">
    <?php include '../include/sideBar.php';?>
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
</body>

</html>