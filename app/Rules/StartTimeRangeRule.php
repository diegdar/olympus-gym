<?php
declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Enums\OperationHours;
use Carbon\Carbon;

class StartTimeRangeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
            $time = Carbon::parse($value);

            if (
                $time->hour < OperationHours::START_HOUR->value 
                || $time->hour > OperationHours::END_HOUR->value
            ) {
                $fail('La hora seleccionada debe estar entre las ' . sprintf('%02d:00', OperationHours::START_HOUR->value) . ' y las ' . sprintf('%02d:00', OperationHours::END_HOUR->value) . '.');
            }
    }
}
