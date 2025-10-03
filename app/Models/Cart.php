<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string'; // perché UUID

    protected $fillable = ['id','user_id','session_id','expires_at'];

    public function items() { return $this->hasMany(CartItem::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
