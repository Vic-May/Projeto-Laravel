<?php

namespace Database\Seeders;

use App\Models\Emprestimo;
use App\Models\Leitor;
use App\Models\Livro;
use Illuminate\Database\Seeder;

/** Dados de exemplo para demonstrar o sistema (opcional). */
class BibliotecaSeeder extends Seeder
{
    public function run(): void
    {
        $livros = collect([
            ['Dom Casmurro', 'Machado de Assis', 'Garnier', 1899, '9788535911664', 5],
            ['Memórias Póstumas de Brás Cubas', 'Machado de Assis', 'Typographia Nacional', 1881, '9788535910001', 3],
            ['Capitães da Areia', 'Jorge Amado', 'Companhia das Letras', 1937, '9788535914849', 2],
            ['Vidas Secas', 'Graciliano Ramos', 'Record', 1938, '9788501003706', 1],
            ['O Cortiço', 'Aluísio Azevedo', 'Ática', 1890, '9788508040391', 4],
        ])->map(fn ($d) => Livro::firstOrCreate(
            ['isbn' => $d[4]],
            ['titulo' => $d[0], 'autor' => $d[1], 'editora' => $d[2], 'ano_publicacao' => $d[3], 'quantidade' => $d[5]]
        ));

        $leitores = collect([
            ['Ana Souza', '111.111.111-11', 'ana@example.com', '(11) 90000-0001'],
            ['Bruno Lima', '222.222.222-22', 'bruno@example.com', '(11) 90000-0002'],
            ['Carla Mendes', '333.333.333-33', 'carla@example.com', '(11) 90000-0003'],
        ])->map(fn ($d) => Leitor::firstOrCreate(
            ['cpf' => $d[1]],
            ['nome' => $d[0], 'email' => $d[2], 'telefone' => $d[3]]
        ));

        if (Emprestimo::count() === 0) {
            // Um empréstimo em dia e um atrasado (estoque ajustado manualmente).
            foreach ([
                [$livros[0], $leitores[0], today()->subDays(3), today()->addDays(11)],
                [$livros[3], $leitores[1], today()->subDays(30), today()->subDays(16)],
            ] as [$livro, $leitor, $inicio, $previsto]) {
                Emprestimo::create([
                    'livro_id' => $livro->id,
                    'leitor_id' => $leitor->id,
                    'data_emprestimo' => $inicio,
                    'data_devolucao_prevista' => $previsto,
                    'status' => Emprestimo::EM_ANDAMENTO,
                ]);
                $livro->decrement('quantidade');
            }
        }
    }
}
