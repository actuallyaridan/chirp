<?php
session_start();
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
    <title>Compose a Chirp - Chirp</title>
    <style>
        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: #ffffff;
            border-radius: 10px;
            width: 90%;
            min-width: fit-content;
            max-width: 500px;
            padding: 10px;
            padding-top: 5px;
            position: relative;

        }

        #composer {
            transition: none !important;
            transform: none !important;
            width: 100% !important;
            padding: 0px !important;
            margin: 0px !important;
            border-radius: 0px !important;
            flex-direction: column;
        }

        .modal-content textarea {
            width: 100%;
            border: none;
            background-color: var(--background-color) !important;
            border-radius: 10px;
            padding: 10px;
            font-size: 1rem;
            width: 100% !important;
            resize: none;
            transition: none !important;
            transform: none !important;
            padding: 0px !important;
            margin: 0px !important;
            margin-top: 15px !important;
            border-radius: 0px !important;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-bottom: 5px;
        }

        .modal-header .cancel,
        .drafts {
            font-size: 1rem;
            background: none !important;
            background-color: none !important;
            color: #8b8b8b;
            cursor: pointer;
            border: none !important;
            width: unset !important;
            text-align: left !important;
            padding-left: 5px;
        }

        .postChirp:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .progress-circle {
            width: 1.5em;
            height: 1.5em;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8em;
            color: #555;
            position: relative;
            background: conic-gradient(var(--accent-color) 0deg, rgb(245, 245, 245) 0deg);
        }

        .progress-circle::before {
            content: "";
            position: absolute;
            width: 1.2em;
            height: 1.2em;
            background-color: #fff;
            border-radius: 50%;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            text-align: right;
            margin-right: 12px;
            color: #8b8b8b;
        }

        .addNewChirpToThread {
            border-radius: 50%;
            border: none;
            background-color: var(--accent-color);
            color: var(--text-color);
            padding: 0px;
            margin: 0px;
            margin-left: 12px;
            height: 1.5em;
            width: 1.5em;
            font-size: 0.8em;
            line-height: 0.8em;
        }

        .progress-text.red {
            color: red;
        }

        .red-circle {
            color: red !important;
            background: red !important;
        }

        .progress-container {
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            width: 100%;
        }

        .progress-container>div {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .errorPosting {
            background-color: red;
            padding: 5px;
            margin-top: 5px;
            border-radius: 5px;
            color: white;
        }

        .postDrafts {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: center;
            align-items: center;
        }

        .replyingTo{
            display: flex;
            text-align: left;
            background-color: var(--contrastColor);
            border-radius: 8px !important;
            width: 90%;
            margin: auto;
            cursor: unset !important;
        }
        .replyingTo>div>div{
            align-items: center;
        }
        .replyingTo>div>div>div{
            position: relative;
            align-items: center;
            top: 8px;
        }
        .replyingTo>div>div>div>pre{
            text-align: left;
            margin-top: 0px;
            padding-top: 0px;
            margin-left: 8px;
            color: #8b8b8b;
            max-width: 35vw;
        }
    </style>
</head>

<body>
    <div class="modal">
        <div class="modal-content">
            <form id="chirpForm" method="POST" action="/compose/submit.php">
                <div class="modal-header">
                    <div class="postDrafts">
                        <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
                        <button type="button" class="drafts">Replying to @b</button>
                    </div>
                    <button type="submit" class="postChirp" id="postButton">Reply</button>
                </div>
                <div id="composer">
                <div class="chirp replyingTo" id="7479">
                    <div class="chirpInfo">
                        <div><img class="userPic"
                                src="https://pbs.twimg.com/profile_images/1870208411847041024/gCkxG7eB_400x400.jpg"
                                alt="Folder :)">
                            <div>
                                <p>
                                    Folder :)

                                </p>
                                <pre>Chirp!</pre>
                            </div>
                        </div>
                    </div>
            </div>
                    <textarea name="chirpComposeText" maxlength="2500" placeholder="What's on your mind?"
                        oninput="updateCharacterCount(this)"></textarea>
                </div>
                <div class="progress-container">
                    <div>
                    </div>
                    <div>
                        <span class="progress-text" id="progressText">500</span>
                        <div class="progress-circle" id="progressCircle" data-progress="0">
                        </div>
                        <button class="addNewChirpToThread">+</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateCharacterCount(textarea) {
            const maxChars = 500;
            const remainingChars = maxChars - textarea.value.length;
            const progressCircle = document.getElementById('progressCircle');
            const progressText = document.getElementById('progressText');
            const postButton = document.getElementById('postButton');

            const progress = Math.max(0, Math.min((textarea.value.length / maxChars) * 100, 100));

            // Update progress text and color
            progressText.textContent = `${remainingChars}`;
            if (remainingChars <= 0) {
                progressText.classList.add('red');
                postButton.disabled = true;
                progressCircle.classList.add('red-circle');  // Add red-circle class when limit is exceeded
            } else {
                progressText.classList.remove('red');
                postButton.disabled = false;
                progressCircle.classList.remove('red-circle');  // Remove red-circle class when text is under limit
            }

            // Update circle progress by manipulating the border color or similar visuals
            const degree = (textarea.value.length / maxChars) * 360;
            progressCircle.style.background = `conic-gradient(var(--accent-color) ${degree}deg, #f5f5f5 ${degree}deg)`;
        }
    </script>

</body>

</html>