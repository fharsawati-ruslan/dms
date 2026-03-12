<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = [
            [
                'name' => 'Document Aplikasi',
                'icon' => 'heroicon-o-code-bracket',
                'color' => '#0077be', // Blue Ocean
            ],
            [
                'name' => 'Document Network & Security',
                'icon' => 'heroicon-o-shield-check',
                'color' => '#dc2626', // Red
            ],
            [
                'name' => 'Document Digital & System',
                'icon' => 'heroicon-o-cpu-chip',
                'color' => '#7c3aed', // Purple
            ],
            [
                'name' => 'Document Umum & Admin',
                'icon' => 'heroicon-o-user-group',
                'color' => '#059669', // Emerald
            ],
            [
                'name' => 'Document Tender',
                'icon' => 'heroicon-o-document-magnifying-glass',
                'color' => '#d97706', // Amber
            ],
            [
                'name' => 'Document Lain-lain',
                'icon' => 'heroicon-o-archive-box',
                'color' => '#475569', // Gray
            ],
        ];

        foreach ($folders as $folder) {
            Category::updateOrCreate(
                ['slug' => Str::slug($folder['name'])], // Unik berdasarkan slug
                [
                    'name' => $folder['name'],
                    'icon' => $folder['icon'],
                    'color' => $folder['color'],
                    'parent_id' => null, // Folder Utama tidak punya parent
                ]
            );
        }
    }
}