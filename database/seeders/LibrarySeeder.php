<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample Member
        \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'member@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member',
        ]);

        // Sample Books
        \App\Models\Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'category' => 'Fiction',
            'stock' => 5,
            'available' => 5,
        ]);

        \App\Models\Book::create([
            'title' => 'A Brief History of Time',
            'author' => 'Stephen Hawking',
            'isbn' => '9780553380163',
            'category' => 'Science',
            'stock' => 3,
            'available' => 3,
        ]);

        \App\Models\Book::create([
            'title' => 'Steve Jobs',
            'author' => 'Walter Isaacson',
            'isbn' => '9781451648539',
            'category' => 'Biography',
            'stock' => 2,
            'available' => 2,
        ]);
    }
}
