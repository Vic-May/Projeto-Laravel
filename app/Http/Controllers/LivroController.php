<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LivroController extends Controller
{
    /** Listagem com pesquisa por título ou autor. */
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $livros = Livro::when($busca, function ($query, $busca) {
            // Agrupa os dois LIKE para que o OR não escape de outras condições.
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                  ->orWhere('autor', 'like', "%{$busca}%");
            });
        })->orderBy('titulo')->paginate(10)->withQueryString();

        return view('livros.index', compact('livros', 'busca'));
    }

    public function create()
    {
        return view('livros.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regras(), $this->mensagens());

        Livro::create($dados);

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro cadastrado com sucesso!');
    }

    public function edit(Livro $livro)
    {
        return view('livros.edit', compact('livro'));
    }

    public function update(Request $request, Livro $livro)
    {
        $dados = $request->validate($this->regras($livro), $this->mensagens());

        $livro->update($dados);

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        // Livro com empréstimos registrados não pode ser excluído (histórico).
        if ($livro->emprestimos()->exists()) {
            return redirect()->route('livros.index')
                ->with('erro', 'Não é possível excluir este livro: existem empréstimos associados a ele.');
        }

        $livro->delete();

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro excluído com sucesso!');
    }

    private function regras(?Livro $livro = null): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editora' => 'nullable|string|max:255',
            'ano_publicacao' => 'nullable|integer|min:1000|max:' . date('Y'),
            'isbn' => [
                'required', 'string', 'max:30',
                Rule::unique('livros', 'isbn')->ignore($livro?->id),
            ],
            'quantidade' => 'required|integer|min:0',
        ];
    }

    private function mensagens(): array
    {
        return [
            'titulo.required' => 'O título é obrigatório.',
            'autor.required' => 'O autor é obrigatório.',
            'ano_publicacao.integer' => 'O ano de publicação deve ser um número.',
            'ano_publicacao.min' => 'O ano de publicação deve ser maior ou igual a 1000.',
            'ano_publicacao.max' => 'O ano de publicação não pode ser maior que o ano atual.',
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.unique' => 'Já existe um livro cadastrado com este ISBN.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade não pode ser negativa.',
        ];
    }
}
