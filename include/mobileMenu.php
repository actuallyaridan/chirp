<div class="mobileCompose">
    <a class="chirpMoile" href="compose">Chirp</a>
</div>
<div>
    <a href="/" class="active"><img src="/src/images/icons/house.svg" alt="Home"></a>
    <a href="/explore"><img src="/src/images/icons/search.svg" alt="Explore"></a>
    <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
    <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Messages"></a>
    <a
        href="<?php echo isset($_SESSION['username']) ? '/user/?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
            src="/src/images/icons/person.svg" alt="Profile"></a>
</div>