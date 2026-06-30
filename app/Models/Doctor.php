<?php

namespace App\Models;

use App\Expertise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'expertise'])]
class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'expertise' => Expertise::class,
        ];
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
