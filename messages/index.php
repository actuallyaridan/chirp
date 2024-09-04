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
  
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js"
        crossorigin="anonymous"></script>
    <script src="/src/scripts/general.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Direct Messages - Chirp</title>
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
                <a href="/messages" class="activeDesktop"><img src="/src/images/icons/envelope.svg" alt=""> Direct Messages</a>
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
                    <p class="usernameMenu"><?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?>
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
                <p class="selcted">Direct Messages</p>
            </div>
            <form id="searchMenu">
                <div id="exploreer">
                    <textarea maxlength="240" placeholder="Find a message..."></textarea>
                </div>
                <div id="exploreChirp">
                    <button type="submit" class="postChirp">Search</button>
                </div>
            </form>
            <div id="noMoreChirps">
                <p class="subText">Something went wrong.</p>
                <button class="followButton following tryAgain">↻ Try again</button>
            </div>
        </div>
        </div>
    </main>
    <aside id="sideBar">
        <?php include '../include/sideBar.php';?>

    </aside>
    <footer>
        <!-- <div class="mobileCompose">
            <a class="chirpMoile" href="compose">New message</a>
        </div>
        -->
        <div>
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages" class="active"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
</body>

</html>