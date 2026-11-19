<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livro extends Model
{
    protected $fillable = [
        'titulo', 'autor', 'editora', 'ano_publicacao',
        'isbn', 'quantidade',
    ];

    /** Um livro pode possuir vários empréstimos. */
    public function emprestimos(): HasMany
    {
        return $this->hasMany(Emprestimo::class);
    }
}
