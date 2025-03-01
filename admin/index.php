<?php
session_start();

// Check if the user is signed in and the username is "chirp"
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'chirp') {
    // If the user is not signed in or if the user is not 'chirp', show 403 Forbidden
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/src/styles/styles.css" rel="stylesheet">
        <link href="/src/styles/timeline.css" rel="stylesheet">
        <link href="/src/styles/menus.css" rel="stylesheet">
        <link href="/src/styles/responsive.css" rel="stylesheet">
        <title>403 Forbidden</title>
    </head>
    <body>
        <div id="feed" class="settingsPageContainer">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selected">403 Forbidden</p>
            </div>
            <div id="noMoreChirps">
                <p class="subText">Your account is not allowed to perform this action.</p>
                <a class="followButton following tryAgain" href="/">Go back home</a>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// Connect to the database
$db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');

// Function to generate a random invite code
function generateInviteCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// If the Generate new invite code button is clicked (via AJAX)
if (isset($_POST['generateCode'])) {
    // Generate a new invite code but do not save it yet
    $newInviteCode = generateInviteCode();

    // Return the new invite code as JSON response
    echo json_encode(['inviteCode' => $newInviteCode]);
    exit;
}

// If the Done button is clicked (via AJAX)
if (isset($_POST['doneCode'])) {
    // Get the invite code from the POST data
    $inviteCode = $_POST['inviteCode'];

    // Save the invite code to the database
    $stmt = $db->prepare("INSERT INTO invites (invite) VALUES (:invite)");
    $stmt->bindParam(':invite', $inviteCode);
    $stmt->execute();

    // Return a success message as JSON response
    echo json_encode(['status' => 'success']);
    exit;
}

// If the Migrate button is clicked (via AJAX)
if (isset($_POST['migrateUser'])) {
    $migrateFrom = $_POST['migrateFrom'];
    $migrateTo = $_POST['migrateTo'];
  
    // Validate input (optional, but recommended)
    // You can add checks here to ensure usernames are not empty, 
    // have a minimum length, or don't contain special characters.
  
    // Prepare the SQL statement to find the user with the migrateFrom username
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :migrateFrom");
    $stmt->bindParam(':migrateFrom', $migrateFrom);
    $stmt->execute();
  
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if ($user) {
      // Update the username in the database
      $stmt = $db->prepare("UPDATE users SET username = :migrateTo WHERE id = :id");
      $stmt->bindParam(':migrateTo', $migrateTo);
      $stmt->bindParam(':id', $user['id']);
      $stmt->execute();
  
      // Success message
      $message = "User migrated successfully!";
    } else {
      // Error message if user with migrateFrom username is not found
      $message = "User with username '$migrateFrom' not found.";
    }
  
    // Send the message back as JSON response
    echo json_encode(['message' => $message]);
    exit;
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

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
    <title>Admin panel - Chirp</title>
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
        <?php if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'chirp') : ?>
        <!-- If the user is not signed in or if the user is not 'chirp', show 403 Forbidden -->
        <div id="feed" class="settingsPageContainer">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selected">403 Forbidden</p>
            </div>
            <div id="noMoreChirps">
                <p class="subText">Your account is not allowed to perform this action.</p>
                <button class="followButton following tryAgain" onclick="window.location.href='/';">Go back home</button>
            </div>
        </div>
        <?php else : ?>
        <!-- If user is 'chirp', show the admin panel -->
        <div id="feed" class="settingsPageContainer">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div class="title">
                <p class="selected">Admin panel</p>
            </div>
            <div id="settings">
                <div id="settingsExpand">
                    <ul>
                        <li class="activeDesktop">
                            <a class="settingsMenuLink" href="/settings/account">👤 Manage users</a>
                        </li>
                        <li>
                            <a class="settingsMenuLink" href="/settings/content-you-see">📝 Manage content</a>
                        </li>
                    </ul>
                </div>
                <div id="expandedSettings">
                    <ul>
                        <li onclick="showinviteCodeModal()">
                            <div>➕ New invite code<p class="subText">Generate a new invite code for a user to sign up with</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>🔢 List invite codes<p class="subText">Show a list of created invite codes and what user has used them</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li>
                            <div>
                                🧑‍⚖️ Manage & moderate accounts<p class="subText">Change account details or suspend and moderate accounts</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                        <li onclick="showMigrateModal()">
                            <div>
                                🧑‍⚖️ Migrate user<p class="subText">Migrate a user from one username to another</p>
                            </div>
                            <p class="subText">▷</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
    <div id="inviteCodeModal" class="modal">
        <div class="modal-content editBannerModalContent">
            <h2>New invite code</h2>
            <textarea id="inviteCodeTextarea" disabled placeholder="Click on the button below to generate a new invite code" class="URLtextarea"></textarea>
            <p class="subText">You need to press "Save & close" after generating an invite code, otherwise it won't be valid. The invite code will automatically be copied to your clipboard when you save it.</p>
            <div class="modal-buttons">
                <button class= "button cancel">Clear</button>
                <button class="button following" id="generateCodeButton">Generate</button>
                <button class="button" id="doneButton" type="button">Save & close</button>
            </div>
        </div>
    </div>

    <div id="migrateModal" class="modal formGroup">
        <div class="modal-content editBannerModalContent">
            <h2>Migrate user</h2>
            <p class="subText">Move a user between handles</p>
            <textarea id="migrateFrom" placeholder="Migrate from" class="URLtextarea"></textarea>
            <textarea id="migrateTo" placeholder="Migrate to" class="URLtextarea"></textarea>
            <div class="modal-buttons">
                <button class="button following" id="migrateUser">Migrate</button>
                <button class="button" id="doneButton" type="button"  onclick="closeMigrateModal()">OK</button>
            </div>
            <div id="migrateStatus"></div>
        </div>
    </div>

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

    <script>
        function showinviteCodeModal() {
            document.getElementById("inviteCodeModal").style.display = "block";
        }

        function showMigrateModal() {
            document.getElementById("migrateModal").style.display = "block";
        }

        function closeinviteCodeModal() {
            document.getElementById("inviteCodeModal").style.display = "none";
        }

        function closeMigrateModal() {
            document.getElementById("migrateModal").style.display = "none";
        }

        // Clear the textarea and close the modal
        function clearTextareaAndCloseModal() {
            document.getElementById("inviteCodeTextarea").value = ''; // Clear the textarea
            closeinviteCodeModal(); // Close the modal
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("inviteCodeModal");
            if (event.target == modal) {
                closeinviteCodeModal();
            }
        }

        // Handle Generate new invite code button click
        document.getElementById("generateCodeButton").addEventListener("click", function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById("inviteCodeTextarea").value = response.inviteCode;
                }
            };
            xhr.send("generateCode=true");
        });

        // Handle Done button click
        document.getElementById("doneButton").addEventListener("click", function() {
            var inviteCode = document.getElementById("inviteCodeTextarea").value;

            // Copy the invite code to the clipboard
            navigator.clipboard.writeText(inviteCode).then(function() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            clearTextareaAndCloseModal(); // Clear textarea and close modal
                        }
                    }
                };
                xhr.send("doneCode=true&inviteCode=" + encodeURIComponent(inviteCode));
            });
        });

        // Handle Migrate button click
document.getElementById("migrateUser").addEventListener("click", function() {
  var migrateFrom = document.getElementById("migrateFrom").value;
  var migrateTo = document.getElementById("migrateTo").value;

  // Validate input (optional, but recommended)
  // You can add checks here to ensure usernames are not empty, 
  // have a minimum length, or don't contain special characters.

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onload   
 = function() {
    if (xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      document.getElementById("migrateStatus").textContent   
 = response.message;
    }
  };
  xhr.send("migrateUser=true&migrateFrom=" + encodeURIComponent(migrateFrom) + "&migrateTo=" + encodeURIComponent(migrateTo));
});
    </script>
</body>

</html>