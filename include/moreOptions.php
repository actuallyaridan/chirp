<div class="morePostOptionWrapper">
    <button class="morePostOptions" title="More..." onClick="openMoreOptionsModal()">
        <i class="fa-solid fa-ellipsis"></i>
    </button>
    <div class="morePostOptionsModal" id="moreOptionsModal">
        <ul>
            <?php 
            // Retrieve the post author and signed-in user
            $postAuthor = isset($user) ? htmlspecialchars($user) : 'guest';
            $signedInUser = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest';
            
            // Check if the signed-in user is the author of the post
            if ($signedInUser === $postAuthor): 
            ?>
                <li id="editPost" class="authorOnly">✏️ Edit</li>
                <li id="delete" class="authorOnly">🗑️ Delete</li>
            <?php endif; ?>
            <li id="copyPost">📋 Copy chirp</li>
            <li id="copyLink">🔗 Copy link</li>
        </ul>
    </div>
</div>
