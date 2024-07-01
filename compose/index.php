<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#00001" /><meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
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
    <title>Compose a Chirp / Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp" onclick="playChirpSound()">
                <a href="/"><img src="/src/images/icons/house.svg" alt=""> Home</a>
                <a href="explore"><img src="/src/images/icons/search.svg" alt=""> Explore</a>
                <a href="notifications"><img src="/src/images/icons/bell.svg" alt=""> Notifications</a>
                <a href="messages"><img src="/src/images/icons/envelope.svg" alt=""> Messages</a>
                <a href="user"><img src="/src/images/icons/person.svg" alt=""> user</a>
            </nav>
            <div id="menuSettings">
                <a href="settings">⚙️ Settings</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/signout.php">🚪 Sign Out</a>
                <?php else: ?>
                <a href="/signin/">🚪 Sign In</a>
                <?php endif; ?>
            </div>
            <button id="settingsButtonWrapper" type="button" onclick="showMenuSettings()">
                <img class="userPic"
                    src="<?php echo isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic']) : '/src/images/users/guest/user.svg'; ?>"
                    alt="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>">
                <div>
                    <p><?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?></p>
                    <p class="subText">
                        @<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>
                    </p>
                </div>
                <p class="settingsButton">⚙️</p>
            </button>
        </div>
    </header>
    <main>
        <div id="cancelModal" class="modal">
            <div class="modal-content">
                <h2>Save as draft?</h2>
                <p>It seems like you've written something without chirping it.<br>Do you want Chirpie to hold on to what
                    you wrote and store it as a draft on your device?</p>
                <button id="saveDraftButton" class="modal-button">Save as draft</button>
                <button id="discardDraftButton" class="modal-button">No</button>
            </div>
        </div>
        <div id="feedCompose">
            <div id="iconChirp">
                <img src="/src/images/icons/write.svg" alt="Write">
            </div>
            <div class="title">
                <p class="selected">Compose a Chirp</p>
            </div>
            <form method="POST" action="/compose/submit.php">
                <div id="composer">
                    <textarea name="chirpComposeText" maxlength="240" placeholder="What's on your mind?"></textarea>
                </div>
                <div id="exploreChirp" class="searchButtons">
                    <button type="button" class="cancelChirp" onclick="slideDown()">Cancel</button>
                    <button type="submit" class="postChirp" onclick="slideDownPost()">Chirp</button>
                </div>
            </form>
            <div class="title">
                <p class="selected">Drafts</p>
            </div>
            <div class="drafts-container">
                <p class="subText">You have no drafts.</p>
            </div>
        </div>
    </main>
    <aside id="sideBar">
        <div id="trends">
            <p>Trends for you</p>
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
            <p>Who to follow</p>
            <div>
                <div>
                    <img class="userPic"
                        src="https://pbs.twimg.com/user_images/1717013664954499072/2dcJ0Unw_400x400.png" alt="">
                    <div>
                        <p>Apple <img class="verified" src="/src/images/icons/verified.svg" alt=""></p>
                        <p class="subText">@apple</p>
                    </div>
                </div>
                <a class="followButton">Follow</a>
            </div>
            <div>
                <div>
                    <img class="userPic"
                        src="https://pbs.twimg.com/user_images/1380530524779859970/TfwVAbyX_400x400.jpg" alt="">
                    <div>
                        <p>President Biden <img class="verified" src="/src/images/icons/verified.svg" alt=""></p>
                        <p class="subText">@POTUS</p>
                    </div>
                </div>
                <a class="followButton">Follow</a>
            </div>
        </div>
        </div>
        <div>
            <p class="subText">Inspired by Twitter/X. No code has been sourced from Twitter/X. Twemoji by Twitter Inc/X
                Corp is licensed under CC-BY 4.0.</p>
        </div>
    </aside>
    <footer>
        <div>
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/explore"><img src="/src/images/icons/search.svg" alt="Explore"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Messages"></a>
            <a href="/user"><img src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
</body>

</html>