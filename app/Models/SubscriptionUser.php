<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class SubscriptionUser extends Pivot
{
    /**
     * Accessor to format start_date as for example"4 de julio de 2022".
     *
     * @return string|null
     */
    public function getStartDateFormattedAttribute(): ?string
    {
        return $this->start_date
            ? Carbon::parse($this->start_date)->translatedFormat('j \\d\\e F \\d\\e Y')
            : null;
    }

    /**
     * Accessor to format end_date as for example"4 de julio de 2022".
     *
     * @return string|null
     */
    public function getEndDateFormattedAttribute(): ?string
    {
        return $this->end_date
            ? Carbon::parse($this->end_date)->translatedFormat('j \\d\\e F \\d\\e Y')
            : null;
    }
}
