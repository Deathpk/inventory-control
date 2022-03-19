<?php


namespace Tests\Traits;


use Illuminate\Support\Facades\DB;

trait HasDatabaseActions
{
    public function assertDbCount(string $table, int $count): bool
    {
        return DB::table($table)
                ->where('deleted_at', '=', null)
                ->count() === $count;
    }
}
