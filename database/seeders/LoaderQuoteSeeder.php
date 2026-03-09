<?php

namespace Database\Seeders;

use App\Models\LoaderQuote;
use Illuminate\Database\Seeder;

class LoaderQuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            ['locale' => 'fr', 'type' => 'message', 'text' => 'Le prochain plan arrive.', 'author' => null, 'weight' => 80],
            ['locale' => 'fr', 'type' => 'message', 'text' => 'Je range deux props et je reviens.', 'author' => null, 'weight' => 70],
            ['locale' => 'fr', 'type' => 'message', 'text' => 'Une transition qui saute, c’est une discussion avec le GPU.', 'author' => null, 'weight' => 60],
            ['locale' => 'fr', 'type' => 'message', 'text' => 'Mode dev : un petit détour, pas une disparition.', 'author' => null, 'weight' => 50],
            ['locale' => 'fr', 'type' => 'quote', 'text' => 'Il faut du temps pour voir ce qui tient.', 'author' => 'Rene Char', 'weight' => 40],
            ['locale' => 'fr', 'type' => 'quote', 'text' => 'Le monde se refait toujours dans le détail.', 'author' => null, 'weight' => 30],
            ['locale' => 'fr', 'type' => 'quote', 'text' => 'Je suis faite de tous les mots que je ne peux pas prononcer.', 'author' => 'Paul B. Preciado', 'weight' => 20],
            ['locale' => 'fr', 'type' => 'quote', 'text' => 'Le visible est toujours traversé par ce qui insiste en dessous.', 'author' => null, 'weight' => 10],
            ['locale' => 'fr', 'type' => 'message', 'text' => 'Le prochain plan arrive avec un peu de drama controle.', 'author' => null, 'weight' => 5],

            ['locale' => 'en', 'type' => 'message', 'text' => 'The next view is arriving.', 'author' => null, 'weight' => 80],
            ['locale' => 'en', 'type' => 'message', 'text' => 'I am rearranging two props and coming back.', 'author' => null, 'weight' => 70],
            ['locale' => 'en', 'type' => 'message', 'text' => 'A jumpy transition is basically a conversation with the GPU.', 'author' => null, 'weight' => 60],
            ['locale' => 'en', 'type' => 'message', 'text' => 'Dev mode: a short detour, not a disappearance.', 'author' => null, 'weight' => 50],
            ['locale' => 'en', 'type' => 'quote', 'text' => 'It takes time to see what really holds.', 'author' => 'Rene Char', 'weight' => 40],
            ['locale' => 'en', 'type' => 'quote', 'text' => 'The world is rebuilt inside the details.', 'author' => null, 'weight' => 30],
            ['locale' => 'en', 'type' => 'quote', 'text' => 'I am made of all the words I cannot pronounce.', 'author' => 'Paul B. Preciado', 'weight' => 20],
            ['locale' => 'en', 'type' => 'quote', 'text' => 'The visible is always crossed by what insists underneath.', 'author' => null, 'weight' => 10],
            ['locale' => 'en', 'type' => 'message', 'text' => 'The next view arrives with a little controlled drama.', 'author' => null, 'weight' => 5],
        ];

        foreach ($quotes as $quote) {
            LoaderQuote::query()->updateOrCreate(
                [
                    'locale' => $quote['locale'],
                    'text' => $quote['text'],
                ],
                [
                    'type' => $quote['type'],
                    'author' => $quote['author'],
                    'is_active' => true,
                    'theme_target' => null,
                    'weight' => $quote['weight'],
                ],
            );
        }
    }
}
