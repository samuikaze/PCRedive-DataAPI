<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'table' => 'roles',
                'data' => [
                    [
                        'accessibles' => json_encode([], JSON_UNESCAPED_UNICODE),
                        'name' => '待審核',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'accessibles' => json_encode(['sysop', 'viewdata', 'editdata'], JSON_UNESCAPED_UNICODE),
                        'name' => '超級管理員',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ], [
                'table' => 'users',
                'data' => [
                    [
                        'role_of' => 2,
                        'username' => 'administrator',
                        'password' => Hash::make('123'),
                        'nickname' => '超級管理員',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ], [
                'table' => 'versions',
                'data' => [
                    [
                        'version_id' => '0.0.1a',
                        'content' => json_encode(['建立專案'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-12 02:40:00'),
                        'updated_at' => Carbon::parse('2020-10-12 02:40:00'),
                    ], [
                        'version_id' => '0.0.2',
                        'content' => json_encode(['完成登入及註冊功能', '完成後台登入驗證'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-15 01:12:00'),
                        'updated_at' => Carbon::parse('2020-10-15 01:12:00'),
                    ], [
                        'version_id' => '0.0.3',
                        'content' => json_encode(['完成權限驗證', '審核頁面完成，功能尚未完成'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-16 02:35:00'),
                        'updated_at' => Carbon::parse('2020-10-16 02:35:00'),
                    ],
                ],
            ],
        ];

        /**
         * 開始執行 Seed
         *
         * @link https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli PHP-CLI-Color
         */
        print("\033[33mSeeding: \033[39m開始執行 Seed。\n");

        $start = microtime(true);
        $times = 0;
        $rows = 0;
        foreach ($data as $d) {
            DB::table($d['table'])->insert($d['data']);
            $rows += count($d['data']);
            $times += 1;
        }

        $duration = round(microtime(true) - $start, 3);

        print("\033[32mSeeded: \033[39mSeed 執行完畢，總共耗時 $duration 秒，查詢 $times 次，影響 $rows 行。\n");
    }
}
