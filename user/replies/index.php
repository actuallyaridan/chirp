<?php
session_start();

// Check if 'id' parameter is provided in the URL
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$id) {
    // Handle error if id parameter is missing or invalid
    $userNotFound = true;
    $invalidId = true;
} else {
    $invalidId = false;
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
    $stmt->bindParam(':username', $id);
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
        $pageTitle = 'Replies by ' . htmlspecialchars($user['name']) . ' (@' . htmlspecialchars($user['username']) . ') - Chirp';
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
    <title><?php echo $pageTitle; ?></title>
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
                    href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>" class="activeDesktop"><img
                        src="/src/images/icons/person.svg" alt=""> Profile</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/compose" class="newchirp">Chirp</a>
                <?php endif; ?>
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
                        src="/src/images/icons/back.svg"> Back </button>
            </div>
            <?php if ($userNotFound): ?>
            <!-- If post is not found or no ID provided, show this -->
            <div id="notFound">
                <p>User not found</p>
                <p class="subText">That user does not exist.<br>Maybe you can invite them?</p>
            </div>
            <?php else : ?>
            <div id="chirps">
                <img class="userBanner"
                    src="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['userBanner']) : '/src/images/users/chirp/banner.png'; ?>">
                <div class="account">
                    <div class="accountInfo">
                        <div>
                            <img class="userPic"
                                src="<?php echo isset($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '/src/images/users/guest/user.svg'; ?>"
                                alt="<?php echo htmlspecialchars($user['name']); ?>">
                            <div>
                                <p><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="subText">@<?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                        </div>
                        <div class="timestampTimeline">
                            <?php if ($isUserProfile): ?>
                                 <a id="editProfileButton" class="followButton" href="/user/edit">Edit profile</a>
                            <?php else: ?>
                            <a id="followProfileButton" class="followButton">Follow</a>
                            <?php endif; ?>
                            <a id="followingProfileButton" class="followButton following"
                                style="display:none;">Following</a>
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
                    </div>
                </div>
                <div id="userNav">
                    <a id="chirpsNav" href="/user?id=<?php echo htmlspecialchars($user['username']); ?>">Chirps</a>
                    <a id="repliesNav" class="selcted"
                        href="/user/replies?id=<?php echo htmlspecialchars($user['username']); ?>">Replies</a>
                    <a id="mediaNav" href="/user/media?id=<?php echo htmlspecialchars($user['username']); ?>">Media</a>
                    <a id="likesNav" href="/user/likes?id=<?php echo htmlspecialchars($user['username']); ?>">Likes</a>
                </div>
            </div>
            <div id="posts" data-offset="0">
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
        <?php include '../../include/sideBar.php';?>
    </aside>
    <footer>
        <div class="mobileCompose">
                <?php if (isset($_SESSION['username'])): ?>
            <a class="chirpMoile" href="compose">Chirp</a>
                <?php endif; ?>
        </div>
        <div>
            <a href="/"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"class="active"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <script>
    let loadingChirps = false; // Flag to track if chirps are currently being loaded

    function updatePostedDates() {
        const chirps = document.querySelectorAll('.chirp .postedDate');
        chirps.forEach(function(chirp) {
            const timestamp = chirp.getAttribute('data-timestamp');
            const postDate = new Date(parseInt(timestamp) * 1000);
            const now = new Date();
            const diffInMilliseconds = now - postDate;
            const diffInSeconds = Math.floor(diffInMilliseconds / 1000);
            const diffInMinutes = Math.floor(diffInSeconds / 60);
            const diffInHours = Math.floor(diffInMinutes / 60);
            const diffInDays = Math.floor(diffInHours / 24);

            let relativeTime;

            if (diffInSeconds < 60) {
                relativeTime = diffInSeconds + "s ago";
            } else if (diffInMinutes < 60) {
                relativeTime = diffInMinutes + "m ago";
            } else if (diffInHours < 24) {
                relativeTime = diffInHours + "h ago";
            } else if (diffInDays < 7) {
                relativeTime = diffInDays + "d ago";
            } else {
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                relativeTime = postDate.toLocaleString([], options);
            }

            chirp.textContent = relativeTime;
        });
    }

    function showLoadingSpinner() {
        document.getElementById('noMoreChirps').style.display = 'block';
    }

    function hideLoadingSpinner() {
        document.getElementById('noMoreChirps').style.display = 'none';
    }

    function loadChirps() {
        if (loadingChirps) return; // If already loading, exit

        const chirpsContainer = document.getElementById('posts');
        const offset = parseInt(chirpsContainer.getAttribute('data-offset'));

        loadingChirps = true; // Set loading flag
        showLoadingSpinner(); // Show loading spinner

        setTimeout(() => {
            fetch(`/user/replies/fetch_replies.php?offset=${offset}&user=<?php echo $user['id']; ?>`)
                .then(response => response.json())
                .then(chirps => {
                    chirps.forEach(chirp => {
                        const chirpDiv = document.createElement('div');
                        chirpDiv.className = 'chirp';
                        chirpDiv.id = chirp.id;
                        chirpDiv.innerHTML = `
                        <a class="chirpClicker" href="/chirp?id=${chirp.id}">
                            <div class="chirpInfo">
                                <div>
                                    <img class="userPic"
                                        src="${chirp.profilePic ? chirp.profilePic : '/src/images/users/guest/user.svg'}"
                                        alt="${chirp.name ? chirp.name : 'Guest'}">
                                    <div>
                                        <p>${chirp.name ? chirp.name : 'Guest'}
                                            ${chirp.isVerified ? '<img class="verified" src="/src/images/icons/verified.svg" alt="Verified">' : ''}
                                        </p>
                                        <p class="subText">@${chirp.username ? chirp.username : 'guest'}</p>
                                    </div>
                                </div>
                                <div class="timestampTimeline">
                                    <p class="subText postedDate" data-timestamp="${chirp.timestamp}"></p>
                                </div>
                            </div>
                            <pre>${chirp.chirp}</pre>
                        </a>
                        <div class="chirpInteract">
                            <button type="button" class="reply" onclick="location.href='/chirp/?id=${chirp.id}'"><img alt="Reply" src="/src/images/icons/reply.svg"> <span class="reply-count">${chirp.reply_count}</span></button>
                            <a href="/chirp?id=${chirp.id}"></a>
                               <button type="button" class="rechirp" onclick="updateChirpInteraction(${chirp.id}, 'rechirp', this)"><img alt="Rechirp" src="/src/images/icons/${chirp.rechirped_by_current_user ? 'rechirped' : 'rechirp'}.svg"> <span class="rechirp-count">${chirp.rechirp_count}</span></button>
                            <a href="/chirp?id=${chirp.id}"></a>
                                 <button type="button" class="like" onclick="updateChirpInteraction(${chirp.id}, 'like', this)"><img alt="Like" src="/src/images/icons/${chirp.liked_by_current_user ? 'liked' : 'like'}.svg"> <span class="like-count">${chirp.like_count}</span></button>
                        </div>
                    `;
                        chirpsContainer.appendChild(chirpDiv);
                    });

                    chirpsContainer.setAttribute('data-offset', offset +
                        12); // Correctly increment the offset

                    updatePostedDates();
                    twemoji.parse(chirpsContainer);
                })
                .catch(error => {
                    console.error('Error fetching chirps:', error);
                })
                .finally(() => {
                    loadingChirps = false; // Reset loading flag
                    hideLoadingSpinner(); // Hide loading spinner
                });
        }, 650);
    }

    // Function to handle button click animation
    function handleButtonClick(button) {
        button.classList.add('button-clicked'); // Add the animation class
        setTimeout(() => {
            button.classList.remove('button-clicked'); // Remove the animation class after 100ms
        }, 100);
    }

    // Add event listeners to each button
    document.querySelectorAll('.reply, .rechirp, .like').forEach(button => {
        button.addEventListener('click', () => {
            handleButtonClick(button); // Call the animation function
        });
    });


    function updateChirpInteraction(chirpId, action, button) {
        fetch(`/interact_chirp.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    chirpId,
                    action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const countElement = button.querySelector(`.${action}-count`);
                    const currentCount = parseInt(countElement.textContent.trim());

                    if (action === 'like') {
                        button.querySelector('img').src = data.like ? '/src/images/icons/liked.svg' :
                            '/src/images/icons/like.svg';
                        countElement.textContent = data.like_count;
                    } else if (action === 'rechirp') {
                        button.querySelector('img').src = data.rechirp ? '/src/images/icons/rechirped.svg' :
                            '/src/images/icons/rechirp.svg';
                        countElement.textContent = data.rechirp_count;
                    }
                } else if (data.error === 'not_signed_in') {
                    window.location.href = '/signin/';
                }
            })
            .catch(error => {
                console.error('Error updating interaction:', error);
            });
    }


    loadChirps();

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
            loadChirps();
        }
    });

    setInterval(updatePostedDates, 1000);



    <?php
if (isset($_SESSION['error_message'])) {
    echo 'console.error(' . json_encode($_SESSION['error_message']) . ');';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>
    </script>

</body>

</html>