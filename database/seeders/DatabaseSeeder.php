<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $Adminuser = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'test@example.com')->first();

        if (!$Adminuser) {
            // Create a test user if it doesn't exist
            User::create([
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => Hash::make('test123'), // Hash the password
            ]);
        }
        if (!$user) {
            // Create a test user if it doesn't exist
            User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'user',
                'password' => Hash::make('test123'), // Hash the password
            ]);
        }

        $this->call([
            DataSeeder::class,
        ]);
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
}
