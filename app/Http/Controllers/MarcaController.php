<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        // $marcas = Marca::all();
        $marcas = $this->marca->with('modelos')->get();
        return response()->json($marcas, 200);
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
        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null) {
            return response()->json(['erro' => 'A marca pesquisada não existe.'], 404);
        }
        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);
        if ($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado para atualização não existe.'], 404);
        }
        if ($request->method() === 'PATCH') {
            return ['teste' => 'Teste metódo Patch'];

            $regrasDinamicas = array();


            foreach ($marca->rules() as $input => $regra) {

                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            };
            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        //Remove o Arquivo antigo caso o novo tenha sido enviado no request.
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca->fill();
        $marca->imagem = $imagem_urn;
        $marca->save();

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn
        // ]);
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado para ser deletado não existe.'], 404);
        }


        Storage::disk('public')->delete($marca->imagem);


        $marca->delete();
        return response()->json(['msg' =>  'A marca foi removida do sistema.'], 200);
    }
}
