// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Post creation form
    window.showCreatePostForm = function() {
        const formHTML = `
            <div class="modal" id="createPostModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Create New Post</h3>
                        <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
                    </div>
                    <form id="createPostForm" onsubmit="submitPost(event)">
                        <textarea 
                            name="content" 
                            placeholder="What's on your mind?"
                            required
                        ></textarea>
                        <div class="image-upload">
                            <label for="postImage">
                                <i class="fas fa-image"></i> Add Image
                            </label>
                            <input 
                                type="file" 
                                id="postImage" 
                                name="image" 
                                accept="image/*"
                                onchange="previewImage(this)"
                            >
                        </div>
                        <div id="imagePreview"></div>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Post
                        </button>
                    </form>
                </div>
            </div>
        `;
        
        // Add styles if not already added
        if (!document.getElementById('community-styles')) {
            const styles = `
                .modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
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
                    font-size: 1.5rem;
                    cursor: pointer;
                }

                #createPostForm textarea {
                    width: 100%;
                    min-height: 100px;
                    padding: 10px;
                    margin-bottom: 15px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    resize: vertical;
                }

                .image-upload {
                    margin-bottom: 15px;
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

                .preview-container {
                    position: relative;
                    margin-bottom: 15px;
                }

                .preview-container img {
                    max-width: 100%;
                    max-height: 200px;
                    border-radius: 5px;
                }

                .preview-container button {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    background: rgba(0,0,0,0.5);
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 25px;
                    height: 25px;
                    cursor: pointer;
                }

                .btn-submit {
                    background: #2ecc71;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .btn-submit:hover {
                    background: #27ae60;
                }
            `;
            const styleSheet = document.createElement("style");
            styleSheet.id = 'community-styles';
            styleSheet.textContent = styles;
            document.head.appendChild(styleSheet);
        }

        document.body.insertAdjacentHTML('beforeend', formHTML);
    };

    // Close modal
    window.closeModal = function() {
        const modal = document.getElementById('createPostModal');
        if (modal) {
            modal.remove();
        }
    };

    // Image preview
    window.previewImage = function(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="preview-container">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Remove preview image
    window.removeImage = function() {
        const input = document.getElementById('postImage');
        const preview = document.getElementById('imagePreview');
        input.value = '';
        preview.innerHTML = '';
    };

    // Submit post
    window.submitPost = function(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
        submitBtn.disabled = true;

        fetch('api/create_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error creating post');
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating post. Please try again.');
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    };

    // Like post
    window.likePost = function(postId) {
        fetch(`api/like_post.php?post_id=${postId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update like button UI
                const likeBtn = document.querySelector(`[data-post-id="${postId}"] .btn-like`);
                if (likeBtn) {
                    likeBtn.innerHTML = data.liked ? 
                        '<i class="fas fa-heart"></i> Liked' : 
                        '<i class="far fa-heart"></i> Like';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Comment functionality
    window.showCommentForm = function(postId) {
        const post = document.querySelector(`[data-post-id="${postId}"]`);
        if (!post) return;

        const existingForm = post.querySelector('.comment-form');
        if (existingForm) {
            existingForm.remove();
            return;
        }

        const formHTML = `
            <div class="comment-form">
                <textarea placeholder="Write a comment..."></textarea>
                <button onclick="submitComment(${postId})">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        `;
        post.querySelector('.post-actions').insertAdjacentHTML('afterend', formHTML);
    };

    // Submit comment
    window.submitComment = function(postId) {
        const post = document.querySelector(`[data-post-id="${postId}"]`);
        const textarea = post.querySelector('.comment-form textarea');
        const content = textarea.value.trim();

        if (!content) return;

        fetch('api/add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                post_id: postId,
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Share post
    window.sharePost = function(postId) {
        if (navigator.share) {
            fetch(`api/get_post.php?post_id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    navigator.share({
                        title: 'Pet Care Community Post',
                        text: data.content,
                        url: window.location.href
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            alert('Sharing is not supported on this browser');
        }
    };

    // Make functions globally accessible
    window.createPost = function() {
        const modal = document.getElementById('createPostModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    };

    window.closePostModal = function() {
        const modal = document.getElementById('createPostModal');
        if (modal) {
            modal.style.display = 'none';
            document.getElementById('postForm').reset();
            document.getElementById('imagePreview').innerHTML = '';
        }
    };

    window.previewImage = function(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Handle post form submission
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';

            fetch('api/create_post.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error creating post');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Post';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating post');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Post';
            });
        });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('createPostModal');
        if (event.target === modal) {
            closePostModal();
        }
    };
}); 