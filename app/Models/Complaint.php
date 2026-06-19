<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    //
    protected $fillable = [
        'user_id',
        'transaksi_id',
        'issue_types',
        'description',
        'image_path',
        'status',
    ];

    protected $casts = [
        'issue_types' => 'array',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
