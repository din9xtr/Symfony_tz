<?php
// src/Enum/Suit.php
namespace App\Enum;

enum Suit: string {
    case admin = 'ROLE_ADMIN';
    case user = 'ROLE_USER';
    case editor = 'ROLE_EDITOR';
    case guest = 'ROLE_GUEST';
}
