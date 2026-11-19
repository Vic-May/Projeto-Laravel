<?php

namespace Tests\Feature;

use App\Models\Emprestimo;
use App\Models\Leitor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Automatiza os cenários da seção "Testes funcionais" do tutorial. */
class BibliotecaTest extends TestCase
{
    use RefreshDatabase;

    private function livro(array $o = []): Livro
    {
        return Livro::create(array_merge([
            'titulo' => 'Livro de Teste',
            'autor' => 'Autor Teste',
            'editora' => 'Editora X',
            'ano_publicacao' => 2000,
            'isbn' => '978' . random_int(1000000000, 9999999999),
            'quantidade' => 3,
        ], $o));
    }

    private function leitor(array $o = []): Leitor
    {
        $n = random_int(100000, 999999);

        return Leitor::create(array_merge([
            'nome' => 'Leitor Teste',
            'cpf' => "cpf-$n",
            'email' => "leitor$n@example.com",
            'telefone' => '11 99999-9999',
        ], $o));
    }

    private function emprestimo(Livro $livro, Leitor $leitor, array $o = []): Emprestimo
    {
        return Emprestimo::create(array_merge([
            'livro_id' => $livro->id,
            'leitor_id' => $leitor->id,
            'data_emprestimo' => today()->subDays(2),
            'data_devolucao_prevista' => today()->addDays(10),
            'status' => 'Em andamento',
        ], $o));
    }

    // ---------- Livros ----------

    public function test_cadastra_livro_valido(): void
    {
        $this->post('/livros', [
            'titulo' => 'Dom Casmurro', 'autor' => 'Machado de Assis',
            'ano_publicacao' => 1899, 'isbn' => '111', 'quantidade' => 2,
        ])->assertRedirect(route('livros.index'))
          ->assertSessionHas('sucesso', 'Livro cadastrado com sucesso!');

        $this->get('/livros')->assertSee('Dom Casmurro');
    }

    public function test_livro_sem_titulo_e_recusado(): void
    {
        $this->post('/livros', ['autor' => 'A', 'isbn' => '1', 'quantidade' => 1])
            ->assertSessionHasErrors('titulo');
        $this->assertDatabaseCount('livros', 0);
    }

    public function test_isbn_duplicado_e_recusado(): void
    {
        $this->livro(['isbn' => 'ABC']);

        $this->post('/livros', ['titulo' => 'X', 'autor' => 'Y', 'isbn' => 'ABC', 'quantidade' => 1])
            ->assertSessionHasErrors('isbn');
        $this->assertDatabaseCount('livros', 1);
    }

    public function test_quantidade_negativa_e_ano_invalido_sao_recusados(): void
    {
        $this->post('/livros', ['titulo' => 'X', 'autor' => 'Y', 'isbn' => '2', 'quantidade' => -1])
            ->assertSessionHasErrors('quantidade');

        $this->post('/livros', [
            'titulo' => 'X', 'autor' => 'Y', 'isbn' => '3', 'quantidade' => 1,
            'ano_publicacao' => (int) date('Y') + 5,
        ])->assertSessionHasErrors('ano_publicacao');
    }

    public function test_edita_livro_mantendo_o_proprio_isbn(): void
    {
        $livro = $this->livro(['isbn' => 'KEEP']);

        $this->put("/livros/{$livro->id}", [
            'titulo' => 'Novo Título', 'autor' => $livro->autor, 'isbn' => 'KEEP', 'quantidade' => 7,
        ])->assertSessionHasNoErrors()->assertRedirect(route('livros.index'));

        $this->assertDatabaseHas('livros', ['id' => $livro->id, 'titulo' => 'Novo Título', 'quantidade' => 7]);
    }

    public function test_exclui_livro_sem_emprestimos(): void
    {
        $livro = $this->livro();

        $this->delete("/livros/{$livro->id}")->assertSessionHas('sucesso');
        $this->assertDatabaseMissing('livros', ['id' => $livro->id]);
    }

    public function test_exclusao_de_livro_com_emprestimo_e_bloqueada(): void
    {
        $livro = $this->livro();
        $this->emprestimo($livro, $this->leitor());

        $this->delete("/livros/{$livro->id}")->assertSessionHas('erro');
        $this->assertDatabaseHas('livros', ['id' => $livro->id]);
    }

