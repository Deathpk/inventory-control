<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Policies\AdminActionsPolicy;
use App\Services\Employee\EmployeesService;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeesService $service = new EmployeesService()
    ) {}

    public function index(): JsonResponse
    {
        $employees = $this->service->getEmployeeList();
        return response()->json($employees);
    }

    public function edit(UpdateEmployeeRequest $request): JsonResponse
    {
        $attributes = $request->getAttributes();
        $this->service->updateEmployee($attributes);
        return response()->json([
            'success' => true,
            'message' => 'Os dados do Colaborador foram atualizados com sucesso!'
        ]);
    }

    public function destroy(int $id, AdminActionsPolicy $policy): JsonResponse
    {
        $policy->ensureUserCanPerformAdminActions();
        $this->service->inactivateEmployee($id);
        return response()->json([
            'success' => true,
            'message' => 'O Colaborador foi inativado com sucesso, mas fique tranquilo, você pode reativá-lo futuramente.'
        ]);
    }
}