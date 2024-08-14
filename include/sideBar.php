<div id="trends">
    <p>Trends</p>
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
        <a class="followButton following">Following</a>
    </div>
    <div>
        <div>
            <img class="userPic" src="https://pbs.twimg.com/profile_images/1380530524779859970/TfwVAbyX_400x400.jpg"
                alt="President Biden">
            <div>
                <p>President Biden <img class="verified" src="/src/images/icons/verified.svg" alt="Verified">
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

        <br><br>You're running: Chirp Beta 0.2.1b 
    </p>
</div>