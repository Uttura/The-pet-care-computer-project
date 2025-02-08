<?php
// Include demo data
require_once 'includes/demo_data.php';

$demoVets = [
    [
        'clinic_name' => 'Happy Paws Clinic',
        'rating' => 4.8,
        'distance' => '0.5 km',
        'image' => 'assets/images/demo/clinic1.jpg'
    ],
    [
        'clinic_name' => 'Pet Care Center',
        'rating' => 4.5,
        'distance' => '1.2 km',
        'image' => 'assets/images/demo/clinic2.jpg'
    ]
];

$demoPets = [
    [
        'name' => 'Max',
        'species' => 'Dog',
        'breed' => 'Golden Retriever',
        'image' => 'assets/images/demo/dog.jpg'
    ],
    [
        'name' => 'Luna',
        'species' => 'Cat',
        'breed' => 'Persian',
        'image' => 'assets/images/demo/cat.jpg'
    ]
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Your Pet's Health, <span class="highlight">Our Priority</span></h1>
        <p class="hero-subtitle">Seamlessly manage your pet's healthcare journey with our comprehensive pet care platform</p>
        <?php if (!$auth->isLoggedIn()): ?>
            <div class="cta-buttons">
                <a href="?page=register" class="btn btn-primary">Get Started <i class="fas fa-arrow-right"></i></a>
                <a href="?page=login" class="btn btn-outline">Sign In</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="hero-image">
        <img src="assets/images/hero.svg" alt="Happy pets" class="floating-animation">
    </div>
</div>

<div class="features-section">
    <h2 class="section-title">Everything Your Pet Needs</h2>
    <p class="section-subtitle">Discover our comprehensive suite of pet care services</p>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-hospital"></i>
            </div>
            <h3>Find Nearby Vets</h3>
            <p>Connect with trusted veterinarians in your neighborhood</p>
            
            <div class="demo-carousel">
                <?php foreach ($demoVets as $vet): ?>
                    <div class="vet-card">
                        <div class="vet-image">
                            <img src="<?php echo $vet['image']; ?>" alt="<?php echo $vet['clinic_name']; ?>">
                        </div>
                        <div class="vet-info">
                            <h4><?php echo $vet['clinic_name']; ?></h4>
                            <div class="rating">
                                <span class="stars"><?php echo str_repeat('⭐', floor($vet['rating'])); ?></span>
                                <span class="rating-number"><?php echo $vet['rating']; ?></span>
                            </div>
                            <p class="distance"><i class="fas fa-map-marker-alt"></i> <?php echo $vet['distance']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-paw"></i>
            </div>
            <h3>Pet Profiles</h3>
            <p>Keep all your pet's information organized in one place</p>
            
            <div class="pets-grid">
                <?php foreach ($demoPets as $pet): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <img src="<?php echo $pet['image']; ?>" alt="<?php echo $pet['name']; ?>">
                        </div>
                        <div class="pet-info">
                            <h4><?php echo $pet['name']; ?></h4>
                            <p class="pet-breed"><?php echo $pet['breed']; ?></p>
                            <span class="pet-species"><?php echo $pet['species']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3>Appointment Booking</h3>
            <p>Schedule vet visits with just a few clicks</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <h3>Health Records</h3>
            <p>Track vaccinations, medications, and medical history</p>
        </div>
    </div>
</div>

<div class="cta-section">
    <div class="cta-content">
        <h2>Ready to Give Your Pet the Best Care?</h2>
        <p>Join thousands of pet owners who trust us with their pet's health</p>
        <?php if (!$auth->isLoggedIn()): ?>
            <a href="?page=register" class="btn btn-primary btn-large">Get Started Now</a>
        <?php endif; ?>
    </div>
</div>

<style>
.hero-section {
    display: flex;
    align-items: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    margin: 2rem;
}

.hero-content {
    flex: 1;
    padding-right: 2rem;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.2;
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: titleGradient 8s ease infinite;
}

@keyframes titleGradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.highlight {
    color: #4A90E2;
    position: relative;
    display: inline-block;
}

.highlight::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 30%;
    bottom: 0;
    left: 0;
    background: rgba(74, 144, 226, 0.1);
    z-index: -1;
    transform: skew(-15deg);
}

.hero-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 2rem;
}

.hero-image {
    flex: 1;
    text-align: center;
}

.hero-image img {
    max-width: 100%;
    height: auto;
}

.floating-animation {
    animation: float 6s ease-in-out infinite;
    filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

.features-section {
    padding: 4rem 2rem;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-subtitle {
    text-align: center;
    color: #6c757d;
    margin-bottom: 3rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.feature-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.feature-icon {
    font-size: 2.5rem;
    color: #4A90E2;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
    color: #357ABD;
}

.vet-card, .pet-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.vet-card:hover, .pet-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.vet-image img, .pet-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.vet-info, .pet-info {
    padding: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

.rating .stars {
    color: #FFD700;
    letter-spacing: 2px;
    transition: all 0.3s ease;
}

.vet-card:hover .stars, .pet-card:hover .stars {
    transform: scale(1.1);
}

.distance {
    color: #6c757d;
}

.cta-section {
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    color: white;
    text-align: center;
    padding: 4rem 2rem;
    border-radius: 20px;
    margin: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.1) 100%);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.cta-section:hover::before {
    transform: translateX(100%);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #4A90E2;
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #357ABD;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
}

.btn-outline {
    border: 2px solid #4A90E2;
    background: white;
    color: white;
    background: #4A90E2;
}

.btn-outline:hover {
    background: #357ABD;
    border-color: #357ABD;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .hero-section {
        flex-direction: column;
        text-align: center;
        padding: 2rem 1rem;
    }

    .hero-content {
        padding-right: 0;
        margin-bottom: 2rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .features-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .cta-buttons {
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Modify dark mode support to keep white background */
@media (prefers-color-scheme: dark) {
    .feature-card, .vet-card, .pet-card {
        background: #ffffff;
        border-color: rgba(0,0,0,0.1);
    }

    .hero-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    body {
        background: #ffffff;
        color: #333333;
    }

    .hero-subtitle, .section-subtitle {
        color: #666666;
    }
}
</style> 