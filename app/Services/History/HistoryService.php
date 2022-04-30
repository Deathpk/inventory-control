<?php

namespace App\Services\History;

use App\Models\History;

class HistoryService
{
    public function createHistory(int $actionId, array $params): void
    {
        $history = new History();
        $history->createChange($actionId, $params);
        $history->save();
    }
}
