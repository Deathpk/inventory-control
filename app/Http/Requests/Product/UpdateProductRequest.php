<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
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
    public function authorize()
    {
        return true;
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
            'externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string'],
            'name' => 'required|string',
            'description' => 'string|max:200',
            'quantity' => 'required|int',
            'paidPrice' => 'required|int',
            'sellingPrice' => 'required|int',
            'categoryId' => Rule::requiredIf(!$this->getCategoryName()),
            'categoryName' => Rule::requiredIf(!$this->getCategoryId()),
            'brandId' => Rule::requiredIf(!$this->getBrandName()),
            'brandName' => Rule::requiredIf(!$this->getBrandId()),
            'limitForRestock' => 'required|int'
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O campo ID do produto é obrigatório.',
            'productId.int' => 'O campo ID do produto deve conter somente numerais',
            'name.required' => 'O nome do produto é um campo obrigatório.',
            'name.string' => 'O nome do produto deve conter somente caracteres Alfa Numéricos.',
            'description.string' => 'A descrição do produto deve conter somente caracteres Alfa Numéricos.',
            'description.max' => 'A descrição do produto deve conter no máximo 200 caractéres.',
            'quantity.required' => 'A quantidade atual do produto é um campo obrigatório.',
            'quantity.int' => 'O campo quantidade do prroduto deve conter somente numerais.',
            'categoryId.required' => 'O campo categoria do produto é obrigatório.',
            'paidPrice.required' => 'O campo valor de custo do produto é obrigatório.',
            'paidPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'sellingPrice.required' => 'O campo valor de venda do produto é obrigatório.',
            'sellingPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'externalProductId.string' => 'O código de identificação externo do produto deve conter somente caracteres Alfa Numéricos.',
            'categoryName.required' => 'O campo nome da categoria é obrigatório quando uma categoria existente não for selecionada.',
            'brandId.required' => 'O ID da marca do produto é obrigatório.',
            'brandName.required' => 'O campo nome da marca deve ser preenchido caso uma marca nao seja selecioanda.',
            'limitForRestock.required' => 'O campo quantidade para reposição é obrigatório',
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
