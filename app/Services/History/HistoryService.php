<?php

namespace App\Services\History;

use App\Models\History;
use Illuminate\Support\Collection;

class HistoryService
{
    /**
     * @throws \Throwable
     */
    public function createProductHistory(Collection $historyParams): void
    {
        $history = new History();
        $history->createChange($historyParams);
    }

    private function createMetaData(Collection $data): string
    {
        //TODO
    }
}
