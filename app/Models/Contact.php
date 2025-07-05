<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'profile_image',
        'additional_file',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the contact.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the custom field values for this contact.
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    /**
     * Get the profile image URL.
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return asset('images/default-profile.png');
    }

    /**
     * Get the additional file URL.
     */
    public function getAdditionalFileUrlAttribute()
    {
        if ($this->additional_file) {
            return asset('storage/' . $this->additional_file);
        }
        return null;
    }

    /**
     * Get the additional file name.
     */
    public function getAdditionalFileNameAttribute()
    {
        if ($this->additional_file) {
            return basename($this->additional_file);
        }
        return null;
    }

    /**
     * Scope to filter by search term.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by gender.
     */
    public function scopeGender($query, $gender)
    {
        if ($gender && $gender !== 'all') {
            return $query->where('gender', $gender);
        }
        return $query;
    }


    /**
     * Contacts that were merged into this master contact.
     */
    public function mergedContacts(): HasMany
    {
        return $this->hasMany(ContactMerge::class, 'master_contact_id');
    }

    /**
     * The master contact this contact was merged into (if any).
     */
    public function mergedInto()
    {
        return $this->hasOne(ContactMerge::class, 'merged_contact_id');
    }


}
