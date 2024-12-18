<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    use HasFactory;

    // Table name (optional, if table name doesn't follow conventions)
    protected $table = 'bulletins';

    // Primary key
    protected $primaryKey = 'bulletin_id';

    // Fillable fields for mass assignment
    protected $fillable = [
        'created_by',
        'date_posted',
        'category',
        'title',
        'content',
        'notification_sent',
    ];

    // Cast notification_sent as a boolean
    protected $casts = [
        'notification_sent' => 'boolean',
    ];
}
