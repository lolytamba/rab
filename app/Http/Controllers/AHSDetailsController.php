<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\AHSDetailsTransformers;
use Illuminate\Support\Facades\DB;
use App\AHSDetails;
use App\AHS; 
use App\Materials;

class AHSDetailsController extends RestController
{
    protected $transformer = AHSDetailsTransformers::Class;

    public function index()
    {
        $ahs_details = AHSDetails::all();
        $response = $this->generateCollection($ahs_details);
        return $this->sendResponse($response,200);
    }

    public function update(Request $request, $id)
    {
        $this->validateWith([
            'coefficient' => 'required|max:255',
        ]);

        $detail = AHSDetails::findOrFail($id);
        $ahs = AHS::where('id_ahs',$detail->id_ahs)->first();
        $material = Materials::where('id_material',$detail->id_material)->first();

        $ahs->total = $ahs->total - $detail->sub_total;
        $ahs->save();

        $detail->coefficient = $request->coefficient;
        $detail->sub_total = $detail->coefficient * $material->price;
        $detail->save();
        
        $ahs->total = $ahs->total + $detail->sub_total;
        $ahs->save();

        return response()->json([
            'status' => (bool) $detail,
            'data' => $detail,
            'message' => $detail ? 'Success' : 'Error Detail'
        ]);
    }

    public function destroy($id)
    {
        $detail = AHSDetails::findOrFail($id);
        // dd($detail);
        $ahs = AHS::findOrFail($detail->id_ahs);

        $material = Materials::where('id_material',$detail->id_material)->first();
        if($material->status == "material")
            $ahs->total_material -= $detail->sub_total;
        else
            $ahs->total_labor -= $detail->sub_total;

        $ahs->total -= $detail->sub_total;
      
        $ahs->save();
        $status = $detail->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Success' : 'Error Delete'
        ]);
    }

    public function showbyID($id)
    {
        $ahs = AHSDetails::where('id_ahs',$id)->get();
        $response = $this->generateCollection($ahs);
        return $this->sendResponse($response,200);
    }
}
