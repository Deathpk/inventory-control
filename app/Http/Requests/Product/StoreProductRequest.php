<?php

namespace App\Http\Requests\Product;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['string', 'max:200'],
            'quantity' => ['required', 'int'],
            'paidPrice' => self::getPaidPriceRules(),
            'sellingPrice' => self::getSellingPriceRules(),
            'externalProductId' => self::getExternalProductIdRules(),
            'categoryId' => Rule::requiredIf(!$this->getCategoryName()),
            'categoryName' => Rule::requiredIf(!$this->getCategoryId()),
            'brandId' => Rule::requiredIf(!$this->getBrandName()),
            'brandName' => Rule::requiredIf(!$this->getBrandId()),
            'minimumQuantity' => ['required', 'int']
        ];
    }

    private static function getPaidPriceRules(): array
    {
        return [
            Rule::prohibitedIf(
                fn() => Auth::user()->getCompany()->plan_id === Plan::ESSENTIAL_PLAN
            ), 'int'
        ];
    }

    private static function getSellingPriceRules(): array
    {
        return [
            Rule::prohibitedIf(
                fn() => (Auth::user()->getCompany()->plan_id === Plan::ESSENTIAL_PLAN)
            ), 'int'
        ];
    }

    private static function getExternalProductIdRules(): array
    {
        return [
            "string", 
            Rule::unique('products', 'external_product_id')
            ->where(fn($query) => $query->where('company_id', Auth::user()->company_id))
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é um campo obrigatório.',
            'name.string' => 'O nome do produto deve conter somente caracteres Alfa Numéricos.',
            'description.string' => 'A descrição do produto deve conter somente caracteres Alfa Numéricos.',
            'description.max' => 'A descrição do produto deve conter no máximo 200 caractéres.',
            'quantity.required' => 'A quantidade atual do produto é um campo obrigatório.',
            'quantity.int' => 'O campo quantidade do prroduto deve conter somente numerais.',
            'categoryId.required' => 'O campo categoria do produto é obrigatório.',
            'paidPrice.prohibited' => 'O campo valor de custo só pode ser utilizado por empresas com plano Premium. Por favor, dê um upgrade no plano e tente novamente!',
            'paidPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'sellingPrice.prohibited' => 'O campo valor de venda só pode ser utilizado por empresas com plano Premium. Por favor, dê um upgrade no plano e tente novamente!',
            'sellingPrice.int' => 'O campo custo do produto deve ser do tipo inteiro.',
            'externalProductId.string' => 'O código de identificação externo do produto deve conter somente caracteres Alfa Numéricos.',
            'externalProductId.unique' => 'O código de identificação externo inserido já está sendo usado por outro produto',
            'categoryName.required' => 'O campo nome da categoria é obrigatório quando uma categoria existente não for selecionada.',
            'brandId.required' => 'O ID da marca do produto é obrigatório.',
            'brandName.required' => 'O campo nome da marca deve ser preenchido caso uma marca nao seja selecioanda.',
            'minimumQuantity.required' => 'O campo quantidade minima para reposição é obrigatório',
            'minimumQuantity.int' => 'O campo quantidade minima para reposição deve conter somente numerais.'
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

    public function getAttributes(): Collection
    {
        return collect($this->validated());
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

    public function getMinimumQuantity(): int
    {
        return $this->request->get('minimumQuantity');
    }
}
