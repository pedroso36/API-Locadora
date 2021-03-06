<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;


class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modelos = array();
        if ($request->has('atributos_marca')) {
            $atributos_marca = $request->atributo_marca;
            $modelos = $this->modelo->with('marca:id,' . $atributos_marca);
        } else {
            $modelos = $this->modelo->with('marca');
        }

        if ($request->has('filtro')) {

            $condicoes = (explode(':', $request->filtro));
            $numeroDeFiltros = count($condicoes);

            // $filtro['marcas'] = [];

            // dd($condicoes);

            if ($numeroDeFiltros > 1) {
                $modelos = $this->modelo->whereIn('nome', $condicoes)->get();
            } else {
                $modelos = $this->modelo->where('nome', 'like', '%' . $condicoes[0] . '%')->get();
            }


            dd($modelos);
        }

        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $atributos_marca = $request->atributos_marca;
            $modelos = $this->modelo->selectRaw($atributos)->with('marca:id,' . $atributos_marca)->get();
        } else {
            $modelos = $this->modelo->with('marca')->get();
        }



        // ($this->modelo->with('marca')->get(), 200);
        return response()->json($modelos, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $modelo = $this->modelo->with('marca')->find($id);
        if ($modelo === null) {
            return response()->json(['erro' =>
            'Modelo pesquisado n??o existe.'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'O modelo pesquisado para atualiza????o n??o existe.'], 404);
        }
        if ($request->method() === 'PATCH') {
            return ['teste' => 'Teste met??do Patch'];

            $regrasDinamicas = array();


            foreach ($modelo->rules() as $input => $regra) {

                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            };

            $request->validate($regrasDinamicas);
        } else {
            $request->validate($modelo->rules());
        }
        //Remove o Arquivo antigo caso o novo tenha sido enviado no request.
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');
        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $request->imagem,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'O modelo pesquisado para ser deletado n??o existe.'], 404);
        }


        Storage::disk('public')->delete($modelo->imagem);


        $modelo->delete();
        return response()->json(['msg' =>  'O modelo foi removida do sistema.'], 200);
    }
}
