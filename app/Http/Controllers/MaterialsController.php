<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Materials;
use App\Transformers\MaterialsTransformers;

class MaterialsController extends RestController
{
    protected $transformer=MaterialsTransformers::Class;

    public function index()
    {
        $material = Materials::orderBy('id_material','DESC')->get();
        $response = $this->generateCollection($material);
        return $this->sendResponse($response,200);
    }

    public function store(Request $request)
    {
        $material = Materials::create([
            'kode' => $request->kode,
            'id_store' => $request->id_store,
            'name' => $request->name,
            'type' => $request->type,
            'satuan' => $request->satuan,
            'spesification' => $request->spesification,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => (bool) $material,
            'data' => $material,
            'message' => $material ? 'Success' : 'Error Material'
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validateWith([
            'kode' => 'required|max:255',
            'id_store' => 'required|max:255',
            'name' => 'required|max:255',
            'type' => 'required|max:255',
            'price' => 'required|max:255',
            'spesification' => 'required|max:255',
            //'status' => 'required|max:255',
            'satuan' => 'required|max:255',
        ]);

        $material = Materials::findOrFail($id);
        $material->kode = $request->kode;
        $material->id_store = $request->id_store;
        $material->name = $request->name;
        $material->type = $request->type;
        $material->price = $request->price;
        $material->spesification = $request->spesification;
        $material->status = $request->status;
        $material->satuan = $request->satuan;
        
        $material->save();

        return response()->json([
            'status' => (bool) $material,
            'data' => $material,
            'message' => $material ? 'Success' : 'Error Material'
        ]);
    }

    public function showbyID($id)
    {
        $material = Materials::findOrFail($id);
        return response()->json($material,200);
    }

    public function destroy($id)
    {
        $material = Materials::findOrFail($id);
        $status = $material->delete();
        return response()->json([
            'status' => $material,
            'message' => $material ? 'Deleted' : 'Error Delete'
        ]);
    }

    public function code()
    {
        $job = Materials::all()->last();
        if($job != null)
            $parts = explode('-',$job->kode);
        if($job==null){
            $kode = 'M/L'.'-'.'0001';
        }
        else if(($parts[1]+1)<10) {
            $kode = 'M/L'.'-'.'000'.($parts[1]+1);
        }else if(($parts[1]+1)>=10 && ($parts[1]+1)<99){
            $kode = 'M/L'.'-'.'00'.($parts[1]+1);
        }else if(($parts[1]+1)>=99 && ($parts[1]+1)<999){
            $kode = 'M/L'.'-'.'0'.($parts[1]+1);
        }else if(($parts[1]+1)==1000){
            $kode = 'M/L'.'-'.($parts[1]+1);
        }else{
            $kode = 'M/L'.'-'.'001';
        }
        return $kode;
    }
}
