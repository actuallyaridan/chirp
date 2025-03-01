<div>
    <div class="modal modalCompose" id="replyChirpModal">
        <div class="modal-content modalContentCompose">
            <form id="replyForm" method="POST" action="/chirp/submit.php?id=<?php echo htmlspecialchars($postId); ?>">
                <div class="modal-header modalHeaderCompose">
                    <div class="postDrafts postDraftsCompose">
                        <button type="button" class="cancel" onclick="closeReplyModal()">Cancel</button>
                        <!--User name-->
                        <button type="button" class="drafts">Replying to @<?php echo isset($user) ? htmlspecialchars($user) : 'guest'; ?></button>
                    </div>
                    <button type="submit" class="postChirp" id="replyButton">Reply</button>
                </div>
                <div id="composerReply" class="composerContent">
                    <div class="chirp replyingTo">
                        <div class="chirpInfo">
                            <!--Profile pic-->
                            <div> 
                                <img class="userPic"
                                    src="<?php echo isset($profilePic) ? $profilePic : '/src/images/users/guest/user.svg'; ?>"
                                    alt="<?php echo isset($user) ? htmlspecialchars($user) : 'Guest'; ?>">
                                <div>
                                    <!--Display name-->
                                    <p><?php echo isset($name) ? $name : 'Guest'; ?></p>
                                    <pre><?php echo $status; ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <textarea name="replyComposeText" maxlength="2500" placeholder="What's on your mind?"
                        oninput="updateCharacterCountReply(this)"></textarea>
                    <div class="progress-container">
                        <div>
                        </div>
                        <div>
                            <span class="progress-text" id="progressTextReply">500</span>
                            <div class="progress-circle" id="progressCircleReply" data-progress="0">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