    public function test_pesquisa_livros_por_titulo_ou_autor(): void
    {
        $this->livro(['titulo' => 'Memórias Póstumas', 'autor' => 'Machado']);
        $this->livro(['titulo' => 'Vidas Secas', 'autor' => 'Graciliano']);

        $this->get('/livros?busca=Póstumas')->assertSee('Memórias Póstumas')->assertDontSee('Vidas Secas');
        $this->get('/livros?busca=Graciliano')->assertSee('Vidas Secas')->assertDontSee('Memórias Póstumas');
    }

    // ---------- Leitores ----------

    public function test_cadastra_leitor_valido(): void
    {
        $this->post('/leitores', [
            'nome' => 'Ana Souza', 'cpf' => '111.111.111-11',
            'email' => 'ana@example.com', 'telefone' => '11 90000-0001',
        ])->assertRedirect(route('leitores.index'));

        $this->get('/leitores')->assertSee('Ana Souza');
    }

    public function test_leitor_com_cpf_repetido_ou_email_invalido_e_recusado(): void
    {
        $this->leitor(['cpf' => '999']);

        $this->post('/leitores', [
            'nome' => 'X', 'cpf' => '999', 'email' => 'x@example.com', 'telefone' => '1',
        ])->assertSessionHasErrors('cpf');

        $this->post('/leitores', [
            'nome' => 'X', 'cpf' => '888', 'email' => 'invalido', 'telefone' => '1',
        ])->assertSessionHasErrors('email');

        $this->post('/leitores', [
            'nome' => 'X', 'cpf' => '777', 'email' => 'x@example.com',
        ])->assertSessionHasErrors('telefone');
    }

