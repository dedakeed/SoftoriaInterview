<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SEOApiRequest extends FormRequest
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
            'keyword' =>'required|max:200',
            'location'=>'required|integer',
            'language' => 'required',
            'domain' => 'required|in:google,bing,yahoo',
            'site' => 'required|string|regex:/^(?!https?:\/\/)(?!www\.)[a-z0-9.-]+\.[a-z]{2,}$/i',

        ];
    }
    public function messages(){
        return [
            'keyword.required' => 'Keyword is required!',
            'location.required'=>'Location is required!',
            'language.required' =>'Language is required!',
            'location.integer'=>'Location must be an integer!',
            'domain.required' => 'Domain is required!',
            'domain.in' => 'Only domains "google", "yahoo" or "bing" available',
            'site.required' =>'Website is required!',
            'site.regex' =>'Please, input a valid url. URL should not start with "https" and "www',
        ];
    }
}
