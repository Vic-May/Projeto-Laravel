<?php

namespace App\Http\Controllers;

use App\Models\Leitor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeitorController extends Controller
{
    /** Listagem com pesquisa por nome. */
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $leitores = Leitor::when($busca, function ($query, $busca) {
            $query->where('nome', 'like', "%{$busca}%");
        })->orderBy('nome')->paginate(10)->withQueryString();

        return view('leitores.index', compact('leitores', 'busca'));
    }

    public function create()
    {
        return view('leitores.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regras(), $this->mensagens());

        Leitor::create($dados);

        return redirect()->route('leitores.index')
            ->with('sucesso', 'Leitor cadastrado com sucesso!');
    }

    public function edit(Leitor $leitor)
    {
        return view('leitores.edit', compact('leitor'));
    }

    public function update(Request $request, Leitor $leitor)
    {
        $dados = $request->validate($this->regras($leitor), $this->mensagens());

        $leitor->update($dados);

        return redirect()->route('leitores.index')
            ->with('sucesso', 'Leitor atualizado com sucesso!');
    }

    public function destroy(Leitor $leitor)
    {
        // Leitor com empréstimos registrados não pode ser excluído (histórico).
        if ($leitor->emprestimos()->exists()) {
            return redirect()->route('leitores.index')
                ->with('erro', 'Não é possível excluir este leitor: existem empréstimos associados a ele.');
        }

        $leitor->delete();

        return redirect()->route('leitores.index')
            ->with('sucesso', 'Leitor excluído com sucesso!');
    }

    private function regras(?Leitor $leitor = null): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required', 'string', 'max:14',
                Rule::unique('leitores', 'cpf')->ignore($leitor?->id),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('leitores', 'email')->ignore($leitor?->id),
            ],
            'telefone' => 'required|string|max:30',
        ];
    }

    private function mensagens(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Já existe um leitor cadastrado com este CPF.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail em formato válido.',
            'email.unique' => 'Já existe um leitor cadastrado com este e-mail.',
            'telefone.required' => 'O telefone é obrigatório.',
        ];
    }
}
