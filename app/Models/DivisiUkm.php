<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ukm_id', 'nama'])]
class DivisiUkm extends Model
{
    protected $table = 'divisi_ukm';

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class);
    }

    public function pengurus(): HasMany
    {
        return $this->hasMany(User::class, 'divisi_ukm_id');
    }
}
