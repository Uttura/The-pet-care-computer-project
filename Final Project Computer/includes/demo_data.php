<?php
// Demo data for clients and pets
$demoClients = [
    [
        'id' => 1,
        'name' => 'John Doe',
        'image' => 'assets/images/demo/client1.jpg',
        'pets' => ['Max', 'Luna']
    ],
    [
        'id' => 2,
        'name' => 'Jane Smith',
        'image' => 'assets/images/demo/client2.jpg',
        'pets' => ['Bella']
    ]
];

$demoPets = [
    [
        'id' => 1,
        'name' => 'Max',
        'species' => 'Dog',
        'breed' => 'Golden Retriever',
        'image' => 'assets/images/demo/dog.jpg',
        'owner_id' => 1
    ],
    [
        'id' => 2,
        'name' => 'Luna',
        'species' => 'Cat',
        'breed' => 'Persian',
        'image' => 'assets/images/demo/cat.jpg',
        'owner_id' => 1
    ]
]; 