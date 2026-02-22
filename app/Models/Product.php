<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'stock',
    ];

    // Relasi: Produk ini dipakai sebagai syarat di promo apa saja?
    public function promosAsRequirement()
    {
        return $this->hasMany(Promo::class, 'buy_product_id');
    }

    // Relasi: Produk ini dipakai sebagai hadiah di promo apa saja?
    public function promosAsReward()
    {
        return $this->hasMany(Promo::class, 'free_product_id');
    }
}
