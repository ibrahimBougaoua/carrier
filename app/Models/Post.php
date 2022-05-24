<?php

namespace App\Models;

use App\Traits\Multitenancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory,Multitenancy,SoftDeletes,Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'body',
        'city_id',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'body' => $this->body,
        ];
    }

    public function scopeLatest()
    {
        return $this->orderBy('created_at', 'desc');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
