<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\DetailKasirRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UploadPotLoginRequest;
use App\Http\Requests\UploadPotRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use XBase\TableReader;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class UploadPotController extends Controller
{

    protected $toko;

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $this->formLoad();

        //!harus login dulu sebelum upload pb pot
        //*error message -> USERNAME ATAU PASSWORD SALAH
        return view('upload-pot');
    }

    //! PROSES F3
    public function readDbf(Request $request){
        $validator = validator($request->all(), [
            'files.*' => 'file|mimes:dbf', // Adjust max file size if needed
        ], [
            'files.*.mimes' => 'Invalid file type. Please upload .dbf files.',
        ]);

        if ($validator->fails()) {
            return [
                'code' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        $files = $request->file('files');

        if (!$files || empty($files)) {
            return [
                'code' => 300,
                'message' => "Files Tidak Ditemukan!"
            ];
        }

        DB::beginTransaction();
        try{
            // Get the path to the uploaded file
            $records = [];
            foreach($files as $file){
                $filePath = $file->getRealPath();
                $fileName = $file->getClientOriginalName();
                // Open the dBASE file using TableReader
                $table = new TableReader($filePath);

                //* VARIABLE
                $KodeToko = substr($fileName,3,4);
                $ip = $this->getIP();

                while ($record = $table->nextRecord()) {

                    // $total_rupiah = ($record->total === "" || $record->total === null) ? 0 : $record->total;
                    // $records[] = [
                    //     'no_pb' => $record->docno,
                    //     'tgl_pb' => $record->tanggal,
                    //     'toko' => $record->prdcd,
                    //     'item' => $record->qty,
                    //     'rupiah' => $total_rupiah,
                    //     'nama_file' => $fileName,
                    // ];

                    //! PROSES F3 PINDAH SINI DULU

                    $noPB = $record->docno;
                    $PasswordOK = False;

                    //! dummy
                    // $kodeigr = session('KODECABANG');
                    $kodeigr = '22';

                    // sb.AppendLine("Select TKO_KodeCustomer ")
                    // sb.AppendLine("From tbMaster_tokoIGR ")
                    // sb.AppendLine("Where TKO_KodeIGR = '" & KDIGR & "' ")
                    // sb.AppendLine("And TKO_KodeOMI = '" & KodeToko & "' ")
                    // sb.AppendLine("And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

                    $data = DB::table('tbmaster_tokoigr')
                        ->where([
                            'tko_kodeigr' => $kodeigr,
                            'tko_kodeomi' => $KodeToko,
                        ])
                        ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                        ->count();

                    // if(jum == 0){
                        //* ERROR MESSAGE -> KODE TOKO " & KodeToko & " TIDAK TERDAFTAR DI MASTER CUSTOMER !
                        // goto skipFilePBSL;
                    // }

                    if($data == 0){
                        $message = "KODE TOKO " . $KodeToko . " TIDAK TERDAFTAR DI MASTER CUSTOMER !";
                        return ApiFormatter::error(400, $message);
                    }

                    //! DELETE
                    // DELETE FROM DBF_PBA WHERE IP = '" & IP & "' ")
                    DB::table('dbf_pba')
                        ->where('ip', $ip)
                        ->delete();

                    //! LOOPING INSERT FROM DATA DBF
                    DB::table('dbf_pba')
                        ->insert([
                            'kodetoko' => $KodeToko,
                            'recid' => $record->recid,
                            'docno' => $record->docno,
                            'tanggal' => $record->tanggal,
                            'prdcd' => $record->prdcd,
                            'stok' => isset($record->stok) ? $record->stok : 0,
                            'sisa_stok' => isset($record->sisa_stok) ? $record->sisa_stok : 0,
                            'leadtime' => isset($record->leadtime) ? $record->leadtime : 0,
                            'buffer' => isset($record->buffer) ? $record->buffer : 0,
                            'qty' => isset($record->qty) ? $record->qty : 0,
                            'price' => isset($record->price) ? $record->price : 0,
                            'total' => isset($record->total) ? $record->total : 0,
                            'rumus' => isset($record->rumus) ? $record->rumus : 0,
                            'bs_rms' => $record->bs_rms,
                            'lt_rms' => $record->lt_rms,
                            'std' => isset($record->std) ? $record->std : 0,
                            'bs' => isset($record->bs) ? $record->bs : 0,
                            'lt' => isset($record->lt) ? $record->lt : 0,
                            'UPDATE' => $record->update,
                            'tglupload' => Carbon::now(),
                            'ip' => $this->getIP(),
                        ]);

                    //! KALAU ADA REVISI PB UNTUK HARI INI - HAPUS CSV_PB_BOT
                    // $this->DeleteCSVPOT(KodeToko);

                    $this->DeleteCSVPOT($KodeToko);

                    //! ISI CSV PB POT
                    // sb.AppendLine("INSERT INTO CSV_PB_POT ")
                    // sb.AppendLine("( ")
                    // sb.AppendLine("  CPP_KodeToko, ")
                    // sb.AppendLine("  CPP_NoPB, ")
                    // sb.AppendLine("  CPP_TglPB, ")
                    // sb.AppendLine("  CPP_PluIDM, ")
                    // sb.AppendLine("  CPP_QTY, ")
                    // sb.AppendLine("  CPP_GROSS, ")
                    // sb.AppendLine("  CPP_IP, ")
                    // sb.AppendLine("  CPP_FileName, ")
                    // sb.AppendLine("  CPP_TglProses,   ")
                    // sb.AppendLine("  CPP_CREATE_BY ")
                    // sb.AppendLine(") ")
                    // sb.AppendLine("SELECT KodeTOKO, ")
                    // sb.AppendLine("       DOCNO, ")
                    // sb.AppendLine("       TANGGAL, ")
                    // sb.AppendLine("       PRDCD, ")
                    // sb.AppendLine("       QTY, ")
                    // sb.AppendLine("       PRICE, ")
                    // sb.AppendLine("       '" & IP & "', ")
                    // sb.AppendLine("       '" & fi.Name & "',")
                    // sb.AppendLine("       CURRENT_DATE, ")
                    // sb.AppendLine("	      '" & UserID & "' ")
                    // sb.AppendLine("  FROM DBF_PBA")
                    // sb.AppendLine(" WHERE IP = '" & IP & "' ")
                    // sb.AppendLine("   AND TGLUPLOAD = CURRENT_DATE ")

                    //! ISI CSV PB POT
                    DB::table('csv_pb_pot')
                        ->insert([
                            'cpp_kodetoko' => $KodeToko,
                            'cpp_nopb' => $record->docno,
                            'cpp_tglpb' => $record->tanggal,
                            'cpp_pluidm' => $record->prdcd,
                            'cpp_qty' => isset($record->qty) ? $record->qty : 0,
                            'cpp_gross' => isset($record->price) ? $record->price : 0,
                            'cpp_ip' => $ip,
                            'cpp_filename' => $filePath,
                            'cpp_tglproses' => Carbon::now(),
                            'cpp_create_by' => session('userid'),
                        ]);

                    //! HANDLING
                    //! CEK ADA PLU DOBEL GA DI PBA NYA
                    $data = DB::select("
                        Select COALESCE(count(cpp_pluidm),0)
                        From csv_pb_pot
                        Where cpp_ip = '$ip'
                            AND cpp_kodetoko = '$KodeToko'
                            AND cpp_tglproses = CURRENT_DATE
                        Group By cpp_pluidm
                        Having count(cpp_pluidm) > 1
                    ");

                    // if(jum > 0){
                        //* ERROR MESSAGE -> Terdapat Dobel Record PLU Di File " & fi.Name & " Yang Sedang Diproses," & vbNewLine & "Harap Minta Revisi File PBA Ke IDM !
                        // $this->DeleteCSVPOT(KodeToko);
                        // goto skipFilePBSL
                    // }

                    if(count($data)){
                        $message = "Terdapat Dobel Record PLU Di File " . $fileName . " Yang Sedang Diproses, Harap Minta Revisi File PBA Ke IDM !";
                        return ApiFormatter::error(400, $message);
                    }

                    //! HITUNG CSV_PB_POT
                    // sb.AppendLine("Select COALESCE(Count(0),0) ")
                    // sb.AppendLine("  FROM CSV_PB_POT ")
                    // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
                    // sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
                    // sb.AppendLine("	AND CPP_TglProses = CURRENT_DATE ")

                    $data = DB::table('csv_pb_pot')
                        ->where([
                            'cpp_ip' => $ip,
                            'cpp_kodetoko' => $KodeToko,
                        ])
                        ->whereDate('cpp_tglproses', Carbon::now())
                        ->count();

                    // if(jum = 0){
                        //* ERROR MESSAGE -> TIDAK ADA DATA CSV_PB_POT YANG BISA JADI PB DI IGR!!
                        // $this->DeleteCSVPOT(KodeToko);
                        // goto skipFilePBSL
                    // }

                    if($data == 0){
                        $message = 'TIDAK ADA DATA CSV_PB_POT YANG BISA JADI PB DI IGR!!';
                        return ApiFormatter::error(400, $message);
                    }

                    //! HANDLING
                    // If ProsesKonversi(KodeToko, noPB) = False Then    PANGGIL FUNCTION
                    // MsgBox("PROSES CSV_PB_POT - KONVERSI PLUIDM- GAGAL", MsgBoxStyle.Information, ProgName)
                    // $this->DeleteCSVPOT(KodeToko);
                    // goto skipFilePBSL

                    //! MERGE CSV_PB_POT UPDATE AVGCOST+STOCK
                    // sb.AppendLine("UPDATE CSV_PB_POT A set  CPP_AVGCOST = COALESCE(B.ST_AVGCOST,0), ")
                    // sb.AppendLine("CPP_Stock = COALESCE(B.ST_SaldoAkhir, 0)")
                    // sb.AppendLine("from (")
                    // sb.AppendLine(" Select * ")
                    // sb.AppendLine("From tbMaster_Stock ")
                    // sb.AppendLine("Where ST_Lokasi = '01' ")
                    // sb.AppendLine(" ) B where A.CPP_PLUIGR = B.ST_PRDCD ")
                    // sb.AppendLine("    and A.CPP_IP = '" & IP & "' ")
                    // sb.AppendLine("    and A.CPP_KodeTOKO = '" & KodeToko & "'  ")
                    // sb.AppendLine("   AND A.CPP_TglProses = CURRENT_DATE ")

                    // if(jum = 0){
                        //* PROSES CSV_PB_POT - MERGE AVGCOST+STOCK - GAGAL
                        // $this->KurangiAkumulasiPB(KodeToko, noPB);
                        // $this->DeleteCSVPOT(KodeToko);
                        // goto skipFilePBSL
                    // }

                    try {
                        DB::select("
                            UPDATE csv_pb_pot A set cpp_avgcost = COALESCE(B.st_avgcost,0),
                            cpp_stock = COALESCE(B.st_saldoakhir, 0)
                            from (
                                Select *
                                From tbmaster_stock
                                Where st_lokasi = '01'
                            ) B
                            where A.cpp_pluigr = B.st_prdcd
                            and A.cpp_ip = '" . $ip . "'
                            and A.cpp_kodetoko = '" . $KodeToko . "'
                            AND A.cpp_tglproses = CURRENT_DATE
                        ");
                    } catch (\Exception $e) {
                        $this->KurangiAkumulasiPB($KodeToko, $noPB);
                        $this->DeleteCSVPOT($KodeToko);
                        $message = "PROSES CSV_PB_POT - MERGE AVGCOST+STOCK - GAGAL ($e)";
                        return ApiFormatter::error(400, $message);
                    }

                    //! MERGE CSV_PB_POT UPDATE STOCK_EKONOMIS
                    // sb.AppendLine("UPDATE CSV_PB_POT A SET CPP_Stock =  COALESCE(B.SEP_QTYEKONOMIS,0)")
                    // sb.AppendLine("from ( ")
                    // sb.AppendLine("Select * ")
                    // sb.AppendLine("From stock_ekonomis_pot ")
                    // sb.AppendLine(" Where sep_toko = '" & KodeToko & "' ")
                    // sb.AppendLine("  AND sep_nopb = '" & noPB & "' ")
                    // sb.AppendLine("  AND sep_ip = '" & IP & "' ")
                    // sb.AppendLine(") B ")
                    // sb.AppendLine(" where A.CPP_PLUIGR = B.SEP_PLUIGR ")
                    // sb.AppendLine("and A.CPP_IP = '" & IP & "' ")
                    // sb.AppendLine("AND A.CPP_KodeTOKO = '" & KodeToko & "' ")
                    // sb.AppendLine("AND A.CPP_TglProses = CURRENT_DATE ")

                    // if(jum = 0){
                        //* PROSES CSV_PB_POT - MERGE STOCK_EKONOMIS - GAGAL
                        // $this->KurangiAkumulasiPB(KodeToko, noPB);
                        // $this->DeleteCSVPOT(KodeToko);
                        // goto skipFilePBSL
                    // }

                    try {
                        DB::select("
                            UPDATE csv_pb_pot A SET cpp_stock =  COALESCE(B.sep_qtyekonomis,0)
                            from (
                            Select *
                            From stock_ekonomis_pot
                            Where sep_toko = '" . $KodeToko . "'
                            AND sep_nopb = '" . $noPB . "'
                            AND sep_ip = '" . $ip . "' ) B
                            where A.cpp_pluigr = B.sep_pluigr
                            and A.cpp_ip = '" . $ip . "'
                            AND A.cpp_kodetoko = '" . $KodeToko . "'
                            AND A.cpp_tglproses = CURRENT_DATE
                        ");

                    } catch (\Exception $e) {

                        DB::rollBack();

                        $this->KurangiAkumulasiPB($KodeToko, $noPB);
                        $this->DeleteCSVPOT($KodeToko);
                        $message = "PROSES CSV_PB_POT - MERGE STOCK_EKONOMIS - GAGAL ($e)";
                        return ApiFormatter::error(400, $message);
                    }

                    // sb.AppendLine("SELECT COALESCE(Count(DISTINCT CPP_NoPB),0)  ")
                    // sb.AppendLine("  FROM CSV_PB_POT ")
                    // sb.AppendLine(" WHERE EXISTS ")
                    // sb.AppendLine(" ( ")
                    // sb.AppendLine("    SELECT pbo_nopb ")
                    // sb.AppendLine("      FROM TBMASTER_PBOMI ")
                    // sb.AppendLine("     WHERE PBO_NOPB = CPP_NoPB ")
                    // sb.AppendLine("       AND PBO_KODEOMI = CPP_KodeToko ")
                    // sb.AppendLine("       AND PBO_TGLPB = CPP_TglPB ")
                    // sb.AppendLine(" ) ")
                    // sb.AppendLine("   AND CPP_IP = '" & IP & "' ")
                    // sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
                    // sb.AppendLine("	AND CPP_TglProses = CURRENT_DATE ")

                    // if(jum > 0){
                        //* ERROR MESSAGE -> No Dokumen:" & noPB & vbNewLine & "Toko:" & KodeToko & vbNewLine & "File:" & fi.Name & vbNewLine & vbNewLine & "SUDAH PERNAH DIPROSES!!!"
                        // $this->KurangiAkumulasiPB(KodeToko, noPB);
                        // $this->DeleteCSVPOT(KodeToko);
                        // goto skipFilePBSL
                    // }

                    try {
                        DB::select("
                        SELECT COALESCE(COUNT(DISTINCT cpp_nopb),0)
                            FROM csv_pb_pot
                        WHERE EXISTS
                        (
                            SELECT pbo_nopb
                                FROM tbmaster_pbomi
                            WHERE pbo_nopb = cpp_nopb
                                AND pbo_kodeomi = cpp_kodetoko
                                AND pbo_tglpb = cpp_tglpb
                        )
                            AND cpp_ip = '" . $ip . "'
                            AND cpp_kodetoko = '" . $KodeToko . "'
                            AND cpp_tglproses = CURRENT_DATE
                        ");
                    } catch (\Exception $e) {

                        DB::rollBack();

                        $message = "No Dokumen:$noPB Toko:" . $KodeToko . "File:" . $fileName . "SUDAH PERNAH DIPROSES!!!"; //! nama_file belum
                        return ApiFormatter::error(400, $message);
                    }

                    skipFilePBSL:
                }
            }

            DB::commit();

            return ApiFormatter::success(200, 'Proses tarik data berhasil');

        }catch(\Exception $e){

            DB::rollBack();

            $message = "Oops terjadi kesalahan ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }

    public function datatablesHead(){

        $ip = $this->getIP();

        //! DELETE TEMP_CSV_PB_POT
        // sb.AppendLine("DELETE FROM TEMP_CSV_PB_POT ")
        // sb.AppendLine(" WHERE IP = '" & IP & "' ")

        DB::table('temp_csv_pb_pot')
            ->where('ip', $ip)
            ->delete();

        //! INSERT INTO TEMP_CSV_PB_POT
        // sb.AppendLine("INSERT INTO TEMP_CSV_PB_POT ")
        // sb.AppendLine("( ")
        // sb.AppendLine("       NOPB, ")
        // sb.AppendLine("       TGLPB, ")
        // sb.AppendLine("       TOKO, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       QTY, ")
        // sb.AppendLine("       NAMA_FILE, ")
        // sb.AppendLine("       STOCK, ")
        // sb.AppendLine("       IP ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select CPP_NOPB as NOPB, ")
        // sb.AppendLine("       TO_CHAR(CPP_TglPB,'DD-MM-YYYY') as TGLPB, ")
        // sb.AppendLine("       CPP_KodeToko as TOKO, ")
        // sb.AppendLine("       CPP_PLUIGR as PLUIGR, ")
        // sb.AppendLine("       CPP_Qty as QTY, ")
        // sb.AppendLine("       CPP_FileName, ")
        // sb.AppendLine("       CPP_Stock, ")
        // sb.AppendLine("       CPP_IP ")
        // sb.AppendLine("  From CSV_PB_POT ")
        // sb.AppendLine(" Where CPP_TGLPROSES >= CURRENT_DATE - 7 ")
        // sb.AppendLine("   And CPP_FLAG IS NULL ")
        // sb.AppendLine("   And CPP_IP = '" & IP & "' ")

        DB::select("
            INSERT INTO temp_csv_pb_pot
            (
                nopb,
                tglpb,
                toko,
                pluigr,
                qty,
                nama_file,
                stock,
                ip
            )
            Select cpp_nopb as nopb,
                TO_CHAR(cpp_tglpb,'DD-MM-YYYY') as tglpb,
                cpp_kodetoko as toko,
                cpp_pluigr as pluigr,
                cpp_qty as qty,
                cpp_filename,
                cpp_stock,
                cpp_ip
            From csv_pb_pot
            Where cpp_tglproses >= CURRENT_DATE - 7
            And cpp_flag IS NULL
            And cpp_ip = '" . $ip . "'
        ");

        //! ISI DATAGRID HEADER PB IDM
        // sb.AppendLine("WITH A  ")
        // sb.AppendLine("AS  ")
        // sb.AppendLine("(  ")
        // sb.AppendLine("Select NOPB,   ")
        // sb.AppendLine("       TGLPB,   ")
        // sb.AppendLine("       TOKO,   ")
        // sb.AppendLine("       coalesce(COUNT(PLUIGR),0) as ITEM,  ")
        // sb.AppendLine("       ROUND(sum(coalesce(QTY::numeric,0) / CASE WHEN PRD_UNIT = 'KG' THEN PRD_FRAC ELSE 1 end * coalesce(ST_AVGCOST::numeric,0)),2) as RUPIAH, ")
        // sb.AppendLine("       NAMA_FILE   ")
        // sb.AppendLine("  From TEMP_CSV_PB_POT,  ")
        // sb.AppendLine("       tbMaster_Stock,  ")
        // sb.AppendLine("       tbMaster_Prodmast  ")
        // sb.AppendLine(" Where ST_PRDCD = PLUIGR  ")
        // sb.AppendLine("   And IP = '" & IP & "'  ")
        // sb.AppendLine("   And ST_LOKASI = '01'  ")
        // sb.AppendLine("   AND PRD_PRDCD = ST_PRDCD  ")
        // sb.AppendLine("   AND PRD_PRDCD = PLUIGR  ")
        // sb.AppendLine(" Group By NOPB,TGLPB,TOKO,NAMA_FILE  ")
        // sb.AppendLine(" ) SELECT NOPB,  ")
        // sb.AppendLine("          TGLPB,  ")
        // sb.AppendLine("          TOKO,  ")
        // sb.AppendLine("          Replace(REPLACE(TO_CHAR(Sum(ITEM),'999G999G999G999'),',','.'),' ','') AS ITEM,  ")
        // sb.AppendLine("          Replace(REPLACE(TO_CHAR(Sum(RUPIAH),'999G999G999G999'),',','.'),' ','') AS RUPIAH,  ")
        // sb.AppendLine("          NAMA_FILE ")
        // sb.AppendLine("     FROM A  ")
        // sb.AppendLine("    GROUP BY NOPB,  ")
        // sb.AppendLine("          TGLPB,  ")
        // sb.AppendLine("          TOKO,  ")
        // sb.AppendLine("          NAMA_FILE  ")
        // sb.AppendLine("    ORDER BY TOKO ")

        $data = DB::select("
            WITH A
            AS
            (
                Select nopb,
                    tglpb,
                    toko,
                    coalesce(COUNT(pluigr),0) as item,
                    ROUND(sum(coalesce(QTY::numeric,0) / CASE WHEN prd_unit = 'KG' THEN prd_frac ELSE 1 end * coalesce(st_avgcost::numeric,0)),2) as rupiah,
                    nama_file
                From temp_csv_pb_pot,
                    tbmaster_stock,
                    tbmaster_prodmast
                Where st_prdcd = pluigr
                And st_lokasi = '01'
                AND prd_prdcd = st_prdcd
                AND prd_prdcd = pluigr
                Group By nopb,tglpb,toko,nama_file
            )
            SELECT nopb,
                tglpb,
                toko,
                Replace(REPLACE(TO_CHAR(Sum(item),'999G999G999G999'),',','.'),' ','') AS item,
                Replace(REPLACE(TO_CHAR(Sum(rupiah),'999G999G999G999'),',','.'),' ','') AS rupiah,
                NAMA_FILE
            FROM A
            GROUP BY nopb,
                tglpb,
                toko,
                nama_file
            ORDER BY toko
        ");

        return DataTables::of($data)
            ->make(true);
    }

    public function datatablesDetail($toko){
        $ip = $this->getIP();

        //! SELECTED DI GRID HEADER -> dgvHeader
        //! ISI DETAIL PB
        // sb.AppendLine("Select PLUIGR as PLU, ")
        // sb.AppendLine("       PRD_DeskripsiPanjang as DESK, ")
        // sb.AppendLine("       QTY, ")
        // sb.AppendLine("       STOCK ")
        // sb.AppendLine("  From temp_csv_pb_POT, ")
        // sb.AppendLine("       tbMaster_Prodmast ")
        // sb.AppendLine(" Where prd_prdcd = pluigr  ")
        // sb.AppendLine("   And TOKO = '" & dgvHeader.CurrentRow.Cells(2).Value & "' ")
        // sb.AppendLine("   AND IP = '" & IP & "' ")
        // sb.AppendLine(" Order By QTY ")

        $data = DB::table('temp_csv_pb_pot')
            ->selectRaw("
                pluigr as plu,
                prd_deskripsipanjang as desk,
                qty,
                stock
            ")
            ->join('tbmaster_prodmast',function($join){
                $join->on('prd_prdcd','=','pluigr');
            })
            ->where([
                // 'ip' => $ip,
                'toko' => $toko,
            ])
            ->orderBy('qty')
            ->get();

        return DataTables::of($data)
            ->make(true);
    }

    public function actionLogin(UploadPotLoginRequest $request){
        // sb.AppendLine("Select coalesce(Count(1),0) ")
        // sb.AppendLine("  From tbmaster_user ")
        // sb.AppendLine(" Where kodeigr = '" & KDIGR & "' ")
        // sb.AppendLine("   And userid = '" & Replace(txtUser.Text, "'", "") & "' ")
        // sb.AppendLine("   And userpassword = '" & Replace(txtPassword.Text, "'", "") & "'  ")

        
        $data = DB::table('tbmaster_user')
        ->where([
            'kodeigr' => session('KODECABANG'),
                'userid' => $request->user,
                'userpassword' => $request->password,
            ])->first();

        if(empty($data)){
            $message = 'Username atau Password Salah!!';
            return ApiFormatter::error(400, $message);
        }
        
        return ApiFormatter::success(200, "Login Berhasil..!");
    }

    //? FLOW
    //? 1.	PILIH FILE DBF DI DERECTORY YANG TELAH DI SIMPAN
    //? 2.	F3 UNTUK TARIK DATA SEHINGGA TAMPIL DI GRID (HEADER DAN DETAIL)
    //? 3.	F8 UNTUK PROSES UPLOAD DATA

    public function uploadPot(){ #PROSES F8

        // Download Dummy PDF zip
        $pdfs = [];

        // Generate PDFs
        $pdfs['order_ditolak.pdf'] = PDF::loadView('pdf.order-ditolak')->output();
        $pdfs['rekap_order.pdf'] = PDF::loadView('pdf.rekap-order')->output();
        $pdfs['cetakan_kertas.pdf'] = PDF::loadView('pdf.cetakan-kertas')->output();
        $pdfs['list_order.pdf'] = PDF::loadView('pdf.list-order')->output();
        $pdfs['karton_non_dpd.pdf'] = PDF::loadView('pdf.karton-non-dpd')->output();

        $zipFileName = 'kode-toko.zip';
        $zip = new ZipArchive();
        $zip->open($zipFileName, ZipArchive::CREATE);

        foreach ($pdfs as $filename => $pdfContent) {
            $zip->addFromString($filename, $pdfContent);
        }

        $zip->close();

        Storage::disk('local')->put($zipFileName, file_get_contents($zipFileName));

        return response()->download(storage_path("app/{$zipFileName}"))->deleteFileAfterSend();
        die;
    


        //! HANDLING HANYA BISA JAM 12 MALAM
        //* ERROR MESSAGE->Mohon Tunggu Sampai JAM 12 MALAM

        //If dgvHeader.RowCount > 0 And dgvDetail.RowCount > 0

        // noPB = dgvHeader.CurrentRow.Cells(0).Value
        // tglPB = dgvHeader.CurrentRow.Cells(1).Value
        // KodeToko = dgvHeader.CurrentRow.Cells(2).Value

        $ip = $this->getIP();
        $noPB = 'TZ4Z133';
        $KodeToko = 'TZ4Z';
        $tglPB = '10-10-2023';

        $namaFile = 'PBATZ4Z.DBF';

        $this->ProsesPBIDM();

        dd('lolos');

        // if(adaProses){
            //! SET FLAG CSV_PB_IDM
            // sb.AppendLine("UPDATE CSV_PB_POT ")
            // sb.AppendLine("   SET CPP_FLAG = '1' ")
            // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
            // sb.AppendLine("   AND CPP_noPB = '" & noPB & "' ")
            // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
            // sb.AppendLine("   AND CPP_TglPB = TO_DATE('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND CPP_FLAG IS NULL ")

            DB::table('csv_pb_pot')
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_nopb' =>  $noPB,
                    'cpp_kodetoko' =>  $KodeToko
                ])
                ->whereRaw("cpp_tglpb = TO_DATE('" . $tglPB . "','DD-MM-YYYY')")
                ->whereNull('cpp_flag')
                ->update([
                    'cpp_flag' => '1'
                ]);

            //$this->RefreshGridHeader()

            // return PROSES UPLOAD PB IDM - POT SELESAI DILAKUKAN !",
        // }
    }

    private function formLoad(){

        $ip = $this->getIP();
        $kodeigr = session('KODECABANG');

        //! CHECK MASTER_SUPPLY_IDM
        // sb.AppendLine("  SELECT COUNT(DISTINCT msi_kodedc)  ")
        // sb.AppendLine("  FROM master_supply_idm  ")
        // sb.AppendLine("  WHERE msi_kodeigr = '" & KDIGR & "' ")

        $data = DB::table('master_supply_idm')
            ->where('msi_kodeigr', $kodeigr)
            ->count();

        if($data > 0){
            //! CHECK TBMASTER_PLUIDM
            // sb.AppendLine(" SELECT idm_kodeidm, COUNT(DISTINCT idm_pluidm) jml_pluidm ")
            // sb.AppendLine(" FROM tbmaster_pluidm  ")
            // sb.AppendLine(" WHERE idm_kodeigr = '" & KDIGR & "' ")
            // sb.AppendLine(" AND EXISTS ( ")
            // sb.AppendLine(" SELECT msi_kodedc  ")
            // sb.AppendLine(" FROM master_supply_idm  ")
            // sb.AppendLine(" WHERE msi_kodedc = idm_kodeidm ")
            // sb.AppendLine(" AND msi_kodeigr = idm_kodeigr ")
            // sb.AppendLine(" ) ")
            // sb.AppendLine(" GROUP BY idm_kodeidm ")
            // sb.AppendLine(" HAVING COUNT(DISTINCT idm_pluidm) > 0 ")
        }

        DB::select("
            SELECT idm_kodeidm, COUNT(DISTINCT idm_pluidm) jml_pluidm
            FROM tbmaster_pluidm
            WHERE idm_kodeigr = '" . $kodeigr . "'
            AND EXISTS (
                SELECT msi_kodedc
                FROM master_supply_idm
                WHERE msi_kodedc = idm_kodeidm
                AND msi_kodeigr = idm_kodeigr
            )
            GROUP BY idm_kodeidm
            HAVING COUNT(DISTINCT idm_pluidm) > 0
        ");

        // If dtCek.Rows.Count > 0 Then
        //     flagPLUIDM = IIf(jum = dtCek.Rows.Count, True, False)
        // Else
        //     flagPLUIDM = False
        // End If
        // Else
        //     lagPLUIDM = False
        // End If

        //! DELETE DATA STOCK_EKONOMIS_POT
        // DELETE FROM STOCK_EKONOMIS_POT WHERE SEP_CREATE_DT < CURRENT_DATE

        DB::table('stock_ekonomis_pot')
            ->whereDate('sep_create_dt','<', Carbon::now())
            ->delete();


        //! DELETE DATA STOCK_AKUMULASIPB_POT
        // DELETE FROM STOCK_AKUMULASIPB_POT WHERE SAP_TGLUPD < CURRENT_DATE

        DB::table('stock_akumulasipb_pot')
            ->whereDate('sap_tglupd','<', Carbon::now())
            ->delete();
    }


}
