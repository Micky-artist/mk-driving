<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define the legacy users data
        $legacyUsers = [
            [
                'id' => 'cmhdrcu710000g7299qzfc5uu',
                'email' => 'admin@mkscholars.com',
                'password' => '$2b$10$481dVHy2YZfr/V5R6L2LAOJvI1zMZ5a.hIcxQWG0vVLejwx19QF7y',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => '2025-10-30 18:28:38',
                'updated_at' => '2025-10-30 18:28:38',
            ],
            [
                'id' => 'cmhoq6qxv0000hk1qck2nlrgc',
                'email' => 'ikuzweyvette36@gmail.com',
                'password' => '$2b$10$WVAFRE6IaHxOSrvIxR6U9eWJxEq/KnIYUEEOtQ2AtKWTCvRomQE5G',
                'first_name' => 'kalisa',
                'last_name' => 'MBanda',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-07 10:41:22',
                'updated_at' => '2025-11-07 10:41:22',
            ],
            [
                'id' => 'cmhsxomsb0000fh1qc7b7t50l',
                'email' => 'rusangizaandre@gmail.com',
                'password' => '$2b$10$c3dbOzLJkmBPk0CNQwgP2upOV7sC4UHJ6cB7q6J2p.5AraR.kp3WO',
                'first_name' => 'Andre',
                'last_name' => 'RUSANGIZA',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-10 09:22:19',
                'updated_at' => '2025-11-10 09:24:26',
            ],
            [
                'id' => 'cmhud41yb0000c71qkm607sap',
                'email' => 'kagabop95@gmail.com',
                'password' => '$2b$10$aEYNDTRP1/HNmBpcG.i5Ce.mxDFxf4XfZu6THe8o6W4f6aF3XgcGa',
                'first_name' => 'KAGABO',
                'last_name' => 'Jean Paul',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-11 09:21:59',
                'updated_at' => '2025-11-11 09:21:59',
            ],
            [
                'id' => 'cmi0mqtgh0000ff1qkjloiwtz',
                'email' => 'imanishimweclaude57@gmail.com',
                'password' => '$2b$10$nTf50a772PZyE6P7Cya3XeBFY56N/MjlfdRHl96mDzqzD8v3BhlNS',
                'first_name' => 'Claude',
                'last_name' => 'IMANISHIMWE',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-15 18:38:14',
                'updated_at' => '2025-11-15 18:38:14',
            ],
            [
                'id' => 'cmi62dxuc0000f61qkjplwz6k',
                'email' => 'niyigenacedick@gmail.com',
                'password' => '$2b$10$.SE9chGs0bnzG1KBkXp41.KA6IABOvf/f8yCzJa3Pi5zFWRpTPy1e',
                'first_name' => 'Niyigena',
                'last_name' => 'Cedric',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-19 13:54:58',
                'updated_at' => '2025-11-19 13:54:58',
            ],
            [
                'id' => 'cmi70knlx0000hf1q10isq354',
                'email' => 'remyrwa@gmail.com',
                'password' => '$2b$10$EJwdxoKpM3EaW9W8RWWx8eSfhikIK6oPVqGXQOrt8cqhgzs8WzWTW',
                'first_name' => 'Remy',
                'last_name' => 'Hirwa',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => '2025-11-20 05:51:59',
                'updated_at' => '2025-11-20 13:17:11',
            ],
            [
                'id' => 'cmi7kh2j80002hc281vkq8gkg',
                'email' => 'shyakapaulmicky@gmail.com',
                'password' => '$2b$10$Q0AWTCppqMoZeDc2SxzYR.10Fglj.nychhB/Hk0GJhmLQqJlIFSjS',
                'first_name' => 'Paul',
                'last_name' => 'SHYAKA',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-20 15:09:04',
                'updated_at' => '2025-11-20 15:09:04',
            ],
            [
                'id' => 'cmi8r2lge0000gd1r7oafhut2',
                'email' => 'niyonsengaemmanuel371@gmail.com',
                'password' => '$2b$10$Eutpo4rF1eXusa/8kG4/auaDdYFbsPY8SMqs9H2kfLw7Otx857uNy',
                'first_name' => 'Niyonsenga',
                'last_name' => 'Emmanuel',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-21 11:01:32',
                'updated_at' => '2025-11-21 11:01:32',
            ],
            [
                'id' => 'cmia18f5i0000j91qtas1nfu9',
                'email' => 'remyrwa@icloud.com',
                'password' => '$2b$10$Orndp3oI/uJYPGddwNvQMus.Z.Afwmk2lK6OoGz.eAWpxu7DfCkIa',
                'first_name' => 'Remy',
                'last_name' => 'Hirwa',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-22 08:33:46',
                'updated_at' => '2025-11-22 08:33:46',
            ],
            [
                'id' => 'cmic0pk7r0000ky1qasxwc9ii',
                'email' => 'claudeishimwe0784828463@gmail.com',
                'password' => '$2b$10$lJ2MrBF06/3JP6So6hy7zuT03y8S4b9DngbL1EfCEJ.uoCbmxCPgS',
                'first_name' => 'ISHIMWE',
                'last_name' => 'Claude',
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => '2025-11-23 17:54:38',
                'updated_at' => '2025-11-23 17:54:38',
            ],
        ];

        // Use a transaction for data integrity
        DB::beginTransaction();
        
        try {
            // Insert or update each user
            foreach ($legacyUsers as $userData) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            // Re-enable foreign key checks
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This is a one-way migration by default to prevent accidental data loss.
     * Uncomment the code below if you want to implement rollback functionality.
     */
    public function down(): void
    {
        // // WARNING: This will delete the imported users
        // Schema::disableForeignKeyConstraints();
        // 
        // $emails = [
        //     'admin@mkscholars.com',
        //     'ikuzweyvette36@gmail.com',
        //     'rusangizaandre@gmail.com',
        //     'kagabop95@gmail.com',
        //     'remyrwa@gmail.com',
        // ];
        // 
        // User::whereIn('email', $emails)->delete();
        // 
        // Schema::enableForeignKeyConstraints();
    }
};
