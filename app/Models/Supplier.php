<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'status',
    ];

    /**
     * Check if supplier is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope active suppliers only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get formatted contact information.
     */
    public function getContactInfoAttribute(): string
    {
        $info = [];
        
        if ($this->contact_person) {
            $info[] = $this->contact_person;
        }
        
        if ($this->email) {
            $info[] = $this->email;
        }
        
        if ($this->phone) {
            $info[] = $this->phone;
        }
        
        return implode(' | ', $info);
    }
}
