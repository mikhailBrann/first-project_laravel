<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("admin")->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ruleArr = [
            "name" => ["required", "min:10", "max:255"],
            "status" => ["required"],
            "data" => []
        ];

        $articleFlag = false;

        //валидируем артикул только при редактировании и если он изменился
        if (isset($this->id)) {
            $productInDB = Product::find($this->id);
            $articleFlag =  mb_strtolower(trim($this->article)) != mb_strtolower(trim($productInDB->article));
        } else {
            $articleFlag = true;
        }

        if($articleFlag) {
            $ruleArr["article"] = [
                "required",
                "unique:products",
                "regex:/^[a-zA-Z0-9]+$/",
                "max:255"
            ];
        }

        return $ruleArr;
    }

    /**
     * lang to error message
     *
     * @return array
     */
    public function messages()
    {
        return [
            'article.regex' => 'Артикул должен состоять только из латинских букв и цифр',
            'article.unique' => 'Артикул с таким значением уже есть у другого товара',
            'article.required' => 'Артикул не может быть пустым',
            'article.max' => 'Артикул не может превышать 255 символов',
            'name.required' => 'Название товара не может быть пустым',
            'name.min' => 'Название товара не может быть короче 10 символов',
            'name.max' => 'Название товара не может превышать 255 символов',
        ];
    }
}
