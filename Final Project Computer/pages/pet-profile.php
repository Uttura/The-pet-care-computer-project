<?php
// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ?page=login');
    exit();
}

try {
    // Initialize database connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Get pet ID from URL if it exists
    $pet_id = $_GET['id'] ?? null;
    $pet = null;

    if ($pet_id) {
        // Get specific pet details
        $stmt = $pdo->prepare("SELECT * FROM pets WHERE pet_id = ? AND user_id = ?");
        $stmt->execute([$pet_id, $_SESSION['user_id']]);
        $pet = $stmt->fetch();

        if (!$pet) {
            echo "<div class='alert alert-warning'>Pet not found.</div>";
        }
    }

    // Get all user's pets
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pets = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
    error_log($e->getMessage());
}
?>

<div class="pet-profile-container">
    <?php if ($pet): ?>
        <!-- Show specific pet profile -->
        <div class="pet-details">
            <h2><?php echo htmlspecialchars($pet['name']); ?>'s Profile</h2>
            <div class="pet-info">
                <img src="<?php echo $pet['profile_image'] ?? 'assets/images/default-pet.png'; ?>" 
                     alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                     class="pet-image">
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name:</label>
                        <span><?php echo htmlspecialchars($pet['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Species:</label>
                        <span><?php echo htmlspecialchars($pet['species']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Breed:</label>
                        <span><?php echo htmlspecialchars($pet['breed'] ?? 'Not specified'); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Birth Date:</label>
                        <span><?php echo $pet['birth_date'] ? date('M d, Y', strtotime($pet['birth_date'])) : 'Not specified'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Show pet list or add new pet form -->
        <div class="pets-overview">
            <h2>Your Pets</h2>
            
            <?php if (empty($pets)): ?>
                <p class="no-pets">You haven't added any pets yet.</p>
            <?php else: ?>
                <div class="pets-grid">
                    <?php foreach ($pets as $pet): ?>
                        <div class="pet-card">
                            <img src="<?php echo $pet['profile_image'] ?? 'assets/images/default-pet.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($pet['name']); ?>">
                            <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <p><?php echo htmlspecialchars($pet['species']); ?></p>
                            <a href="?page=pet-profile&id=<?php echo $pet['pet_id']; ?>" 
                               class="btn btn-view">View Profile</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button class="btn btn-add" onclick="showAddPetForm()">
                <i class="fas fa-plus"></i> Add New Pet
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
.pet-profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.pet-details {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pet-info {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.pet-image {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.info-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-weight: bold;
    color: #666;
    margin-bottom: 5px;
}

.pets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.pet-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.pet-card:hover {
    transform: translateY(-5px);
}

.pet-card img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}

.btn {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s ease;
}

.btn-view {
    background: #2ecc71;
    color: white;
}

.btn-add {
    background: #3498db;
    color: white;
    margin-top: 20px;
}

.btn:hover {
    opacity: 0.9;
}

.no-pets {
    text-align: center;
    color: #666;
    margin: 40px 0;
}

@media (max-width: 768px) {
    .pet-info {
        flex-direction: column;
    }
    
    .pet-image {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }
}
</style>

<script>
function showAddPetForm() {
    // Implement the add pet form functionality
    alert('Add pet form will be implemented soon!');
}
</script> 