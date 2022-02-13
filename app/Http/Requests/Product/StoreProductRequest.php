<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\Pure;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // TODO MONTAR A LOGICA DE AUTH DPS...
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'quantity' => 'required|int',
            'paidPrice' => 'required|int',
            'sellingPrice' => 'required|int',
            'categoryId' => Rule::requiredIf(!$this->getCategoryName()),
            'categoryName' => Rule::requiredIf(!$this->getCategoryId()) . '|string',
            'limitForRestock' => 'required|int'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome do produto é um campo obrigatório.',
            'name.string' => 'O nome do produto deve conter somente caracteres Alfa Numéricos.',
            'quantity.required' => 'A quantidade atual do produto é um campo obrigatório.',
            'quantity.int' => 'O campo quantidade do prroduto deve conter somente numerais.',
            'categoryId.required' => 'O campo categoria do produto é obrigatório.',
            'paidPrice.required' => 'O campo valor de custo do produto é obrigatório.',
            'paidPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'sellingPrice.required' => 'O campo valor de venda do produto é obrigatório.',
            'sellingPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'categoryId.int' => 'O campo categoria do produto deve conter somente numerais.',
            'categoryName.required' => 'O campo nome da categoria é obrigatório quando uma categoria existente não for selecionada.',
            'categoryName.string' => 'O campo nome da categoria deve conter somente caracteres Alfa Numéricos.',
            'limitForRestock.required' => 'O campo quantidade para reposição é obrigatório',
            'limitForRestock.int' => 'O campo quantidade para reposição deve conter somente numerais.'
        ];
    }

    public function getName(): string
    {
        return $this->request->get('name');
    }

    public function getQuantity(): int
    {
        return $this->request->get('quantity');
    }

    public function getCategoryId(): ?int
    {
        return $this->request->get('categoryId');
    }

    public function getPaidPrice(): int
    {
        return $this->request->get('paidPrice');
    }

    public function getSellingPrice(): int
    {
        return $this->request->get('sellingPrice');
    }

    #[Pure] public function hasCategoryId(): bool
    {
        $categoryId = $this->request->get('categoryId');
        return isset($categoryId);
    }

    public function getCategoryName(): ?string
    {
        return $this->request->get('categoryName');
    }

    public function getLimitForRestock(): int
    {
        return $this->request->get('limitForRestock');
    }
}
