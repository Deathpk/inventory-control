<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'productId' => [Rule::requiredIf(!$this->getExternalProductId()), 'int'],
            'externalProductId' => [Rule::requiredIf(!$this->getProductId())],
            'name' => 'string',
            'description' => 'string|max:200',
            'quantity' => 'int',
            'minimumQuantity' => 'int',
            'paidPrice' => 'int',
            'sellingPrice' => 'int',
            'categoryId' => 'int',
            'categoryName' => 'string',
            'brandId' => 'int',
            'brandName' => 'string',
            'limitForRestock' => 'int'
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O campo ID do produto é obrigatório.',
            'productId.int' => 'O campo ID do produto deve conter somente numerais',
            'externalProductId.required' => 'O ID externo do produto é obrigatório quando o ID do produto não estiver presente.',
            'name.string' => 'O nome do produto deve conter somente caracteres Alfa Numéricos.',
            'description.string' => 'A descrição do produto deve conter somente caracteres Alfa Numéricos.',
            'description.max' => 'A descrição do produto deve conter no máximo 200 caractéres.',
            'quantity.int' => 'O campo quantidade do prroduto deve conter somente numerais.',
            'minimumQuantity.int' => 'O campo quantidade minima em estoque deve conter somente numerais.',
            'categoryId.int' => 'O campo ID da categoria do produto deve conter somente numerais',
            'categoryName.string' => 'O nome da categoria do produto deve conter somente caracteres Alfa Numéricos.',
            'brandId.int' => 'O campo ID da marca do produto deve conter somente numerais',
            'brandName.string' => 'O nome da marca do produto deve conter somente caracteres Alfa Numéricos.',
            'paidPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'sellingPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'limitForRestock.int' => 'O campo quantidade para reposição deve conter somente numerais.'
        ];
    }

    public function getProductId(): ?int
    {
        return $this->request->get('productId', null);
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }

    public function getName(): string
    {
        return $this->request->get('name');
    }

    public function getQuantity(): int
    {
        return $this->request->get('quantity');
    }

    public function getMinimumQuantity(): int
    {
        return $this->request->get('minimumQuantity');
    }

    public function getCategoryId(): ?int
    {
        return $this->request->get('categoryId');
    }

    public function getCategoryName(): ?string
    {
        return $this->request->get('categoryName');
    }

    public function getBrandId(): ?int
    {
        return $this->request->get('brandId');
    }

    public function getBrandName(): ?string
    {
        return $this->request->get('brandName');
    }

    public function getPaidPrice(): int
    {
        return $this->request->get('paidPrice');
    }

    public function getSellingPrice(): int
    {
        return $this->request->get('sellingPrice');
    }

    public function getLimitForRestock(): int
    {
        return $this->request->get('limitForRestock');
    }

    public function getExternalProductId(): ?string
    {
        return $this->request->get('externalProductId', null);
    }
}
