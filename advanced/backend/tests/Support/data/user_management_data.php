<?php

/** Test-only accounts for administrator account management. Maintainer: A. */

return [
    [
        'id' => 1,
        'username' => 'site-admin',
        'email' => 'site-admin@example.test',
        'auth_key' => 'site-admin-test-auth-key',
        'password_hash' => '$2y$13$nJ1WDlBaGcbCdbNC.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
        'status' => 10,
        'role' => 'admin',
        'created_at' => 1700000000,
        'updated_at' => 1700000000,
    ],
    [
        'id' => 2,
        'username' => 'site-member',
        'email' => 'site-member@example.test',
        'auth_key' => 'site-member-test-auth-key',
        'password_hash' => '$2y$13$nJ1WDlBaGcbCdbNC.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
        'status' => 10,
        'role' => 'member',
        'created_at' => 1700000001,
        'updated_at' => 1700000001,
    ],
];
