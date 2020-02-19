<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\AHSTransformers2;
use Illuminate\Support\Facades\DB;
use App\AHSDetails;
use App\AHS;

class AHSController extends RestController
{
    protected $transformer = AHSTransformers2::Class;

    public function index()
    {
        $ahs = AHS::all();
        $response = $this->generateCollection($ahs);
        return $this->sendResponse($response,200);
    }

    public function paginate()
    {
        $ahs = AHS::orderBy('id_ahs','DESC')->paginate(2);
        return $ahs;
        // $response = $this->generateCollection($ahs);
        // return $this->sendResponse($response,200);
    }

    public function store(Request $request)
    {
        try{
            if($request->has('detail'))
            {
                $detail_ahs = $request->get('detail');
            }
            
            $ahs = new AHS;
            $ahs->kode = $request->get('kode');
            $ahs->id_job = $request->get('id_job');
            $ahs->id_sub = $request->get('id_sub');
            $ahs->total_labor = $request->get('total_labor');
            $ahs->total_material = $request->get('total_material');
            $ahs->total = $request->get('total');
            $ahs->save();
            
            if($request->has('detail'));
            {
                $ahs = DB::transaction(function () use ($ahs,$detail_ahs) {
                    $ahs->detail_ahs()->createMany($detail_ahs);   
                    return $ahs;
                });
            }
            $response = $this->generateItem($ahs);
            return $this->sendResponse($response,201);
        }catch(\Exception $e){
            return $this->sendIseResponse($e->getMessage());
        }
    }

    public function update(Request $request,$id)
    {
        $ahs=AHS::findOrFail($id);
        if($request->has('detail'))
        {
            $detail = $request->get('detail');
        }
        $Detail = AHSDetails::where('id_ahs',$id)->select('id_ahs_details','id_material','coefficient','sub_total')->get();
        // dd($Detail);
        $details=[];
        // dd($detail);

        // $result = array_intersect($Detail,$detail);
        $collection = collect($detail);
        // dd($newCollection);
        // dd($collection);
        $result = $Detail->intersectByKeys($collection);
        // print_r($result);
        // $result->all();
        dd($result);

        foreach($Detail as $ahs_details)
        {
            foreach($detail as $detail_ahs)
            {
                if($detail_ahs['id_ahs_details'] == null)
                {
                    array_push($details,$ahs->detail_ahs()->create($detail_ahs));
                }
                else
                {
                    $detail_data = AHSDetails::find($detail_ahs['id_ahs_details']);
                    $detail_data->id_material = $detail_ahs['id_material'];
                    $detail_data->coefficient = $detail_ahs['coefficient'];
                    $detail_data->sub_total = $detail_ahs['sub_total'];
                    $detail_data->save();
                    
                }
            }
        }
            $ahs->total_labor = $request->total_labor; 
            $ahs->total_material = $request->total_material;
            $ahs->total = $request->total;
            $ahs->save();

        // }

        // $detail_ahs = AHSDetails::where('id_ahs',$id)->get();
        // foreach($detail_ahs as $detail)
        // {
        //     if(AHSDetails::where('id_ahs',$id)->get() != null)
        //         $delete = AHSDetails::where('id_ahs',$id)->delete();
        // }
        
        // $detail_ahs = $request->get('detail');
    
        // $ahs=AHS::findOrFail($id);
        // $ahs->kode = $request->get('kode');
        // $ahs->id_sub = $request->get('id_sub');
        // $ahs->total_labor = $request->get('total_labor');
        // $ahs->total_material = $request->get('total_material');
        // $ahs->total = $request->get('total');
        // $ahs->save();

        // if($request->has('detail'));
        // {
        //     $ahs = DB::transaction(function () use ($ahs,$detail_ahs) {
        //         $ahs->detail_ahs()->createMany($detail_ahs);   
        //         return $ahs;
        //     });
        // }

        // $response = $this->generateCollection($ahs);
        // return $this->sendResponse($response,200);
    }

    public function destroy($id)
    {
        $details=AHSDetails::where('id_ahs',$id)->get();
        foreach($details as $detail)
        {
            if(AHSDetails::where('id_ahs',$id)->get() !== null)
            $delete = AHSDetails::where('id_ahs',$id)->delete();
        }

        $ahs=AHS::find($id);
        $status = $ahs->delete();
        
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Deleted' : 'Error Delete'
        ]);
    }

    public function showbyID($id)
    {
        $ahs = AHS::findOrFail($id);
        return response()->json($ahs,200);
    }

    public function count_ahs()
    {
        $ahs = AHS::all();
        $count = count($ahs);

        $result['data'][0]['count']=$count;
        return $result;
    }
}
