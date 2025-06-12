<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Todo::create([
            'title' => 'Mempelajari Laravel 12',
            'assignee' => 'Rinaldi Putra',
            'due_date' => '2025-07-15',
            'time_tracked' => 180,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        Todo::create([
            'title' => 'Membuat Dokumentasi API',
            'assignee' => 'John Doe',
            'due_date' => '2025-08-01',
            'time_tracked' => 60,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        Todo::create([
            'title' => 'Refaktor Kode Lama',
            'assignee' => 'Alice',
            'due_date' => '2025-07-25',
            'time_tracked' => 120,
            'status' => 'pending',
            'priority' => 'low',
        ]);

        Todo::create([
            'title' => 'Perbaiki Bug Produksi',
            'assignee' => 'Bob',
            'due_date' => '2025-07-05',
            'time_tracked' => 90,
            'status' => 'completed',
            'priority' => 'high',
        ]);

        Todo::create([
            'title' => 'Diskusi Fitur Baru',
            'assignee' => 'John Doe',
            'due_date' => '2025-08-10',
            'time_tracked' => 45,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        Todo::create([
            'title' => 'Persiapan Ujian',
            'assignee' => null,
            'due_date' => '2025-07-01',
            'time_tracked' => 200,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

    }
}
