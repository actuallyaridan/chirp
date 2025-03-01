
<div>
<div class="modal modalCompose" id="newChirpModal">
        <div class="modal-content modalContentCompose">
            <form id="chirpForm" method="POST" action="/compose/submit.php">
                <div class="modal-header modalHeaderCompose">
                    <div class="postDrafts postDraftsCompose">
                        <button type="button" class="cancel" onclick="closeNewChirpModal()">Cancel</button>
                        <button type="button" class="drafts">Drafts</button>
                    </div>
                    <button type="submit" class="postChirp" id="postButton">Chirp</button>
                </div>
                <div id="composer" class="composerContent">
                    <textarea name="chirpComposeText" maxlength="2500" placeholder="What's on your mind?"
                        oninput="updateCharacterCount(this)"></textarea>
                </div>
                <div class="progress-container progressContainerCompose">
                    <div>
                    </div>
                    <div>
                        <span class="progress-text progressTextCompose" id="progressText">500</span>
                        <div class="progress-circle progressCircleCompose" id="progressCircle" data-progress="0">
                        </div>
                        <button style="display: none;" class="addNewChirpToThread addNewChirpCompose">+</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>