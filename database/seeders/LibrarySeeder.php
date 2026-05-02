<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Seed the application's database with sample data.
     * Uses firstOrCreate to be idempotent (safe to re-run).
     */
    public function run(): void
    {
        // Admin User
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Sample Member
        \App\Models\User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name' => 'John Doe',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'member',
            ]
        );

        // Categories (normalized 1NF entities)
        $catFiction = \App\Models\Category::firstOrCreate(['name' => 'Fiction']);
        $catScience = \App\Models\Category::firstOrCreate(['name' => 'Science']);
        $catBiography = \App\Models\Category::firstOrCreate(['name' => 'Biography']);

        // Authors (normalized 1NF entities)
        $authorFitzgerald = \App\Models\Author::firstOrCreate(['name' => 'F. Scott Fitzgerald']);
        $authorHawking = \App\Models\Author::firstOrCreate(['name' => 'Stephen Hawking']);
        $authorIsaacson = \App\Models\Author::firstOrCreate(['name' => 'Walter Isaacson']);

        // Books — NO 'available' column (3NF compliant, computed via JOIN)
        $book1 = \App\Models\Book::firstOrCreate(
            ['isbn' => '9780743273565'],
            [
                'title' => 'The Great Gatsby',
                'category_id' => $catFiction->id,
                'stock' => 5,
            ]
        );
        $book1->authors()->sync([$authorFitzgerald->id]);

        $book2 = \App\Models\Book::firstOrCreate(
            ['isbn' => '9780553380163'],
            [
                'title' => 'A Brief History of Time',
                'category_id' => $catScience->id,
                'stock' => 3,
            ]
        );
        $book2->authors()->sync([$authorHawking->id]);

        $book3 = \App\Models\Book::firstOrCreate(
            ['isbn' => '9781451648539'],
            [
                'title' => 'Steve Jobs',
                'category_id' => $catBiography->id,
                'stock' => 2,
            ]
        );
        $book3->authors()->sync([$authorIsaacson->id]);
    }
}
