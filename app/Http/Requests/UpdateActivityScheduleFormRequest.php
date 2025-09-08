<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\RoomAvailableRule;
use App\Rules\StartTimeRangeRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityScheduleFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'exists:activities,id'],
            'start_datetime' => [
                'required', 
                'date', 
                'after_or_equal:today',
                new StartTimeRangeRule(),
            ],
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime',
            ],
            'room_id' => [
                'required', 
                'exists:rooms,id',
                new RoomAvailableRule(),
            ],
            'max_enrollment' => ['required', 'integer', 'min:10', 'max:50'],
        ];
    }
}