    public function test_edita_leitor(): void
    {
        $leitor = $this->leitor();

        $this->put("/leitores/{$leitor->id}", [
            'nome' => 'Nome Alterado', 'cpf' => $leitor->cpf,
            'email' => $leitor->email, 'telefone' => $leitor->telefone,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('leitores', ['id' => $leitor->id, 'nome' => 'Nome Alterado']);
    }

    public function test_exclusao_de_leitor_com_emprestimo_e_bloqueada(): void
    {
        $leitor = $this->leitor();
        $this->emprestimo($this->livro(), $leitor);

        $this->delete("/leitores/{$leitor->id}")->assertSessionHas('erro');
        $this->assertDatabaseHas('leitores', ['id' => $leitor->id]);

        $livre = $this->leitor();
        $this->delete("/leitores/{$livre->id}")->assertSessionHas('sucesso');
        $this->assertDatabaseMissing('leitores', ['id' => $livre->id]);
    }

    public function test_pesquisa_leitores_por_nome(): void
    {
        $this->leitor(['nome' => 'Fulano Alfa']);
        $this->leitor(['nome' => 'Beltrano Beta']);

        $this->get('/leitores?busca=Alfa')->assertSee('Fulano Alfa')->assertDontSee('Beltrano Beta');
    }

    // ---------- Empréstimos ----------

    public function test_emprestimo_com_estoque_1_deixa_estoque_em_0(): void
    {
        $livro = $this->livro(['quantidade' => 1]);
        $leitor = $this->leitor();

        $this->post('/emprestimos', [
            'livro_id' => $livro->id, 'leitor_id' => $leitor->id,
            'data_emprestimo' => today()->toDateString(),
            'data_devolucao_prevista' => today()->addDays(7)->toDateString(),
        ])->assertRedirect(route('emprestimos.index'))->assertSessionHas('sucesso');

        $this->assertSame(0, $livro->fresh()->quantidade);
        $this->assertDatabaseHas('emprestimos', ['livro_id' => $livro->id, 'status' => 'Em andamento']);
    }

    public function test_exemplo_do_enunciado_5_para_4(): void
    {
        $livro = $this->livro(['quantidade' => 5]);

        $this->post('/emprestimos', [
            'livro_id' => $livro->id, 'leitor_id' => $this->leitor()->id,
            'data_emprestimo' => today()->toDateString(),
            'data_devolucao_prevista' => today()->addDays(7)->toDateString(),
        ]);

        $this->assertSame(4, $livro->fresh()->quantidade);
    }

    public function test_emprestimo_sem_estoque_e_recusado(): void
    {
        $livro = $this->livro(['quantidade' => 0]);

        $this->post('/emprestimos', [
            'livro_id' => $livro->id, 'leitor_id' => $this->leitor()->id,
            'data_emprestimo' => today()->toDateString(),
            'data_devolucao_prevista' => today()->addDays(7)->toDateString(),
        ])->assertSessionHasErrors('livro_id');

        $this->assertDatabaseCount('emprestimos', 0);
        $this->assertSame(0, $livro->fresh()->quantidade);
    }

    public function test_devolucao_anterior_ao_emprestimo_e_recusada(): void
    {
        $livro = $this->livro();

        $this->post('/emprestimos', [
            'livro_id' => $livro->id, 'leitor_id' => $this->leitor()->id,
            'data_emprestimo' => today()->toDateString(),
            'data_devolucao_prevista' => today()->subDay()->toDateString(),
        ])->assertSessionHasErrors('data_devolucao_prevista');

        $this->assertSame(3, $livro->fresh()->quantidade);
    }

    public function test_formulario_lista_apenas_livros_com_estoque(): void
    {
        $this->livro(['titulo' => 'Com Estoque', 'quantidade' => 2]);
        $this->livro(['titulo' => 'Sem Estoque', 'quantidade' => 0]);

        $this->get('/emprestimos/criar')->assertSee('Com Estoque')->assertDontSee('Sem Estoque');
    }

    public function test_devolucao_atualiza_status_data_e_estoque(): void
    {
        $livro = $this->livro(['quantidade' => 2]);
        $emp = $this->emprestimo($livro, $this->leitor());

        $this->patch("/emprestimos/{$emp->id}/devolver")->assertSessionHas('sucesso');

        $emp->refresh();
        $this->assertSame('Devolvido', $emp->status);
        $this->assertTrue($emp->data_devolucao->isToday());
        $this->assertSame(3, $livro->fresh()->quantidade);
    }

    public function test_devolucao_duplicada_e_recusada_sem_alterar_estoque(): void
    {
        $livro = $this->livro(['quantidade' => 2]);
        $emp = $this->emprestimo($livro, $this->leitor());

        $this->patch("/emprestimos/{$emp->id}/devolver");
        $this->patch("/emprestimos/{$emp->id}/devolver")->assertSessionHasErrors('emprestimo');

        $this->assertSame(3, $livro->fresh()->quantidade);
    }

    public function test_filtros_de_emprestimos_por_status_e_atraso(): void
    {
        $leitor = $this->leitor();
        $this->emprestimo($this->livro(['titulo' => 'Livro Em Dia']), $leitor);
        $this->emprestimo($this->livro(['titulo' => 'Livro Vencido']), $leitor, [
            'data_emprestimo' => today()->subDays(30),
            'data_devolucao_prevista' => today()->subDays(10),
        ]);
        $this->emprestimo($this->livro(['titulo' => 'Livro Devolvido']), $leitor, [
            'data_devolucao_prevista' => today()->subDays(10),
            'data_devolucao' => today()->subDays(5),
            'status' => 'Devolvido',
        ]);

        $this->get('/emprestimos?atrasados=1')
            ->assertSee('Livro Vencido')
            ->assertDontSee('Livro Em Dia')
            ->assertDontSee('Livro Devolvido');

        $this->get('/emprestimos?status=Devolvido')
            ->assertSee('Livro Devolvido')
            ->assertDontSee('Livro Vencido');
    }

    // ---------- Painel ----------

    public function test_painel_exibe_os_quatro_indicadores(): void
    {
        $leitor = $this->leitor();
        $this->leitor();
        $this->emprestimo($this->livro(), $leitor);
        $this->emprestimo($this->livro(), $leitor, [
            'data_emprestimo' => today()->subDays(30),
            'data_devolucao_prevista' => today()->subDays(10),
        ]);
        $this->emprestimo($this->livro(), $leitor, [
            'data_devolucao_prevista' => today()->subDays(10),
            'data_devolucao' => today(),
            'status' => 'Devolvido',
        ]);

        $resp = $this->get('/')->assertOk();

        $resp->assertViewHas('totalLivros', 3);
        $resp->assertViewHas('totalLeitores', 2);
        $resp->assertViewHas('emAndamento', 2);
        $resp->assertViewHas('atrasados', 1);
    }
}
