<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{

    use HasFactory;
    protected $table = 'marcas';
    protected $fillable = ['nome', 'imagem'];


    public function modelos()
    {
        return $this->hasMany('App\Models\Modelo');
    }

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatorio.',
            'nome.unique' => 'O nome da marca já existe.',
            'imagem.mimes' => 'O arquivo deve ser uma imagem PNG.'
        ];
    }
}
