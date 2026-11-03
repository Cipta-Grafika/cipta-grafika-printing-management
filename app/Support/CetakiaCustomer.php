<?php

namespace App\Support;

class CetakiaCustomer
{
    private static ?array $cached = null;

    /**
     * Data master pelanggan dari sistem Cetakia
     *
     * @return array<int, array{id: string, customer_code: string, name: string}>
     */
    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $jsonFile = __DIR__ . '/cetakia_customers.json';
        if (file_exists($jsonFile)) {
            $data = json_decode((string) file_get_contents($jsonFile), true);
            if (is_array($data)) {
                self::$cached = $data;
                return self::$cached;
            }
        }

        return self::fallback();
    }

    /**
     * Data cadangan minimal jika file json belum termuat
     *
     * @return array<int, array{id: string, customer_code: string, name: string}>
     */
    public static function fallback(): array
    {
        return [
            [
                'id' => '019ddd30-41f5-757a-b610-b9ccd44dbd7e',
                'customer_code' => 'USR000394',
                'name' => 'Tres De Padel',
            ],
            [
                'id' => '019dd2d5-a18d-7556-8eee-2e99392262e3',
                'customer_code' => 'USR000387',
                'name' => 'Nadhira Falisha',
            ],
            [
                'id' => '019dd8b1-c5ad-70ef-bc7c-8a4fe0dc2d6d',
                'customer_code' => 'USR000392',
                'name' => 'Diyah Fitaloka',
            ],
            [
                'id' => '019dd3e3-5c93-7507-b9bc-b8d20dd069a7',
                'customer_code' => 'USR000389',
                'name' => 'Nurina Maretha Rianti',
            ],
        ];
    }
}
