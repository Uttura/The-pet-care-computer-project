<?php
// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ?page=login');
    exit();
}

try {
    // Get community posts
    $stmt = $pdo->prepare("
        SELECT p.*, u.full_name, u.profile_image as user_image 
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $posts = [];
}
?>

<div class="community-container">
    <?php include 'pages/includes/create_post_form.php'; ?>
    <div class="community-header">
        <h2>Pet Community</h2>
        <button class="btn btn-create" onclick="createPost()">
            <i class="fas fa-plus"></i> Create Post
        </button>
    </div>

    <div class="posts-container">
        <?php if (empty($posts)): ?>
            <div class="no-posts">
                <i class="fas fa-paw"></i>
                <p>No posts yet. Be the first to share!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card" data-post-id="<?php echo $post['post_id']; ?>">
                    <div class="post-header">
                        <img src="<?php echo $post['user_image'] ?? 'assets/images/default-user.png'; ?>" 
                             alt="Profile" 
                             class="user-avatar">
                        <div class="post-info">
                            <h3><?php echo htmlspecialchars($post['full_name']); ?></h3>
                            <span class="post-date">
                                <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($post['image']): ?>
                        <img src="<?php echo $post['image']; ?>" 
                             alt="Post image" 
                             class="post-image">
                    <?php endif; ?>

                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>

                    <div class="post-actions">
                        <button class="btn-like" onclick="likePost(<?php echo $post['post_id']; ?>)">
                            <i class="far fa-heart"></i> Like
                        </button>
                        <button class="btn-comment" onclick="showCommentForm(<?php echo $post['post_id']; ?>)">
                            <i class="far fa-comment"></i> Comment
                        </button>
                        <button class="btn-share" onclick="sharePost(<?php echo $post['post_id']; ?>)">
                            <i class="far fa-share-square"></i> Share
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.community-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.community-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.btn-create {
    background: #2ecc71;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s ease;
}

.btn-create:hover {
    background: #27ae60;
}

.post-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.post-info h3 {
    margin: 0;
    font-size: 1.1rem;
}

.post-date {
    color: #666;
    font-size: 0.9rem;
}

.post-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin: 10px 0;
}

.post-content {
    margin: 15px 0;
    line-height: 1.6;
}

.post-actions {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.post-actions button {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: color 0.3s ease;
}

.post-actions button:hover {
    color: #2ecc71;
}

.no-posts {
    text-align: center;
    padding: 50px 0;
    color: #666;
}

.no-posts i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .community-container {
        padding: 10px;
    }

    .post-actions {
        flex-wrap: wrap;
    }
}
</style>

<script>
function showCreatePostForm() {
    // Implement post creation functionality
    alert('Create post feature coming soon!');
}

function likePost(postId) {
    // Implement like functionality
    alert('Like feature coming soon!');
}

function showCommentForm(postId) {
    // Implement comment functionality
    alert('Comment feature coming soon!');
}

function sharePost(postId) {
    // Implement share functionality
    alert('Share feature coming soon!');
}
</script> 