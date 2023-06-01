<?php

namespace App\Services\Employee;

use App\Exceptions\AbstractException;
use App\Exceptions\Employee\FailedToListEmployees;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Models\History;
use App\Models\User;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EmployeesService
{
  use RegisterHistory;

  public function getEmployeeList(): LengthAwarePaginator
  {
    $loggedCompany = Auth::user()->getCompany();

    try {
        return $loggedCompany->getEmployeesList();
    } catch(\Throwable $e) {
      throw new FailedToListEmployees($e);
    }
  }

  public function updateEmployee(Collection $attributes): void
  {
    try {
      $employee = User::find($attributes->get('id'));
      if(!$employee) {
        throw new RecordNotFoundOnDatabaseException(AbstractException::USER_ENTITY_LABEL);
      }

      $employee->updateFromArray($attributes->toArray());
    } catch(\Throwable $e) {
      throw new FailedToUpdateEntity(AbstractException::USER_ENTITY_LABEL, $e);
    }
  }

  public function inactivateEmployee(int $id): void
  {
    try {
      $employee = User::find($id);
      if(!$employee) {
        throw new RecordNotFoundOnDatabaseException(AbstractException::USER_ENTITY_LABEL);
      }

      $employee->revokeLogedToken();
      $employee->delete();
      $this->createEmployeeInactivatedHistory($id);
    } catch(\Throwable $e) {
      throw new FailedToUpdateEntity(AbstractException::USER_ENTITY_LABEL, $e);
    }
  }

      /**
     * @throws \Throwable
     */
    private function createEmployeeInactivatedHistory(int $id): void
    {
      $historyService = new HistoryService();

      $params =  [
          'entityId' => $id,
          'entityType' => History::USER_ENTITY,
          'changedById' => self::getChangedBy(),
          'metadata' => " "
      ];
      // TODO ALTERAR PARA O ENUM CORRETO.
      $historyService->createHistory(History::USER_PASSWORD_CHANGED, $params);
    }

}
