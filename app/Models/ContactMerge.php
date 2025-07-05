<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMerge extends Model
{
    protected $fillable = [
        'master_contact_id',
        'merged_contact_id',
    ];

    public function masterContact()
    {
        return $this->belongsTo(Contact::class, 'master_contact_id');
    }

    public function mergedContact()
    {
        return $this->belongsTo(Contact::class, 'merged_contact_id');
    }
}
