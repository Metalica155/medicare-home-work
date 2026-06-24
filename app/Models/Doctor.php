<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Database\Factories\DoctorFactory;

#[Fillable(['name', 'email', 'expertise'])]
class Doctor extends Model
{
     /** @use HasFactory<DoctorFactory> */
    use HasFactory;
}
