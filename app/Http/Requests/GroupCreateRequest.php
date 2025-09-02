<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    protected function prepareForValidation()
    {
        if (is_string($this->directions)) {
            $decoded = json_decode($this->directions, true);
            $this->merge([
                'directions' => $decoded ?: []
            ]);
        }
    }

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
        $isPrivateLesson = $this->input('class') === 'private_lesson';

        return [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'directions' => 'required|array|min:1|max:5',
            'levels' => 'required|array|max:4',
            'levels.*' => 'in:beginner,starter,intermediate,advanced',
            'count_people' => 'required|integer|min:1|max:99',
            'class' => 'required|in:regular_group,course,intensive,class,private_lesson,guest_masterclass',
            'date' => $isPrivateLesson ? 'nullable|date_format:Y-m-d' : 'required|date_format:Y-m-d',
            'time' => $isPrivateLesson ? 'nullable' : 'required',
            'selected_week' => 'required_if:is_schedule,true|array|min:1|max:7',
            'price' => $isPrivateLesson ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'duration' => 'nullable|numeric|min:0',
            'address' => $isPrivateLesson ? 'nullable' : 'required',
            'preview' => 'nullable|image|mimes:jpeg,png,jpg|max:4000',
            'video_group' => 'required|max:71680',
            'date_end' => 'nullable|date_format:Y-m-d',
            'isAdult' => 'nullable',
        ];
    }
}
