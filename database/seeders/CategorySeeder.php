<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{

    public function run(): void
    {
        $categories = [
            ['name' => 'Design Gráfico', 'description' => 'Criação de logos, banners, e identidade visual.'],
            ['name' => 'Desenvolvimento Web', 'description' => 'Sites, sistemas web e manutenção.'],
            ['name' => 'Redação e Tradução', 'description' => 'Textos criativos, técnicos e traduções.'],
            ['name' => 'Marketing Digital', 'description' => 'Social media, anúncios e SEO.'],
            ['name' => 'Suporte Técnico', 'description' => 'Atendimento, manutenção e configuração.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
