<!-- If you have a navigation menu -->
<nav class="navbar">
    <div class="nav-brand">
        <a href="index.php" class="logo">
            <i class="fas fa-paw"></i> Pet Care
        </a>
    </div>
    
    <div class="nav-links">
        <?php if ($auth->isLoggedIn()): ?>
            <a href="?page=dashboard" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="?page=pet-profile" class="nav-link">
                <i class="fas fa-paw"></i> Pets
            </a>
            <a href="?page=vet-finder" class="nav-link">
                <i class="fas fa-user-md"></i> Find Vets
            </a>
            <a href="?page=community" class="nav-link">
                <i class="fas fa-users"></i> Community
            </a>
            <a href="?page=account" class="nav-link">
                <i class="fas fa-user"></i> Account
            </a>
            <a href="?page=logout" class="nav-link logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <a href="?page=login" class="nav-link">Login</a>
            <a href="?page=register" class="nav-link">Register</a>
        <?php endif; ?>
    </div>
</nav>

<style>
.navbar {
    background: #fff;
    padding: 1rem 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand .logo {
    color: #2ecc71;
    font-size: 1.5rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 20px;
}

.nav-link {
    color: #666;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 12px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: #f8f9fa;
    color: #2ecc71;
}

.logout-btn {
    color: #dc3545;
}

.logout-btn:hover {
    background: #dc354520;
}

@media (max-width: 768px) {
    .navbar {
        padding: 1rem;
        flex-direction: column;
        gap: 10px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav-link {
        font-size: 14px;
    }
}
</style> 