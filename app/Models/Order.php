<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    public function details(){
        return $this->hasMany(Detail::class, 'order_id', 'id');
    }

    protected $casts = [
        'id' => 'string'
    ];
}
