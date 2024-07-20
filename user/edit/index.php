<?php
session_start();

// Check if 'username' is set in the session
if (!isset($_SESSION['username'])) {
    // Handle error if username is not in the session
    $userNotFound = true;
} else {
    $username = $_SESSION['username'];
    $userNotFound = false;

    // Establish a connection to SQLite database
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling
    } catch(PDOException $e) {
        // Handle database connection error
        echo "Database connection failed: " . $e->getMessage();
        exit;
    }

    // Prepare SQL statement to fetch user details based on username (case insensitive)
    $stmt = $db->prepare('SELECT * FROM users WHERE LOWER(username) = LOWER(:username)');
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        // Handle case where user is not found
        $userNotFound = true;
    } else {
        $userNotFound = false;
        // Set the page title dynamically
        $isUserProfile = isset($_SESSION['username']) && strtolower($_SESSION['username']) === strtolower($user['username']);
    }

    // Close the database connection
    $db = null;
}
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
    <title>Editing profile - Chirp</title>
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
                <a href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"
                    class="activeDesktop"><img src="/src/images/icons/person.svg" alt=""> Profile</a>
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
            <div id="timelineSelect">
                <button id="back" class="selcted" onclick="back()"><img alt="" class="emoji"
                        src="/src/images/icons/back.svg"> Cancel </button>

            </div>
            <form method="POST" action="/user/edit/editProfile.php">
                <div id="chirps">
                    <div class="banner-container">
                        <button class="edit-banner-button" title="Edit banner" type="button"
                            onClick="openEditBannerModal()">✏️ Edit banner</button>
                        <img id="bannerPreview" class="userBanner"
                            src="<?php echo isset($user['userBanner']) ? htmlspecialchars($user['userBanner']) : '/src/images/users/chirp/banner.png'; ?>"
                            alt="User Banner">
                        <input type="hidden" name="userBanner" id="userBannerInput"
                            value="<?php echo isset($user['userBanner']) ? htmlspecialchars($user['userBanner']) : ''; ?>">
                    </div>
                    <div class="account">
                        <div class="accountInfo">
                            <div>
                                <div class="profile-container">
                                    <button class="edit-button" title="Edit profile picture" type="button"
                                        onClick="openEditProfilePicModal()">✏️</button>
                                    <img id="profilePicPreview" class="userPic"
                                        src="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '/src/images/users/guest/user.svg'; ?>"
                                        alt="<?php echo htmlspecialchars($user['name']); ?>">
                                    <input type="hidden" name="profilePic" id="profilePicInput"
                                        value="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['profilePic']) : ''; ?>">
                                </div>
                                <div>
                                    <textarea id="nameEdit" name="name" class="editText"
                                        placeholder="<?php echo htmlspecialchars($user['name']); ?>"><?php echo htmlspecialchars($user['name']); ?></textarea>
                                    <p class="subText">@<?php echo htmlspecialchars($user['username']); ?></p>
                                </div>
                            </div>
                            <div class="timestampTimeline">
                                <button type="submit" id="editProfileButton" class="followButton following">Save
                                    changes</button>
                            </div>
                        </div>
                        <textarea id="bioEdit" name="bio" class="editText"
                            placeholder="<?php echo isset($user['bio']) ? htmlspecialchars($user['bio']) : 'This is a bio where you describe your account using at most 120 characters.'; ?>"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        <div id="accountStats">
                            <p class="subText">
                                <?php echo isset($user['following']) ? htmlspecialchars($user['following']) . ' following' : '0 following'; ?>
                            </p>
                            <p class="subText">
                                <?php echo isset($user['followers']) ? htmlspecialchars($user['followers']) . ' followers' : '0 followers'; ?>
                            </p>
                        </div>
                    </div>
                    <div id="userNav">
                        <a id="chirpsNav" href="/user?id=<?php echo htmlspecialchars($user['username']); ?>">Chirps</a>
                        <a id="repliesNav"
                            href="/user/replies?id=<?php echo htmlspecialchars($user['username']); ?>">Replies</a>
                        <a id="mediaNav"
                            href="/user/media?id=<?php echo htmlspecialchars($user['username']); ?>">Media</a>
                        <a id="likesNav"
                            href="/user/likes?id=<?php echo htmlspecialchars($user['username']); ?>">Likes</a>
                    </div>
                </div>
            </form>
        </div>
        </p>
    </main>
    <aside id="sideBar">
        <?php include '../../include/sideBar.php';?>
    </aside>
    <footer>
        <div class="mobileCompose">
            <a class="chirpMoile" href="compose">Chirp</a>
        </div>
        <div>
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"
                class="active"><img src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerUrlInput = document.getElementById('bannerUrl');
        const profilePicUrlInput = document.getElementById('profilePicUrl');
        const bannerPreview = document.getElementById('bannerPreview');
        const profilePicPreview = document.getElementById('profilePicPreview');
        const userBannerInput = document.getElementById('userBannerInput');
        const profilePicInput = document.getElementById('profilePicInput');

        function validateImageUrl(url, callback) {
            const img = new Image();
            img.onload = function() {
                if (this.width > 0 && this.height > 0) {
                    callback(true);
                } else {
                    callback(false);
                }
            };
            img.onerror = function() {
                callback(false);
            };
            img.src = url;
        }

        function updateImagePreview(inputElement, previewElement, hiddenInputElement, defaultUrl) {
            const url = inputElement.value;
            if (url) {
                validateImageUrl(url, function(isValid) {
                    if (isValid) {
                        previewElement.src = url;
                        hiddenInputElement.value = url;
                    } else {
                        previewElement.src = defaultUrl;
                    }
                });
            } else {
                previewElement.src = defaultUrl;
                hiddenInputElement.value = '';
            }
        }

        // Update banner preview when URL input changes
        bannerUrlInput.addEventListener('input', function() {
            const defaultBannerUrl =
                '<?php echo isset($user['userBanner']) ? htmlspecialchars($user['userBanner']) : '/src/images/users/chirp/banner.png'; ?>';
            updateImagePreview(bannerUrlInput, bannerPreview, userBannerInput, defaultBannerUrl);
        });

        // Update profile picture preview when URL input changes
        profilePicUrlInput.addEventListener('input', function() {
            const defaultProfilePicUrl =
                '<?php echo isset($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '/src/images/users/guest/user.svg'; ?>';
            updateImagePreview(profilePicUrlInput, profilePicPreview, profilePicInput,
                defaultProfilePicUrl);
        });
    });
    </script>
    <div id="editBannerModal" class="modal">
        <div class="modal-content editBannerModalContent">
            <h2>Edit banner</h2>
            <p>You can change your banner at any time. Chirp does not have a storage space for it though, so you'd need
                to link a photo to use. We suggest Twitter as they do not expire.</p>
            <textarea id="bannerUrl" placeholder="URL" class="URLtextarea"></textarea>
            <div class="modal-buttons">
                <button class="button" id="okButton" onClick="closeEditBannerModal()" type="button">OK</button>
            </div>
        </div>
    </div>
    <div id="editProfilePicModal" class="modal">
        <div class="modal-content editBannerModalContent">
            <h2>Edit profile picture</h2>
            <p>You can change your profile picture at any time. Chirp does not have a storage space for it though, so
                you'd need
                to link a photo to use. We suggest Twitter as they do not expire.</p>
            <textarea id="profilePicUrl" placeholder="URL" class="URLtextarea"></textarea>
            <div class="modal-buttons">
                <button class="button" id="okButton" onClick="closeEditProfilePicModal()" type="button">OK</button>
            </div>
        </div>
    </div>
</body>

</html>