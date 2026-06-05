<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@example.com',
                'contact_number' => '09171234567',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@example.com',
                'contact_number' => '09181234567',
            ],
            [
                'first_name' => 'Angela',
                'last_name' => 'Reyes',
                'email' => 'angela.reyes@example.com',
                'contact_number' => '09191234567',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Garcia',
                'email' => 'miguel.garcia@example.com',
                'contact_number' => '09201234567',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Mendoza',
                'email' => 'patricia.mendoza@example.com',
                'contact_number' => '09211234567',
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Navarro',
                'email' => 'carlos.navarro@example.com',
                'contact_number' => '09221234567',
            ],
            [
                'first_name' => 'Sofia',
                'last_name' => 'Villanueva',
                'email' => 'sofia.villanueva@example.com',
                'contact_number' => '09231234567',
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Aquino',
                'email' => 'daniel.aquino@example.com',
                'contact_number' => '09241234567',
            ],
            [
                'first_name' => 'Isabella',
                'last_name' => 'Torres',
                'email' => 'isabella.torres@example.com',
                'contact_number' => '09251234567',
            ],
            [
                'first_name' => 'Gabriel',
                'last_name' => 'Ramos',
                'email' => 'gabriel.ramos@example.com',
                'contact_number' => '09261234567',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::query()->updateOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }
    }
}
