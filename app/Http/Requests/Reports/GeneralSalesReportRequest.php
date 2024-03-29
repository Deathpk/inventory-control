<?php

namespace App\Http\Requests\Reports;

use App\Policies\FinancialModulePolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GeneralSalesReportRequest extends FormRequest
{
    const WEEKLY_TYPE_FILTER = 'weekly';
    const MONTLY_TYPE_FILTER = 'monthly';
    const YEARLY_TYPE_FILTER = 'yearly';

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
        return Auth::check() && FinancialModulePolicy::canAccessFinancialModule();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filterType' => [
                'required',
                Rule::in(self::getAllAvailableFilterTypes())
            ],
            'value' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'filterType.required' => 'O tipo de filtro para o relatório é obrigatório',
            'value.required' => 'O valor do filtro é obrigatório'
        ];
    }

    public function getFilterByType(): string
    {
        return $this->query('filterType');
    }

    public function getFilterValue(): string
    {
        return $this->query('value');
    }

    public function getAllAvailableFilterTypes(): array
    {
        return [
            self::WEEKLY_TYPE_FILTER,
            self::MONTLY_TYPE_FILTER,
            self::YEARLY_TYPE_FILTER
        ];
    }
}
