<?php
// src/Enum/Status.php
namespace App\Enum;

enum Status: string {
    case approved = 'approved';
    case rejected = 'rejected';
    case pending = 'pending';
}
