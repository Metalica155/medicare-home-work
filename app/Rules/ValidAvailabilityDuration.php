<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;


class ValidAvailabilityDuration implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startsAt = Carbon::parse($this->data['starts_at']);
        $endsAt = Carbon::parse($value);
        $slotDuration = (int) $this->data['slot_duration'];

        $duration = $startsAt->diffInMinutes($endsAt, false);

        if ($duration < $slotDuration) {
            $fail("The availability must be at least {$slotDuration} minutes long.");
            return;
        }

        if ($duration % $slotDuration !== 0) {
            $fail("The availability duration must be a multiple of {$slotDuration} minutes.");
        }
    }
}
