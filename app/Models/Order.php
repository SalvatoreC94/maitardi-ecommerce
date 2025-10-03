<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','user_id','status','subtotal','shipping_cost','discount_total','tax_total','total',
        'payment_method','payment_status','customer_email','customer_name','customer_phone',
        'billing_address_json','shipping_address_json','notes','placed_at'
    ];

    protected $casts = [
        'billing_address_json' => 'array',
        'shipping_address_json' => 'array',
        'placed_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
