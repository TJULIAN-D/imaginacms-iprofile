<?php

namespace Modules\Iprofile\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateInformationRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
        ];
    }

    public function translationRules()
    {
        return [
            'title' => 'required|min:5',
            'description' => 'required|min:5',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [];
    }

    public function getValidator(){
        return $this->getValidatorInstance();
    }
}
