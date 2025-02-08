<div class="modal" id="createPostModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Post</h3>
            <button type="button" class="close-btn" onclick="closePostModal()">&times;</button>
        </div>
        <form id="postForm" method="POST" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <div class="form-actions">
                <div class="image-upload">
                    <label for="image">
                        <i class="fas fa-image"></i> Add Image
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                </div>
                <div id="imagePreview"></div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Post
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}

#postForm textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: vertical;
}

.image-upload input[type="file"] {
    display: none;
}

.image-upload label {
    display: inline-block;
    padding: 8px 15px;
    background: #f1f1f1;
    border-radius: 5px;
    cursor: pointer;
}

#imagePreview img {
    max-width: 100%;
    max-height: 200px;
    margin-top: 10px;
    border-radius: 5px;
}

.btn-submit {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-submit:hover {
    background: #27ae60;
}
</style> 