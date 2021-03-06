<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PDF;
use App\AHS;
use App\RABDetails;

class ReportsController extends Controller
{
    public function ahs_master($id)
    {
        $ahs = AHS::findOrFail($id);
        $datas = DB::table('a_h_s_s')
                ->select('a_h_s_s.kode as kode_ahs','jobs.kode as kode_task','jobs.name as task',
                        'satuans.name as satuan_task','ahs_details.coefficient as coefficient',
                        'ahs_details.sub_total as price_satuan','materials.kode as kode_material',
                        'materials.name as material','satuanM.name as satuan_material',
                        'materials.price','materials.status','a_h_s_s.total_labor',
                        'a_h_s_s.total_material', 'a_h_s_s.total_before_overhead',
                        'a_h_s_s.total_equipment','a_h_s_s.overhead','a_h_s_s.total')
                ->join('jobs','jobs.id_job','=','a_h_s_s.id_job')
                ->join('satuans','satuans.id_satuan','=','jobs.id_satuan',)
                ->join('ahs_details','ahs_details.id_ahs','=','a_h_s_s.id_ahs')
                ->join('materials','materials.id_material','=','ahs_details.id_material')
                ->join('satuans as satuanM','satuanM.id_satuan','=','materials.id_satuan',)
                ->where('a_h_s_s.id_ahs','=',$id)
                ->whereNull('ahs_details.deleted_at')
                ->orderBy('materials.status','DESC')
                ->get();
                // dd($datas);
        $j=0;
        $index = count($datas);
        for($i=0;$i<$index;$i++)
        {
            if($datas[$i]->status == "labor")
            {
                $j = $i;
                break;
            }
            if($datas[$i]->status == "material")
                $j +=1;
        }
        $pdf = PDF::loadView('ahs_master_report',['datas' => $datas, 'j'=>$j]);
        $pdf->setPaper('A4','potrait');
        return $pdf->stream();
    }

    public function ahs_lokal($id)
    {
        $ahs = RABDetails::findOrFail($id);
        $datas = DB::table('ahs_lokals')
                ->select('projects.name as project','jobs.kode as kode_task','jobs.name as task','satuans.name as satuan_task',
                        'ahs_lokal_details.coefficient as coefficient','ahs_lokal_details.sub_total as price_satuan',
                        'materials.kode as kode_material','materials.name as material',
                        'satuanM.name as satuan_material','materials.price','materials.status','jobs.status as status_job',
                        'ahs_lokals.total_labor','ahs_lokals.total_material','ahs_lokals.total_equipment',
                        'ahs_lokals.HSP as total', 'ahs_lokals.adjustment', 'ahs_lokals.volume',
                        'ahs_lokals.overhead','ahs_lokals.HSP_before_overhead as total_before_overhead')
                ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
                ->join('ahs_lokal_details','ahs_lokal_details.id_ahs_lokal','=','ahs_lokals.id_ahs_lokal')
                ->join('materials','materials.id_material','=','ahs_lokal_details.id_material')
                ->join('task_sub_details','task_sub_details.id_sub_details','=','ahs_lokals.id_sub_details')
                ->join('group_details','group_details.id_group_details','=','task_sub_details.id_group_details')
                ->join('structure_details','structure_details.id_structure_details','=','group_details.id_structure_details')
                ->join('rabs','rabs.id_rab','=','structure_details.id_rab')
                ->join('projects','projects.id_project','=','rabs.id_project')
                ->join('satuans','satuans.id_satuan','=','jobs.id_satuan')
                ->join('satuans as satuanM','satuanM.id_satuan','=','materials.id_satuan')
                ->where('ahs_lokals.id_ahs_lokal','=',$id)
                ->whereNull('ahs_lokal_details.deleted_at')
                ->orderBy('materials.status','DESC')
                ->get();
        // dd($datas);
        $j=0;
        $index = count($datas);
        for($i=0;$i<$index;$i++)
        {
            if($datas[$i]->status == "labor")
            {
                $j = $i;
                break;
            }
            if($datas[$i]->status == "material")
                $j +=1;
        }
        $pdf = PDF::loadView('ahs_lokal_report',['datas' => $datas, 'j'=>$j]);
        $pdf->setPaper('A4','potrait');
        return $pdf->stream();
    }

