<?php
// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ?page=login');
    exit();
}

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Get user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Store full name in session if not already set
    if (!isset($_SESSION['full_name'])) {
        $_SESSION['full_name'] = $user['full_name'];
    }

    // Get user's pets
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pets = $stmt->fetchAll();

    // Get upcoming appointments
    $stmt = $pdo->prepare("
        SELECT a.*, p.name as pet_name, v.clinic_name 
        FROM appointments a 
        JOIN pets p ON a.pet_id = p.pet_id 
        JOIN vets v ON a.vet_id = v.vet_id 
        WHERE p.user_id = ? AND a.status = 'scheduled'
        ORDER BY a.appointment_date ASC 
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();

} catch (PDOException $e) {
    // If tables don't exist, show a message to run setup
    if ($e->getCode() == '42S02') { // Table doesn't exist error code
        echo "<div class='alert alert-warning'>
                Please run the setup script first: 
                <a href='install/setup.php'>Run Setup</a>
              </div>";
        exit();
    }
    // For other database errors
    echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
    error_log($e->getMessage());
}
?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?>!</h2>
    
    <div class="dashboard-grid">
        <!-- Pets Summary -->
        <div class="dashboard-card">
            <div class="pets-header">
                <h3><i class="fas fa-paw"></i> Your Pets</h3>
                <button class="btn btn-primary btn-sm" onclick="showAddPetForm()">
                    <i class="fas fa-plus"></i> Add Pet
                </button>
            </div>
            <?php if (empty($pets)): ?>
                <p>No pets added yet.</p>
            <?php else: ?>
                <div class="pets-grid">
                    <?php foreach ($pets as $pet): ?>
                        <div class="pet-card">
                            <img src="<?php 
                                // Use demo images based on pet species
                                if (!empty($pet['profile_image'])) {
                                    echo htmlspecialchars($pet['profile_image']);
                                } else {
                                    echo 'assets/images/demo/pets/' . 
                                        (strtolower($pet['species']) === 'cat' ? 'cat.jpg' : 'dog.jpg');
                                }
                                ?>" 
                                alt="<?php echo htmlspecialchars($pet['name']); ?>"
                                class="pet-image">
                            <h4><?php echo htmlspecialchars($pet['name']); ?></h4>
                            <p><?php echo htmlspecialchars($pet['species']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Upcoming Appointments -->
        <div class="dashboard-card">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Appointments</h3>
            <?php if (empty($appointments)): ?>
                <p>No upcoming appointments.</p>
            <?php else: ?>
                <div class="appointments-list">
                    <?php foreach ($appointments as $apt): ?>
                        <div class="appointment-item">
                            <div class="appointment-date">
                                <?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?>
                            </div>
                            <div class="appointment-details">
                                <h4><?php echo htmlspecialchars($apt['pet_name']); ?></h4>
                                <p><?php echo htmlspecialchars($apt['clinic_name']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Replace the existing vet finder card with this -->
        <div class="dashboard-card vet-finder-card">
            <div class="vet-finder-header">
                <i class="fas fa-hospital"></i>
                <h3>Find Nearby Vets</h3>
                <p>Connect with trusted veterinarians in your neighborhood</p>
            </div>
            
            <div class="vet-suggestions">
                <?php
                try {
                    // Get nearby vets (limit to 2 for dashboard preview)
                    $stmt = $pdo->prepare("
                        SELECT clinic_name, rating
                        FROM vets 
                        ORDER BY RAND() 
                        LIMIT 2
                    ");
                    $stmt->execute();
                    $suggestedVets = $stmt->fetchAll();

                    if (!empty($suggestedVets)): 
                        foreach ($suggestedVets as $index => $vet): 
                            $distance = $index === 0 ? '0.5 km' : '1.2 km';
                            $rating = $index === 0 ? '4.8' : '4.5';
                            ?>
                            <div class="vet-suggestion-box">
                                <img src="assets/images/demo/clinics/clinic<?php echo $index + 1; ?>.jpg" 
                                     alt="<?php echo htmlspecialchars($vet['clinic_name']); ?>" 
                                     class="vet-image">
                                <div class="vet-info">
                                    <h4><?php echo htmlspecialchars($vet['clinic_name']); ?></h4>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                        <span class="rating-number"><?php echo $rating; ?></span>
                                    </div>
                                    <p class="distance">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo $distance; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif;
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    echo "<p class='error'>Unable to load vet suggestions.</p>";
                } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="addPetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Pet</h3>
            <button type="button" class="close-btn" onclick="closeAddPetModal()">&times;</button>
        </div>
        <form id="addPetForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <div class="pet-photo-upload">
                    <div class="photo-preview" id="photoPreview">
                        <i class="fas fa-paw"></i>
                        <span>Click to add photo</span>
                    </div>
                    <input type="file" id="petPhoto" name="photo" accept="image/*" onchange="previewPetPhoto(this)" required>
                </div>
            </div>
            <div class="form-group">
                <label for="petName">Pet Name</label>
                <input type="text" id="petName" name="name" required>
            </div>
            <div class="form-group">
                <label for="petSpecies">Species</label>
                <select id="petSpecies" name="species" required>
                    <option value="">Select species</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                </select>
            </div>
            <div class="form-group">
                <label for="petBreed">Breed</label>
                <input type="text" id="petBreed" name="breed" required>
            </div>
            <div class="form-group">
                <label for="petAge">Age (years)</label>
                <input type="number" id="petAge" name="age" min="0" max="30" step="0.1" required>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-plus"></i> Add Pet
            </button>
        </form>
    </div>
</div>

<style>
.dashboard-container {
    padding: 20px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dashboard-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-card h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #2ecc71;
    margin-bottom: 20px;
}

.pets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
}

.pet-card {
    text-align: center;
}

.pet-card img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.appointment-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px;
    border-radius: 8px;
    background: #f8f9fa;
}

.appointment-date {
    background: #2ecc71;
    color: white;
    padding: 8px;
    border-radius: 6px;
    font-size: 0.9em;
    text-align: center;
}

.appointment-details h4 {
    margin: 0;
    color: #333;
}

.appointment-details p {
    margin: 5px 0 0;
    color: #666;
    font-size: 0.9em;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.btn {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #2ecc71;
    color: white;
}

.btn-primary:hover {
    background: #27ae60;
}

.btn-sm {
    font-size: 0.9em;
    padding: 5px 10px;
}

.vet-finder-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.vet-finder-header {
    margin-bottom: 20px;
}

.vet-finder-header i {
    color: #4A90E2;
    font-size: 24px;
    margin-bottom: 10px;
}

.vet-finder-header h3 {
    color: #333;
    font-size: 24px;
    margin: 10px 0;
}

.vet-finder-header p {
    color: #666;
    margin: 0;
    font-size: 16px;
}

.vet-suggestions {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.vet-suggestion-box {
    background: white;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.vet-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

.vet-info h4 {
    color: #333;
    font-size: 18px;
    margin: 0 0 10px 0;
}

.rating {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 8px;
}

.rating .fa-star {
    color: #FFD700;
    font-size: 16px;
}

.rating-number {
    color: #333;
    margin-left: 4px;
    font-weight: 500;
}

.distance {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
    margin: 0;
}

.distance i {
    color: #666;
}

@media (max-width: 768px) {
    .vet-finder-card {
        padding: 15px;
    }

    .vet-image {
        height: 120px;
    }
}

.pets-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

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
    max-width: 400px;
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

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #666;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.pet-photo-upload {
    text-align: center;
    margin-bottom: 20px;
}

.photo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f1f1f1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin: 0 auto;
    overflow: hidden;
}

.photo-preview i {
    font-size: 24px;
    color: #666;
    margin-bottom: 5px;
}

.photo-preview span {
    font-size: 12px;
    color: #666;
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.btn-submit {
    width: 100%;
    padding: 10px;
    background: #2ecc71;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #27ae60;
}

#petPhoto {
    display: none;
}
</style>

<script>
// Add Pet functionality
function showAddPetForm() {
    document.getElementById('addPetModal').style.display = 'flex';
}

function closeAddPetModal() {
    document.getElementById('addPetModal').style.display = 'none';
    document.getElementById('addPetForm').reset();
    document.getElementById('photoPreview').innerHTML = `
        <i class="fas fa-paw"></i>
        <span>Click to add photo</span>
    `;
}

function previewPetPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Pet photo preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Make the preview div trigger file input
document.getElementById('photoPreview').addEventListener('click', function() {
    document.getElementById('petPhoto').click();
});

// Handle form submission
document.getElementById('addPetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    fetch('api/add_pet.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding pet');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add Pet';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding pet');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add Pet';
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addPetModal');
    if (event.target === modal) {
        closeAddPetModal();
    }
};
</script> 