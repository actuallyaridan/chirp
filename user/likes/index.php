<?php
session_start();

// Check if 'id' parameter is provided in the URL
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$id) {
    $userNotFound = true;
    $invalidId = true;
} else {
    $invalidId = false;
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../../../chirp.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit;
    }

    // Fetch user details
    $stmt = $db->prepare('SELECT * FROM users WHERE LOWER(username) = LOWER(:username)');
    $stmt->bindParam(':username', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $userNotFound = true;
    } else {
        $userNotFound = false;
        $pageTitle = htmlspecialchars($user['name']) . ' (@' . htmlspecialchars($user['username']) . ') - Chirp';
        $isUserProfile = isset($_SESSION['username']) && strtolower($_SESSION['username']) === strtolower($user['username']);
        $user['is_verified'] = strtolower($user['isVerified']) === 'yes';

        // Fetch follower and following counts
        $followerStmt = $db->prepare('SELECT COUNT(*) FROM following WHERE following_id = :userId');
        $followerStmt->bindParam(':userId', $user['id']);
        $followerStmt->execute();
        $user['followers'] = $followerStmt->fetchColumn();

        $followingStmt = $db->prepare('SELECT COUNT(*) FROM following WHERE follower_id = :userId');
        $followingStmt->bindParam(':userId', $user['id']);
        $followingStmt->execute();
        $user['following'] = $followingStmt->fetchColumn();

        // Check if the signed-in user is following this user
        if (isset($_SESSION['user_id'])) {
            $checkFollowStmt = $db->prepare('SELECT 1 FROM following WHERE follower_id = :followerId AND following_id = :followingId');
            $checkFollowStmt->bindParam(':followerId', $_SESSION['user_id']);
            $checkFollowStmt->bindParam(':followingId', $user['id']);
            $checkFollowStmt->execute();
            $isFollowing = $checkFollowStmt->fetchColumn();
        } else {
            $isFollowing = false;
        }
    }

    // Check if the viewed user is followed by the logged-in user
    if (isset($_SESSION['user_id'])) {
        $checkFollowerStmt = $db->prepare('SELECT 1 FROM following WHERE follower_id = :followerId AND following_id = :followingId');
        $checkFollowerStmt->bindParam(':followerId', $user['id']); // This user is being viewed
        $checkFollowerStmt->bindParam(':followingId', $_SESSION['user_id']); // Current logged-in user
        $checkFollowerStmt->execute();
        $followsYou = $checkFollowerStmt->fetchColumn(); // Check if followed by the user being viewed
    } else {
        $followsYou = false;
    }

    $db = null;
}
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
    <title><?php echo $pageTitle; ?></title>
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
                    <a href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"
                        class="activeDesktop">
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
            <div id="timelineSelect">
                <div id="iconChirp" onclick="playChirpSound()">
                    <img src="/src/images/icons/chirp.svg" alt="Chirp">
                </div>
                <button id="back" class="selcted" onclick="back()"><i class="fa-solid fa-arrow-left"></i> Back </button>
            </div>
            <?php if ($userNotFound): ?>
                <!-- If post is not found or no ID provided, show this -->
                <div id="notFound">
                    <p>This account doesn't exist</p>
                    <p class="subText">Try searching for something else</p>
                </div>
            <?php else: ?>
                <div id="chirps">
                    <img class="userBanner"
                        src="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['userBanner']) : '/src/images/users/chirp/banner.png'; ?>" alt="">
                    <div class="account">
                        <div class="accountInfo">
                            <div>
                                <img class="userPic"
                                    src="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '/src/images/users/guest/user.svg'; ?>"
                                    alt="">
                                <div>
                                    <p>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                        <?php if ($user['is_verified']): ?>
                                            <img class="verified" src="/src/images/icons/verified.svg" alt="Verified">
                                        <?php endif; ?>
                                    </p>
                                    <p class="subText">
                                        @<?php echo htmlspecialchars($user['username']); ?>
                                        <?php if ($followsYou): ?>
                                            <span class="followsYouBadge">Follows you</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="timestampTimeline">
                                <?php if ($isUserProfile): ?>
                                    <a id="editProfileButton" class="followButton" href="/user/edit">Edit profile</a>
                                <?php else: ?>
                                    <button id="followProfileButton" class="followButton"
                                        onclick="toggleFollow(<?php echo $user['id']; ?>)"
                                        style="<?php echo $isFollowing ? 'display:none;' : ''; ?>">Follow</button>
                                    <button id="followingProfileButton" class="followButton following"
                                        onclick="toggleFollow(<?php echo $user['id']; ?>)"
                                        style="<?php echo $isFollowing ? '' : 'display:none;'; ?>">Following</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p>
                            <?php echo isset($user['bio']) ? htmlspecialchars($user['bio']) : 'This is a bio where you describe your account using at most 120 characters.'; ?>
                        </p>
                        <div id="accountStats">
                            <p class="subText">
                                <?php echo isset($user['following']) ? htmlspecialchars($user['following']) . ' following' : '0 following'; ?>
                            </p>
                            <p class="subText">
                                <?php echo isset($user['followers']) ? htmlspecialchars($user['followers']) . ' followers' : '0 followers'; ?>
                            </p>
                            <p class="subText">
                                joined:
                                <?php echo isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'unknown'; ?>
                            </p>
                        </div>
                    </div>
                    <div id="userNav">
                        <a id="chirpsNav" href="/user?id=<?php echo htmlspecialchars($user['username']); ?>">Chirps</a>
                        <a id="repliesNav"
                            href="/user/replies?id=<?php echo htmlspecialchars($user['username']); ?>">Replies</a>
                        <a id="mediaNav" href="/user/media?id=<?php echo htmlspecialchars($user['username']); ?>">Media</a>
                        <a id="likesNav" class="selcted"
                            href="/user/likes?id=<?php echo htmlspecialchars($user['username']); ?>">Likes</a>
                    </div>
                </div>
                <div id="posts" data-offset="0" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                    <!-- Chirps will be loaded here -->
                </div>
                <div id="noMoreChirps">
                    <div class="lds-ring">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <aside id="sideBar">
        <?php include '../../include/sideBar.php'; ?>
    </aside>
    <footer>
        <div class="mobileCompose">
            <?php if (isset($_SESSION['username'])): ?>
                <button class="newchirpmobile" onclick="openNewChirpModal()">Chirp</button>
            <?php endif; ?>
        </div>
        <div class="mobileMenuFooter">
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"
                class="active"><img src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <?php include '../../include/compose.php'; ?>
    <script defer src="/src/scripts/profile/loadLikes.js"></script>
    <script>
        function toggleFollow(userId) {
            const followButton = document.getElementById('followProfileButton');
            const followingButton = document.getElementById('followingProfileButton');

            // Determine action based on current button state
            const action = followButton.style.display === 'none' ? 'unfollow' : 'follow';

            fetch('/interact_user.php', { // Ensure the path is correct
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId,
                    action
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (action === 'follow') {
                            followButton.style.display = 'none'; // Hide Follow button
                            followingButton.style.display = ''; // Show Following button
                        } else if (action === 'unfollow') {
                            followButton.style.display = ''; // Show Follow button
                            followingButton.style.display = 'none'; // Hide Following button
                        }
                    } else if (data.error === 'not_signed_in') {
                        window.location.href = '/signin/';
                    } else {
                        console.error('Error:', data.message); // Log server errors
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error); // Log fetch errors
                });
        }
        <?php
        if (isset($_SESSION['error_message'])) {
            echo 'console.error(' . json_encode($_SESSION['error_message']) . ');';
            unset($_SESSION['error_message']); // Clear the error message after displaying it
        }
        ?>
    </script>

</body>

</html>