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
        <a href="/compose" class="newchirp">Chirp</a>
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