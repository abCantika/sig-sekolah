<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'npsn', 'nama', 'jenjang', 'status', 'alamat',
        'kecamatan', 'telepon', 'latitude', 'longitude',
    ];

    public function profil(): HasOne
    {
        return $this->hasOne(ProfilSekolah::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}