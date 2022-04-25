<?php

namespace App\Services\History;

use App\Models\History;

class HistoryService
{
    /**
     * @throws \Throwable
     */
    public function createProductHistory(array $historyParams): void
    {
        $history = new History();
        $history->createChange($historyParams);
    }
}
