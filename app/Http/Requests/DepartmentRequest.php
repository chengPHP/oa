<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'GET':
                break;
            case 'DELETE':
                {
                    return [];
                }
                break;
            case 'POST':
                return [
                    'name'=>'required',
                    'status'=>'required',
                ];
                break;
            case 'PUT':
                return [
                    'name'=>'required',
                    'status'=>'required',
                ];
            case 'PATCH':
            default:break;
        }

    }

    public function messages()
    {
        return [
            'name.required'=>'请输入部门名称',
            'status.required'=>'请选择部门所属状态'
        ];
    }
}
