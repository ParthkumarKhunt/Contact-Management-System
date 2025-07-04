<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomFields extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'custom_fields';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $softDelete = true;

    protected $fillable = [
        'name',
        'label',
        'type',
        'options',
        'active'
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
