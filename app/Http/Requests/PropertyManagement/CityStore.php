<?php

namespace App\Http\Requests\PropertyManagement;

use App\Models\User\BasicSetting;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Models\User\Property\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CityStore extends FormRequest
{
    use TenantFrontendLanguage;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $tenantId = Auth::guard('web')->user()->id;
        $country =  $this->country;
        $rules = [
            'state' => [
                Rule::requiredIf(function () use ($country, $tenantId) {
                    if ($country) {
                        $country = Country::where('user_id', $tenantId)->findOrFail($country);
                        if (count($country->states) > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    return false;
                })
            ],
            'image' => "required|mimes:jpg,png,svg,jpeg,webp",
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $basicSettings = BasicSetting::where('user_id', $tenantId)->select('property_state_status', 'property_country_status')->first();
        if ($basicSettings->property_country_status == 1) {
            $rules['country'] = 'required';
        }
        $languages = $this->allLangs($tenantId);
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_city_contents', 'name')->where('user_id', $tenantId)

                ];
        }

        return $rules;
    }
    public function messages()
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        foreach ($languages as $lan) {

            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }
        return $message;
    }
}
