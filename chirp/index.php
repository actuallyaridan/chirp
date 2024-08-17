<?php
session_start();
try {
    // Connect to the SQLite database
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    include 'fetch_post.php';
    
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo isset($title) ? $title : 'Chirp'; ?></title>
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

<body>

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
            <div id="feed" class="thread">
                <div id="iconChirp" onclick="playChirpSound()">
                    <img src="/src/images/icons/chirp.svg" alt="Chirp">
                </div>
                <div id="timelineSelect">
                    <button id="back" class="selected" onclick="back()"><img alt="" class="emoji"
                            src="/src/images/icons/back.svg"> Chirp</button>
                </div>
                <?php if (!$post || empty($postId)) : ?>
                <!-- If post is not found or no ID provided, show this -->
                <div id="notFound">
                    <p>Chirp not found</p>
                    <p class="subText">That chirp does not exist. <br>It was most likely deleted, or it never existed in the first place.</p>
                </div>
                <?php else : ?>
                <!-- Display the fetched post -->
                <div id="context" data-offset="0">
                    <!-- Chirps will be loaded here -->
                </div>
                <div id="chirps">
                    <div class="chirpThread" id="<?php echo $postId; ?>">
                        <div class="chirpInfo">
                            <div>
                                <img class="userPic"
                                    src="<?php echo isset($profilePic) ?$profilePic : '/src/images/users/guest/user.svg'; ?>"
                                    
                                    alt="<?php echo isset($user) ? htmlspecialchars($user) : 'Guest'; ?>">
                                <div>
                                    <p><?php echo isset($name) ? $name : 'Guest'; ?></p>
                                    <p class="subText">@<?php echo isset($user) ? htmlspecialchars($user) : 'guest'; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="morePostOptionWrapper"><button class="morePostOptions"
                                    title="More..." onClick="openMoreOptionsModal()">🟰</button>
                                <div class="morePostOptionsModal" id="moreOptionsModal">
                                    <ul>
                                        <li id="editPost">✏️ Edit</li>
                                        <li id="editHistory">🕓 View edit history</li>
                                        <li id="copyPost">📋 Copy chirp</li>
                                        <li id="copyLink">🔗 Copy link</li>
                                        <li id="embedPost">🖇️ Embed chirp</li>
                                        <li id="pinPost">📌 Pin chirp</li>
                                        <li id="broadcastPost">📢 Broadcast chirp</li>
                                        <li id="changeReply">🔐 Change who can reply</li>
                                        <li id="hideReply">🤐 Hide reply</li>
                                        <li id="showHiddenReplies">🤐 Show hidden replies</li>
                                        <li id="translate">🗣️ Translate</li>
                                        <li id="suggestMore">😄 Suggest more</li>
                                        <li id="notInterested">🙂‍↔️ Not interested</li>
                                        <li id="writeNote">📝 Write a ChirpSees Note</li>
                                        <li id="muteConversation">🔇 Mute conversation</li>
                                        <li id="muteUser">🔇 Mute user</li>
                                        <li id="block">🚫 Block</li>
                                        <li id="report">🚩 Report</li>
                                        <li id="delete">🗑️ Delete</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Display chirp content with line breaks -->
                        <pre><?php echo $status; ?></pre>
                        <div class="chirpInteractThread">
                            <p class="subText postedDate">Posted on:
                                <script>
                                const options = {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                };
                                document.write(new Date("<?php echo $timestamp ?>").toLocaleString([], options));
                                </script> via Chirp for Web
                            </p>
                            <div>
                                <button type="button" class="reply">
                                    <img alt="Reply" src="/src/images/icons/reply.svg"><br>
                                    <?php echo ($reply_count == 1) ? '1 <span class="interactWithPostLabel">reply</span>' : $reply_count . ' <span class="interactWithPostLabel">replies</span>'; ?>
                                </button>

                                <?php if ($rechirped): ?>
                                <button type="button" class="rechirp rechirped">
                                    <img alt="Rechirped" src="/src/images/icons/rechirped.svg"><br>
                                    <?php echo ($rechirp_count == 1) ? '1 <span class="interactWithPostLabel">rechirp</span>' : $rechirp_count . ' <span class="interactWithPostLabel">rechirps</span>'; ?>
                                </button>
                                <?php else: ?>
                                <button type="button" class="rechirp">
                                    <img alt="Rechirp" src="/src/images/icons/rechirp.svg"><br>
                                    <?php echo ($rechirp_count == 1) ? '1 <span class="interactWithPostLabel">rechirp</span>' : $rechirp_count . ' <span class="interactWithPostLabel">rechirps</span>'; ?>
                                </button>
                                <?php endif; ?>

                                <?php if ($liked): ?>
                                <button type="button" class="like liked">
                                    <img alt="Liked" src="/src/images/icons/liked.svg"><br>
                                    <?php echo ($like_count == 1) ? '1 <span class="interactWithPostLabel">like</span>' : $like_count . ' <span class="interactWithPostLabel">likes</span>'; ?>
                                </button>
                                <?php else: ?>
                                <button type="button" class="like">
                                    <img alt="Like" src="/src/images/icons/like.svg"><br>
                                    <?php echo ($like_count == 1) ? '1 <span class="interactWithPostLabel">like</span>' : $like_count . ' <span class="interactWithPostLabel">likes</span>'; ?>
                                </button>
                                <?php endif; ?>
                            </div>

                        </div>
                        <form method="POST" action="/chirp/submit.php?id=<?php echo htmlspecialchars($postId); ?>"
                            id="replyTo">
                            <textarea name="chirpComposeText" id="replytotext" maxlength="240"
                                placeholder="Reply to @<?php echo isset($user) ? htmlspecialchars($user) : 'guest'; ?>..."></textarea>
                            <button type="submit" class="postChirp">Reply</button>
                        </form>
                    </div>
                </div>
                <div id="replies" data-offset="0">
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
                <?php endif; ?>
            </div>
        </main>

        <aside id="sideBar">
            <?php include '../include/sideBar.php';?>
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
                    href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                        src="/src/images/icons/person.svg" alt="Profile"></a>
            </div>
        </footer>
        <div id="likeModal" class="interaction-modal" style="display: none;">
            <ul class="interaction-modal-content">
                <li><button onclick="showLikes()"><img src="/src/images/icons/stats.svg" class="emoji">Show
                        likes</button></li>
                <li>
                    <button type="button" class="like"
                        onclick="updateChirpInteraction(<?php echo $postId; ?>, 'like', this)">
                        <img alt="Like" src="/src/images/icons/<?php echo $liked ? 'liked' : 'like'; ?>.svg">
                        <span class="like-button-text"><?php echo $liked ? 'Undo like' : 'Like'; ?></span>
                    </button>
                </li>
            </ul>
        </div>

        <div id="rechirpModal" class="interaction-modal" style="display: none;">
            <ul class="interaction-modal-content">
                <li><button onclick="showRechirps()"><img src="/src/images/icons/stats.svg" class="emoji">Show
                        rechirps</button></li>
                <li><button onclick="quoteChirp()"><img src="/src/images/icons/write.svg" class="emoji">Quote</button>
                </li>
                <li>
                    <button type="button" class="rechirp"
                        onclick="updateChirpInteraction(<?php echo $postId; ?>, 'rechirp', this)">
                        <img alt="Rechirp"
                            src="/src/images/icons/<?php echo $rechirped ? 'rechirped' : 'rechirp'; ?>.svg">
                        <span class="rechirp-button-text"><?php echo $rechirped ? 'Undo rechirp' : 'Rechirp'; ?></span>
                    </button>
                </li>
            </ul>
        </div>
        <script src="/src/scripts/general.js"></script>
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

            const chirpsContainer = document.getElementById('replies');
            const offset = parseInt(chirpsContainer.getAttribute('data-offset'));

            loadingChirps = true; // Set loading flag
            showLoadingSpinner(); // Show loading spinner

            setTimeout(() => {
                fetch(`/chirp/fetch_replies.php?offset=${offset}&for=<?php echo $postId; ?>`)
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
            }, 300);
        }

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
                        const imgElement = button.querySelector('img');

                        if (action === 'like') {
                            imgElement.src = data.like ? '/src/images/icons/liked.svg' :
                                '/src/images/icons/like.svg';
                            countElement.textContent = data.like_count;
                        } else if (action === 'rechirp') {
                            imgElement.src = data.rechirp ? '/src/images/icons/rechirped.svg' :
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


        document.addEventListener("DOMContentLoaded", function() {
            const likeModal = document.getElementById("likeModal");
            const rechirpModal = document.getElementById("rechirpModal");
            const likeButton = document.getElementById("likeButton");
            const rechirpButton = document.getElementById("rechirpButton");

            function openModal(modalId) {
                document.getElementById(modalId).style.display = "block";
            }


            window.onclick = function(event) {
                if (event.target == likeModal) {
                    likeModal.style.display = "none";
                } else if (event.target == rechirpModal) {
                    rechirpModal.style.display = "none";
                }
            }

            document.querySelector(".like").addEventListener("click", function() {
                openModal("likeModal");
            });

            document.querySelector(".rechirp").addEventListener("click", function() {
                openModal("rechirpModal");
            });

        });

        <?php
if (isset($_SESSION['error_message'])) {
    echo 'console.error(' . json_encode($_SESSION['error_message']) . ');';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>
        </script>
    </body>

</html>