    function integerToRoman($integer)
    {
        $integer = intval($integer);
        $result = '';
     
        $lookup = array('M' => 1000,'CM' => 900,'D' => 500,'CD' => 400,'C' => 100,'XC' => 90,'L' => 50,
                        'XL' => 40,'X' => 10,'IX' => 9,'V' => 5,'IV' => 4,'I' => 1);
        
        foreach($lookup as $roman => $value)
        {
            $matches = intval($integer/$value);
            $result .= str_repeat($roman,$matches);
            $integer = $integer % $value;
        }
        return $result;
    }

    function tanggal_ind($tanggal){
        $bulan = array (
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
        );
        
        $pecahkan = explode('-', $tanggal);
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }

    public function rab($id,$ppn,$jasa)
    {
        $rab = DB::table('rabs')
            ->select('projects.name','projects.owner','projects.date','projects.address', 'projects.type','rabs.total_rab',
                        'task_subs.id_sub','groups.id_groups','structures.id_structure','ahs_lokals.adjustment',
                        'ahs_lokals.volume','ahs_lokals.HSP',
                        'ahs_lokals.HP_Adjust','jobs.name as job', 'satuans.name as satuan','jobs.status',
                        'task_sub_details.id_sub_details','ahs_lokals.id_ahs_lokal', 
                        'ahs_lokals.id_sub_details')
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->join('structures','structures.id_structure','=','structure_details.id_structure')
            ->join('satuans','satuans.id_satuan','=','jobs.id_satuan')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->whereNull('ahs_lokals.deleted_at')
            ->get();
        // dd($rab);
        $newDate = $this->tanggal_ind($rab[0]->date);
        $totalSt = DB::table('rabs')
            ->select(DB::raw('SUM(ahs_lokals.HP_Adjust) as Total,structure_details.id_structure'))
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id) 
            ->whereNull('ahs_lokals.deleted_at')
            ->groupBy('structure_details.id_structure')
            ->get();
        // dd($totalSt);
        $structure = DB::table('rabs')
            ->select('structures.id_structure','structures.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('structures','structure_details.id_structure','=','structures.id_structure')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->get();
        // dd($structure);
        $group = DB::table('rabs')
            ->select('structure_details.id_structure','groups.id_groups','groups.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->get();
        // dd($group);

        $tasksub = DB::table('rabs')
            ->select('structure_details.id_structure','group_details.id_groups','task_subs.id_sub','task_subs.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->get();
        // dd($tasksub);
        $alphabet=[];
        foreach(range('A','Z') as $alphabet_data)
        {
            array_push($alphabet,$alphabet_data);
        }

        $index = count($group);
        $roman=[];
        for($i=1;$i<=$index;$i++)
        {
            $data = $this->integerToRoman($i);
            array_push($roman,$data);
        }
        // dd($roman);
        $pdf = PDF::loadView('rab_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                            'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman,'totalSt'=>$totalSt,
                            'ppn'=>$ppn, 'jasa'=>$jasa,'newDate'=>$newDate]);
        $pdf->setPaper('A4','potrait');
        return $pdf->stream();
    }

    public function rab_bq($id)
    {
        $rab = DB::table('rabs')
            ->select('projects.name','projects.owner','projects.date','projects.address', 'projects.type',
                        'task_subs.id_sub','groups.id_groups','structures.id_structure','ahs_lokals.adjustment',
                        'ahs_lokals.volume','ahs_lokals.HSP','ahs_lokals.HP_Adjust','jobs.name as job', 
                        'satuans.name as satuan','jobs.status')
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->join('structures','structures.id_structure','=','structure_details.id_structure')
            ->join('satuans','satuans.id_satuan','=','jobs.id_satuan')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->whereNull('ahs_lokals.deleted_at')
            ->get();
        // dd($rab);
        $newDate = $this->tanggal_ind($rab[0]->date);
        $structure = DB::table('rabs')
            ->select('structures.id_structure','structures.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('structures','structure_details.id_structure','=','structures.id_structure')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->get();
        // dd($structure);
        $group = DB::table('rabs')
            ->select('structure_details.id_structure','groups.id_groups','groups.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->get();
        // dd($group);

        $tasksub = DB::table('rabs')
            ->select('structure_details.id_structure','group_details.id_groups','task_subs.id_sub','task_subs.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->get();
        // dd($tasksub);
        $alphabet=[];
        foreach(range('A','Z') as $alphabet_data)
        {
            array_push($alphabet,$alphabet_data);
        }

        $index = count($group);
        $roman=[];
        for($i=1;$i<=$index;$i++)
        {
            $data = $this->integerToRoman($i);
            array_push($roman,$data);
        }
        // dd($roman);
        $pdf = PDF::loadView('rab_bq_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                            'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman, 'newDate'=>$newDate]);
        $pdf->setPaper('A4','potrait');
        return $pdf->stream();
    }

    public function rab_mr($id,$type)
    {
        $rab = DB::table('rabs')
            ->select('projects.name','projects.owner','projects.date','projects.address', 'projects.type',
                    'task_subs.id_sub','groups.id_groups','structures.id_structure','ahs_lokals.id_ahs_lokal',
                    'ahs_lokals.adjustment','ahs_lokals.volume','ahs_lokals.HSP',
                    'ahs_lokals.HP_Adjust','jobs.name as job', 'satuans.name as satuan','jobs.status',
                    'ahs_lokal_details.id_material','materials.kode','materials.name as materials',
                    'materials.price', 'satuanM.name  as sat_mat','ahs_lokals.id_sub_details',
                    'ahs_lokal_details.coefficient','ahs_lokals.adjustment',
                    'ahs_lokal_details.sub_total','ahs_lokal_details.adjustment')
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('ahs_lokal_details','ahs_lokal_details.id_ahs_lokal','=','ahs_lokals.id_ahs_lokal')
            ->join('materials','ahs_lokal_details.id_material','=','materials.id_material')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->join('structures','structures.id_structure','=','structure_details.id_structure')
            ->join('satuans','satuans.id_satuan','=','jobs.id_satuan')
            ->join('satuans as satuanM','satuanM.id_satuan','=','materials.id_satuan')
            ->where('rabs.id_rab','=',$id)
            // ->where('materials.status','=','material')
            // ->orderBy('structure_details.id_structure')
            ->orderBy('materials.name')
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->whereNull('ahs_lokals.deleted_at')
            ->whereNull('ahs_lokal_details.deleted_at')
            ->get();
        // dd($rab);
        $newDate = $this->tanggal_ind($rab[0]->date);
        $structure = DB::table('rabs')
            ->select('structures.id_structure','structures.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('structures','structure_details.id_structure','=','structures.id_structure')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->get();
        // dd($structure);
        $group = DB::table('rabs')
            ->select('structure_details.id_structure','groups.id_groups','groups.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->get();
        // dd($group);
         $tasksub = DB::table('rabs')
            ->select('structure_details.id_structure','group_details.id_groups','task_subs.id_sub','task_subs.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->get();
        // dd($tasksub);
        $alphabet=[];
        foreach(range('A','Z') as $alphabet_data)
        {
            array_push($alphabet,$alphabet_data);
        }

        $index = count($group);
        $roman=[];
        for($i=1;$i<=$index;$i++)
        {
            $data = $this->integerToRoman($i);
            array_push($roman,$data);
        }
        if($type==1)
        {
            $temp=[];
            foreach($structure as $structure_data)
            {
                foreach($group as $group_data)
                {
                    if($structure_data->id_structure == $group_data->id_structure)
                    {
                        foreach($tasksub as $tasksub_data)
                        {
                            if($structure_data->id_structure == $tasksub_data->id_structure)
                            {
                                if($group_data->id_groups == $tasksub_data->id_groups)
                                {
                                    foreach($rab as $rab_data)
                                    {
                                        if($structure_data->id_structure == $rab_data->id_structure)
                                        {
                                            if($group_data->id_groups == $rab_data->id_groups) 
                                            {
                                                if($tasksub_data->id_sub == $rab_data->id_sub)
                                                    array_push($temp,$rab_data);
                                            }
                                        }
                                    }
                                }
                            }
                            
                        }
                    }
                }
            }
            $pdf = PDF::loadView('rab_mr_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                        'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman,
                        'temp'=>$temp,'newDate'=>$newDate]);
            $pdf->setPaper('A4','potrait');
            return $pdf->stream();
        }
        else if($type==2)
        {
            $temp=[];
            foreach($structure as $structure_data)
            {
                foreach($rab as $rab_data)
                {
                    if($structure_data->id_structure == $rab_data->id_structure)
                    {
                        array_push($temp,$rab_data);
                    }
                }
            }
            // dd($temp);
            $pdf = PDF::loadView('rab_mr_building_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                        'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman,
                        'temp'=>$temp,'newDate'=>$newDate]);
            $pdf->setPaper('A4','potrait');
            return $pdf->stream();
        }
        else 
        {
            $temp=[];
            foreach($structure as $structure_data)
            {
                foreach($group as $group_data)
                {
                    if($structure_data->id_structure == $group_data->id_structure)
                    {
                        foreach($rab as $rab_data)
                        {
                            if($structure_data->id_structure == $rab_data->id_structure)
                            {
                                if($group_data->id_groups == $rab_data->id_groups) 
                                {
                                        array_push($temp,$rab_data);
                                }
                            }
                        }
                    }
                }
            }
            // dd($temp);
            $pdf = PDF::loadView('rab_mr_floor_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                        'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman,
                        'temp'=>$temp,'newDate'=>$newDate]);
            $pdf->setPaper('A4','potrait');
            return $pdf->stream();
        }
    }

    public function rap($id,$rap)
    {
        if($rap == 0)
            $rap = 1;
        else
            $rap = $rap/100;

        $rab = DB::table('rabs')
            ->select('projects.name','projects.owner','projects.date','projects.address', 'projects.type','rabs.total_rab',
                        'task_subs.id_sub','groups.id_groups','structures.id_structure','ahs_lokals.adjustment',
                        'ahs_lokals.volume','ahs_lokals.HSP',
                        'ahs_lokals.HP_Adjust','jobs.name as job', 'satuans.name as satuan','jobs.status',
                        'task_sub_details.id_sub_details','ahs_lokals.id_ahs_lokal', 
                        'ahs_lokals.id_sub_details')
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->join('structures','structures.id_structure','=','structure_details.id_structure')
            ->join('satuans','satuans.id_satuan','=','jobs.id_satuan')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->whereNull('ahs_lokals.deleted_at')
            ->get();
        // dd($rab);
        $newDate = $this->tanggal_ind($rab[0]->date);
        $totalSt = DB::table('rabs')
            ->select(DB::raw('SUM(ahs_lokals.HP_Adjust) as Total, structure_details.id_structure'))
            ->join('projects','projects.id_project','=','rabs.id_project')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('ahs_lokals','ahs_lokals.id_sub_details','=','task_sub_details.id_sub_details')
            ->join('jobs','jobs.id_job','=','ahs_lokals.id_job')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id) 
            ->whereNull('ahs_lokals.deleted_at')
            ->groupBy('structure_details.id_structure')
            ->get();
        // dd($totalSt);
        foreach($totalSt as $total)
        {
            $total->Total = $total->Total * $rap;
        }
        // dd($totalSt);
        $structure = DB::table('rabs')
            ->select('structures.id_structure','structures.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('structures','structure_details.id_structure','=','structures.id_structure')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->get();
        // dd($structure);
        $group = DB::table('rabs')
            ->select('structure_details.id_structure','groups.id_groups','groups.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('groups','groups.id_groups','=','group_details.id_groups')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->get();
        // dd($group);
        $tasksub = DB::table('rabs')
            ->select('structure_details.id_structure','group_details.id_groups','task_subs.id_sub','task_subs.name')
            ->join('structure_details','rabs.id_rab','=','structure_details.id_rab')
            ->join('group_details','group_details.id_structure_details','=','structure_details.id_structure_details')
            ->join('task_sub_details','task_sub_details.id_group_details','=','group_details.id_group_details')
            ->join('task_subs','task_subs.id_sub','=','task_sub_details.id_sub')
            ->where('rabs.id_rab','=',$id)
            ->whereNull('rabs.deleted_at')
            ->whereNull('structure_details.deleted_at')
            ->whereNull('group_details.deleted_at')
            ->whereNull('task_sub_details.deleted_at')
            ->get();
        // dd($tasksub);
        $alphabet=[];
        foreach(range('A','Z') as $alphabet_data)
        {
            array_push($alphabet,$alphabet_data);
        }
        $index = count($group);
        $roman=[];
        for($i=1;$i<=$index;$i++)
        {
            $data = $this->integerToRoman($i);
            array_push($roman,$data);
        }
        // dd($roman);
        $pdf = PDF::loadView('rab_rap_report',['rab' => $rab,'structure'=>$structure,'group'=>$group,
                            'tasksub'=>$tasksub, 'alphabet'=>$alphabet,'roman'=>$roman,
                            'totalSt'=>$totalSt,'rap'=>$rap,'newDate'=>$newDate]);
        $pdf->setPaper('A4','potrait');
        return $pdf->stream();
    }
}
