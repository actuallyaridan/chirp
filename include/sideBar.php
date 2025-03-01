
<div id="trends">
    <p>Trends</p>
    <div>
        <a href="/discover/search?q=gay_people">chirp</a>
        <p class="subText">12 chirps</p>
    </div>
    <div>
        <a>twitter</a>
        <p class="subText">47 chirps</p>
    </div>
    <div>
        <a>iphone 16</a>
        <p class="subText">62 chirps</p>
    </div>
</div>
<?php if (isset($_SESSION['username'])): ?>
<div id="whotfollow">
    <p>Suggested accounts</p>
    <div>
        <div>
            <img class="userPic" src="https://pbs.twimg.com/profile_images/1797665112440045568/305XgPDq_400x400.png"
                alt="Apple">
            <div>
                <p>Apple <img class="verified" src="/src/images/icons/verified.svg" alt="Verified"></p>
                <p class="subText">@apple</p>
            </div>
        </div>
        <a class="followButton">Follow</a>
    </div>
    <div>
        <div>
            <img class="userPic" src="https://pbs.twimg.com/profile_images/1881368435453542400/NnD56DYV_400x400.jpg"
                alt="President Trump">
            <div>
                <p>President Trump <img class="verified" src="/src/images/icons/verified.svg" alt="Verified">
                </p>
                <p class="subText">@POTUS</p>
            </div>
        </div>
        <a class="followButton">Follow</a>
    </div>
</div>
<?php endif; ?>
<div>
    <p class="subText">Inspired by Twitter/X. No code has been sourced from Twitter/X. Twemoji by Twitter Inc/X
        Corp is licensed under CC-BY 4.0.

        <br><br>You're running: Chirp Beta 0.7b
    </p>
    <p class="subText">Visit Chirps source code on <a class="subText" href="https://github.com/actuallyaridan/chirp" target="_blank" rel="noopener noreferrer">GitHub</a></p>
    <p class="subText">Proudly developed in Sweden by <a  class="subText" href="https://aridan.net" target="_blank" rel="noopener noreferrer">Adnan Bukvic</a></p>
</div>