<div>
    <a href="/" class="active"><img src="/src/images/icons/house.svg" alt="Home"></a>
    <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
    <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
    <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
    <a
        href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
            src="/src/images/icons/person.svg" alt="Profile"></a>
</div>