<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leitor extends Model
{
    // O Laravel pluralizaria "Leitor" como "leitors" (regra do inglês),
    // por isso o nome da tabela precisa ser informado explicitamente.
    protected $table = 'leitores';

    protected $fillable = ['nome', 'cpf', 'email', 'telefone'];

    /** Um leitor pode realizar vários empréstimos. */
    public function emprestimos(): HasMany
    {
        return $this->hasMany(Emprestimo::class);
    }
}
