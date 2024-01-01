<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
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
     * @return arrayz
     */
    public function rules()
    {
        return [
            'userName' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'agree' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'userName.required' => 'Tên người dùng là bắt buộc.',
            'email.required' => 'Trường email là bắt buộc.',
            'email.email' => 'Trường email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];
    }
}
