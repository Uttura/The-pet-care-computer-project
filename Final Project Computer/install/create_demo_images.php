<?php
// Create demo directories
$directories = [
    '../assets/images/demo/clients',
    '../assets/images/demo/pets',
    '../assets/images/demo/clinics'  // Add clinics directory
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Sample clinic images
$clinicImages = [
    'clinic1.jpg' => 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=500',
    'clinic2.jpg' => 'https://images.unsplash.com/photo-1590105577767-e21a1067899f?w=500',
    'clinic3.jpg' => 'https://images.unsplash.com/photo-1581594549595-35f6edc7b762?w=500'
];

// Sample client images
$clientImages = [
    'client1.jpg' => 'https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?w=200',
    'client2.jpg' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200'
];

// Sample pet images
$petImages = [
    'dog.jpg' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=300',
    'cat.jpg' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=300'
];

// Function to download and save image
function saveImage($url, $path) {
    $image = file_get_contents($url);
    if ($image !== false) {
        file_put_contents($path, $image);
        return true;
    }
    return false;
}

// Save clinic images
foreach ($clinicImages as $filename => $url) {
    saveImage($url, "../assets/images/demo/clinics/$filename");
}

// Save client images
foreach ($clientImages as $filename => $url) {
    saveImage($url, "../assets/images/demo/clients/$filename");
}

// Save pet images
foreach ($petImages as $filename => $url) {
    saveImage($url, "../assets/images/demo/pets/$filename");
}

echo "Demo images created successfully!"; 