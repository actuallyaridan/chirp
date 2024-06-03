<?php
$filename = "../compose/chirp.json";
$myfile = fopen($filename, "r") or die("Unable to open file!");
$file_size = filesize($filename);
$file_content = fread($myfile, $file_size);
fclose($myfile);

$obj = json_decode($file_content);

if ($obj !== null) {
    $user = $obj->user;
    $timestamp = gmdate("Y-m-d\TH:i\Z", $obj->timestamp);
    $status = $obj->status;
} else {
    echo "Error decoding JSON";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#00001" />
    <link href="/src/styles/styles.css" rel="stylesheet">
    <link href="/src/styles/timeline.css" rel="stylesheet">
    <link href="/src/styles/menus.css" rel="stylesheet">
    <link href="/src/styles/responsive.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js" crossorigin="anonymous">
    </script>
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Chirp / Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
                <a href="/"><img src="/src/images/icons/house.svg" alt=""> Home</a>
                <a href="/explore"><img src="/src/images/icons/search.svg" alt=""> Explore</a>
                <a href="/notifications"><img src="/src/images/icons/bell.svg" alt=""> Notifications</a>
                <a href="/messages"><img src="/src/images/icons/envelope.svg" alt=""> Messages</a>
                <a href="/profile"><img src="/src/images/icons/person.svg" alt=""> Profile</a>
                <a href="/compose" class="newchirp">Chirp</a>
            </nav>
            <div id="menuSettings">
                <a href="settings">⚙️ Settings</a>

                <a href="signin">🚪 Sign in</a>
            </div>
            <button id="settingsButtonWrapper" type="button" onclick=showMenuSettings()>
                <img class="profilePic" src="/src/images/profiles/guest/profile.svg" alt="aridan">
                <div>
                    <p>Guest</p>
                    <p class="subText">@guest</p>
                </div>
                <p class="settingsButton">⚙️</p>
            </button>
        </div>
    </header>
    <main>
        <div id="feed" class="thread">
            <div id="iconChirp">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div id="timelineSelect">
                <button id="back" class="selcted" onclick="back()"><img alt="" class="emoji"
                        src="/src/images/icons/back.svg"> Back</button>
            </div>
            <div id="highTraffic">
                <p>We're experiencing very high traffic right now.<br>Chirpie is trying his best, but if Chirp slows
                    down, don't panic!</p>
            </div>
            <div id="chirps">
                <div class="chirpThread" id="1">
                    <div class="chirpInfo">
                        <img class="profilePic" src="/src/images/profiles/chirp/profile.svg" alt="aridan">
                        <div>
                            <p>Chirp <img class="verified" src="/src/images/icons/verified.svg" alt="Verified"></p>
                            <p class="subText">@chirp</p>
                        </div>
                    </div>
                    <p><?php echo $status; ?></p>
                    <div class="chirpInteractThread">
                        <p class="subText postedDate">Posted at: <script>
                            const options = {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            };
                            document.write(new Date("<?php echo $timestamp ?>").toLocaleString([], options));
                            </script>
                        </p>
                        <div>
                            <button type="button" class="reply"><img alt="Reply" src="/src/images/icons/reply.svg"><br>0
                                replies</button><button type="button" class="rechirp"><img alt="Rechirp"
                                    src="/src/images/icons/rechirp.svg"><br>0 rechirps</button><button type="button"
                                class="like"><img alt="Like" src="/src/images/icons/like.svg"><br>0 likes</button>
                        </div>
                    </div>
                    <div id="replyTo">
                        <textarea maxlength="240" placeholder="Reply to @chirp..."></textarea>
                    </div>
                    <div id="postReply">
                        <button class="postChirp">Reply</button>
                    </div>
                </div>
            </div>
            <div id="noMoreChirps">
                <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </main>
    <div class="mobileCompose">
        <a class="chirpMoile" href="compose">Chirp</a>
    </div>
    <aside id="sideBar">
        <div id="trends">
            <p>Trends for you</p>
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
        <div id="whotfollow">
            <p>Who to follow</p>
            <div>
                <div>
                    <img class="profilePic"
                        src="https://pbs.twimg.com/profile_images/1717013664954499072/2dcJ0Unw_400x400.png" alt="Apple">
                    <div>
                        <p>Apple <img class="verified" src="/src/images/icons/verified.svg" alt="Verified"></p>
                        <p class="subText">@apple</p>
                    </div>
                </div>
                <a class="followButton">Follow</a>
            </div>
            <div>
                <div>
                    <img class="profilePic"
                        src="https://pbs.twimg.com/profile_images/1380530524779859970/TfwVAbyX_400x400.jpg"
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
        </div>
        <div>
            <p class="subText">Inspired by Twitter/X. No code has been sourced from Twitter/X.</p>
        </div>
    </aside>
    <footer>
        <div>
            <a href="/" class="active"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/explore"><img src="/src/images/icons/search.svg" alt="Explore"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Messages"></a>
            <a href="/profile"><img src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
</body>

</html>