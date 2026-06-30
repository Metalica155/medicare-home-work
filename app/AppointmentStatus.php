<?php

namespace App;

enum AppointmentStatus: string
{
    case Pending = 'Pending';
    case Confirmed = 'Confirmed';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
}
