<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\DetailKasirRequest;
use App\Http\Requests\KonversiPluSaveRequest;
use App\Http\Requests\TableRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KonversiPluController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){
        return view('home');
    }

    public function detail($plu){
        $data = DB::table("tbmaster_prodmast")
            ->select("prd_deskripsipanjang")
            ->where("prd_prdcd", $plu)
            ->first();

        if(empty($data)){
            return ApiFormatter::error(400, 'Data tidak ditemukan');
        }

        $message = 'Data detail berhasil ditampilkan';
        return ApiFormatter::success(200,$message, $data);
    }

    public function datatables(){
        $data = DB::table("konversi_atk")
            ->select("kat_pluidm", "kat_pluigr", "kat_deskripsi", "kat_flagaktif")
            ->orderBy("kat_pluidm")
            ->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function actionSave(KonversiPluSaveRequest $request){

        DB::beginTransaction();
	    try{

            //! CEK DI TABLE KONVERSI ATK UNTUK PLU DAN IGR YANG DI PILIH APAKAH SUDAH ADA
            //! CEK SUDAH ADA BELUM PLUNYA

            $data = DB::table('konversi_atk')
                ->where([
                    'kat_pluidm' => $request->kat_pluidm,
                    'kat_pluigr' => $request->kat_pluigr,
                ])->count();

            if($data > 0){

                //! CEK SUDAH ADA YG AKTIF??
                $data = DB::table('konversi_atk')
                ->where([
                    'kat_pluidm' => $request->kat_pluidm,
                ])
                ->where('kat_pluigr', '!=', $request->kat_pluigr)
                ->where('kat_flagaktif','>',0)
                ->count();

                if($data > 0){
                    $message = "PLU IDM : " . $request->kat_pluidm . " Sudah Mempunyai PLU IGR Yang Aktif !";
                    return ApiFormatter::error(400, $message);

                }else{

                    //! UPDATE KONVERSI_ATK
                    DB::table('konversi_atk')
                        ->where('kat_pluidm', '=', $request->kat_pluidm)
                        ->where('kat_pluigr', '=', $request->kat_pluigr)
                        ->update([
                            'kat_deskripsi' => $request->description,
                            'kat_flagaktif' => $request->flag_aktif == 1 ? 1 : 0,
                            'kat_modify_by' => session('userid'),
                            'kat_modify_dt' => now(), // Assuming you want to set the modification date to the current timestamp
                        ]);

                    DB::commit();

                    $message = 'Data Berhasil Diupdate !!';
                    return ApiFormatter::success(200, $message);
                }
            }

            //! CREATE KONVERSI_ATK
            DB::table('konversi_atk')->insert([
                'kat_pluidm' => $request->kat_pluidm,
                'kat_pluigr' => $request->kat_pluigr,
                'kat_deskripsi' => $request->description,
                'kat_flagaktif' => $request->flag_aktif == 1 ? 1 : 0,
                'kat_create_by' => session('userid'),
                'kat_create_dt' => now(), // Assuming you want to set the creation date to the current timestamp
            ]);

            DB::commit();

            $message = 'Data Berhasil disimpan !!';
            return ApiFormatter::success(200, $message);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }

    //* =======ANOTHER FUNCTION=========

    //! di double click akan menmpel plu igr dan deskripsi
    public function helpIgr(){
        $data = DB::table("tbmaster_prodmast")
            ->selectRaw("prd_prdcd as pluigr, prd_deskripsipanjang as desk")
            ->orderBy("prd_deskripsipanjang")
            ->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
}
