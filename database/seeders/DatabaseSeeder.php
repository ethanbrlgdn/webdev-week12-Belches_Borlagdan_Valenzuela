<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The password comes from .env (SEED_PASSWORD) so none is stored in the repo.
        $password = env('SEED_PASSWORD');

        if (blank($password)) {
            throw new \RuntimeException('Set SEED_PASSWORD in your .env before seeding.');
        }

        $people = [
            ['Alice Reader', 'alice@example.com', [
                ['Clean Code', 'Robert C. Martin', 'finished', 5],
                ['Eloquent JavaScript', 'Marijn Haverbeke', 'reading', null],
                ['The Pragmatic Programmer', 'Andrew Hunt', 'to-read', null],
            ]],
            ['Bob Bookworm', 'bob@example.com', [
                ['Dune', 'Frank Herbert', 'finished', 4],
                ['Project Hail Mary', 'Andy Weir', 'reading', null],
                ['Sapiens', 'Yuval Noah Harari', 'to-read', null],
            ]],
        ];

        foreach ($people as [$name, $email, $books]) {
            $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);

            foreach ($books as [$title, $author, $status, $rating]) {
                $user->books()->create(compact('title', 'author', 'status', 'rating'));
            }
        }
    }
}