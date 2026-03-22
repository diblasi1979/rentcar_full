<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('users')
            ->where('role', 'conductor')
            ->whereNull('driver_id')
            ->whereNotNull('email')
            ->select(['id', 'email'])
            ->get();

        foreach ($users as $user) {
            $driver = DB::table('drivers')
                ->where('email', $user->email)
                ->select('id')
                ->first();

            if (!$driver) {
                continue;
            }

            $alreadyAssigned = DB::table('users')
                ->where('driver_id', $driver->id)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($alreadyAssigned) {
                continue;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update(['driver_id' => $driver->id]);
        }
    }

    public function down(): void
    {
    }
};