<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Care Website</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="assets/js/main.js"></script>
</head>
<body>
    <nav>
        <div class="logo">
            <a href="?page=home" class="logo-link">
                <i class="fas fa-paw"></i>
                Pet Care
            </a>
        </div>
        <div class="menu">
            <?php if ($auth->isLoggedIn()): ?>
                <a href="?page=dashboard" class="menu-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="?page=pet-profile" class="menu-link"><i class="fas fa-paw"></i> Pets</a>
                <a href="?page=vet-finder" class="menu-link"><i class="fas fa-hospital"></i> Find Vet</a>
                <a href="?page=community" class="menu-link"><i class="fas fa-users"></i> Community</a>
                <a href="?page=account" class="menu-link"><i class="fas fa-user-cog"></i> Account</a>
                <a href="?page=logout" class="menu-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="?page=login" class="menu-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="?page=register" class="menu-link"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <main>

<style>
body {
    margin: 0;
    padding: 0;
    background: #ffffff;
    min-height: 100vh;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease;
}

.logo-link:hover {
    color: #f0f0f0;
    transform: scale(1.05);
}

.logo-link i {
    font-size: 1.8rem;
}

.menu {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #ffffff;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.menu-link i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.menu-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.menu-link:hover i {
    transform: translateX(2px);
}

main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

@media (max-width: 768px) {
    nav {
        flex-direction: column;
        padding: 1rem;
    }

    .menu {
        flex-direction: column;
        width: 100%;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .menu-link {
        width: 100%;
        justify-content: center;
    }
}
</style> 