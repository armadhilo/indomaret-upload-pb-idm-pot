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
use Symfony\Component\HttpFoundation\Response;

class UploadPotController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        //!harus login dulu sebelum upload pb pot
        //*error message -> USERNAME ATAU PASSWORD SALAH
        return view('upload-pot');
    }

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
        // Get the path to the uploaded file
        $records = [];
        foreach($files as $file){
            $filePath = $file->getRealPath();
            $fileName = $file->getClientOriginalName();
            // Open the dBASE file using TableReader
            $table = new TableReader($filePath);
            while ($record = $table->nextRecord()) {
                $total_rupiah = ($record->total === "" || $record->total === null) ? 0 : $record->total;
                $records[] = [
                    'no_pb' => $record->docno,
                    'tgl_pb' => $record->tanggal,
                    'toko' => $record->prdcd,
                    'item' => $record->qty,
                    'rupiah' => $total_rupiah,
                    'nama_file' => $fileName,
                ];
            }
        }

        return ApiFormatter::success(200,"success", $records);
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

        return view('upload-pot');
    }

    public function actionUpload(Request $request){

        $ip = $this->getIP();

        //! CHECK MASTER_SUPPLY_IDM
        // sb.AppendLine("  SELECT COUNT(DISTINCT msi_kodedc)  ")
        // sb.AppendLine("  FROM master_supply_idm  ")
        // sb.AppendLine("  WHERE msi_kodeigr = '" & KDIGR & "' ")

        $data = DB::table('master_supply_idm')
            ->where('msi_kodeigr', $request->kdigr)
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
            WHERE idm_kodeigr = '" . $request->kdigr . "'
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

        DB::table('STOCK_EKONOMIS_POT')
            ->whereDate('SEP_CREATE_DT','<', Carbon::now())
            ->delete();


        //! DELETE DATA STOCK_AKUMULASIPB_POT
        // DELETE FROM STOCK_AKUMULASIPB_POT WHERE SAP_TGLUPD < CURRENT_DATE

        DB::table('STOCK_AKUMULASIPB_POT')
            ->whereDate('SAP_TGLUPD','<', Carbon::now())
            ->delete();
    }

    //? FLOW
    //? 1.	PILIH FILE DBF DI DERECTORY YANG TELAH DI SIMPAN
    //? 2.	F3 UNTUK TARIK DATA SEHINGGA TAMPIL DI GRID (HEADER DAN DETAIL)
    //? 3.	F8 UNTUK PROSES UPLOAD DATA

    public function selectedDataTable(Request $request){

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

        DB::select("
            Select PLUIGR as PLU,
                    PRD_DeskripsiPanjang as DESK,
                    QTY,
                    STOCK
            From temp_csv_pb_POT,
                    tbMaster_Prodmast
            Where prd_prdcd = pluigr
                And TOKO = '" . $request->toko . "'
                AND IP = '" . $ip . "'
            Order By QTY
        ");

    }

    public function tarikData(Request $request){ #PROSES F3

        $ip = $this->getIP();

        //! jika file path kosong
        #Path File PBAT Kosong!",

        $fullPath = "";
        $KodeToko = "";
        $noPB = "";
        $PasswordOK = False;

        // sb.AppendLine("Select TKO_KodeCustomer ")
        // sb.AppendLine("From tbMaster_tokoIGR ")
        // sb.AppendLine("Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data = DB::table('tbMaster_tokoIGR')
            ->where([
                'TKO_KodeIGR' => $request->KDIGR,
                'TKO_KodeOMI' => $request->KodeToko,
            ])
            ->whereRaw("COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->count();

        // if(jum == 0){
            //* ERROR MESSAGE -> KODE TOKO " & KodeToko & " TIDAK TERDAFTAR DI MASTER CUSTOMER !
            // goto skipFilePBSL;
        // }

        if($data == 0){
            $message = "KODE TOKO " . $request->KodeToko . " TIDAK TERDAFTAR DI MASTER CUSTOMER !";
            return ApiFormatter::error(400, $message);
        }

        //! BACA DBF
        // sb.AppendLine("Select '" & KodeToko & "'  as KodeToko, ")
        // sb.AppendLine(" RECID, ")
        // sb.AppendLine(" DOCNO, ")
        // sb.AppendLine(" CDATE(TANGGAL), ")
        // sb.AppendLine(" PRDCD, ")
        // sb.AppendLine(" IIf(STOK Is Null,0, STOK) AS STOK, ")
        // sb.AppendLine(" IIf(SISA_STOK Is Null,0,SISA_STOK) AS SISA_STOK, ")
        // sb.AppendLine(" IIf(LEADTIME Is Null,0, LEADTIME) AS LEADTIME, ")
        // sb.AppendLine(" IIf(BUFFER Is Null,0,BUFFER) AS BUFFER, ")
        // sb.AppendLine(" IIf(QTY Is Null ,0, QTY) AS QTY, ")
        // sb.AppendLine(" IIf(PRICE Is Null ,0, PRICE) AS PRICE, ")
        // sb.AppendLine(" IIf(TOTAL Is Null ,0, TOTAL) AS TOTAL, ")
        // sb.AppendLine(" IIf(RUMUS Is Null ,0, RUMUS) AS RUMUS, ")
        // sb.AppendLine(" BS_RMS, ")
        // sb.AppendLine(" LT_RMS, ")
        // sb.AppendLine(" IIf(STD Is Null ,0, STD) AS STD, ")
        // sb.AppendLine(" IIf(BS Is Null,0, BS) AS BS, ")
        // sb.AppendLine(" IIf(LT Is Null,0, LT) AS LT, ")
        // sb.AppendLine(" CDATE(UPDATE), ")
        // sb.AppendLine(" DATE() as TGLUPLOAD, ")
        // sb.AppendLine(" '" & IP & "' as IP ")
        // sb.AppendLine("  From " & Strings.Left(fi.Name, Len(fi.Name) - 4))

        //! DELETE
        // DELETE FROM DBF_PBA WHERE IP = '" & IP & "' ")
        DB::table('DBF_PBA')
            ->where('ip', $ip)
            ->delete();

        //! LOOPING INSERT FROM TABLE DBF
        //! INSERT  - DBF_PBA PENGGANTI BULKCOPY
        // sb.AppendLine("INSERT INTO DBF_PBA ( ")
        // sb.AppendLine("kodetoko, ")
        // sb.AppendLine("recid, ")
        // sb.AppendLine("docno, ")
        // sb.AppendLine("tanggal, ")
        // sb.AppendLine("prdcd, ")
        // sb.AppendLine("stok, ")
        // sb.AppendLine("sisa_stok, ")
        // sb.AppendLine("leadtime, ")
        // sb.AppendLine("Buffer, ")
        // sb.AppendLine("qty, ")
        // sb.AppendLine("price, ")
        // sb.AppendLine("total, ")
        // sb.AppendLine("rumus, ")
        // sb.AppendLine("bs_rms, ")
        // sb.AppendLine("lt_rms, ")
        // sb.AppendLine("std, ")
        // sb.AppendLine(" bs, ")
        // sb.AppendLine(" lt, ")
        // sb.AppendLine("""UPDATE"", ")
        // sb.AppendLine("tglupload, ")
        // sb.AppendLine("IP) ")
        // sb.AppendLine("VALUES ( ")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(0) & "', ")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(1) & "', ")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(2) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(3) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(4) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(5) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(6) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(7) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(8) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(9) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(10) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(11) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(12) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(13) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(14) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(15) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(16) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(17) & "',")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(18) & "',")
        // sb.AppendLine(" Current_date ,")
        // sb.AppendLine("'" & dtDBF.Rows(k).Item(20) & "')")

        DB::table('DBF_PBA')
            ->insert([
                'kodetoko' => 'aa',
                'recid' => 'aa',
                'docno' => 'aa',
                'tanggal' => 'aa',
                'prdcd' => 'aa',
                'stok' => 'aa',
                'sisa_stok' => 'aa',
                'leadtime' => 'aa',
                'Buffer' => 'aa',
                'qty' => 'aa',
                'price' => 'aa',
                'total' => 'aa',
                'rumus' => 'aa',
                'bs_rms' => 'aa',
                'lt_rms' => 'aa',
                'std' => 'aa',
                'bs' => 'aa',
                'lt' => 'aa',
                'UPDATE' => 'aa',
                'tglupload' => Carbon::now(),
                'IP' => 'aa',
            ]);

        //! GET -> noPB
        // sb.AppendLine("SELECT DISTINCT DOCNO FROM DBF_PBA ")
        // sb.AppendLine("WHERE kodetoko = '" & KodeToko & "' ")
        // sb.AppendLine("AND ip = '" & IP & "' ")
        // sb.AppendLine("AND tglupload = CURRENT_DATE ")
        // sb.AppendLine("LIMIT 1 ")

        $noPB = DB::table('dbf_pba')
            ->select('docno')
            ->where([
                'kodetoko' => $request->KodeToko,
                'ip' => $ip,
                'tglupload' => DB::raw("current_date")
            ])
            ->distinct()
            ->first()->docno;

        //! KALAU ADA REVISI PB UNTUK HARI INI - HAPUS CSV_PB_BOT
        // $this->DeleteCSVPOT(KodeToko);

        $this->DeleteCSVPOT($request->kodeToko);

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

        $data = DB::table('DBF_PBA')
            ->where('ip', $ip)
            ->whereDate('TGLUPLOAD', Carbon::now())
            ->get();

        if(!count($data)){
            $message = 'PROSES CSV_PB_POT - INSERT - GAGAL';
            return ApiFormatter::error(400, $message);
        }

        foreach($data as $item){
            DB::table('CSV_PB_POT')
                ->insert([
                    'CPP_KodeToko' => $item->KodeTOKO,
                    'CPP_NoPB' => $item->DOCNO,
                    'CPP_TglPB' => $item->TANGGAL,
                    'CPP_PluIDM' => $item->PRDCD,
                    'CPP_QTY' => $item->QTY,
                    'CPP_GROSS' => $item->PRICE,
                    'CPP_IP' => $ip,
                    'CPP_FileName' => '', //! belum
                    'CPP_TglProses' => Carbon::now(),
                    'CPP_CREATE_BY' => session('userid'),
                ]);
        }

        //! HANDLING
        //! CEK ADA PLU DOBEL GA DI PBA NYA
        // sb.AppendLine("Select COALESCE(count(CPP_PLUIDM),0)  ")
        // sb.AppendLine("  From CSV_PB_POT ")
        // sb.AppendLine(" Where CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
        // sb.AppendLine("	AND CPP_TglProses = CURRENT_DATE ")
        // sb.AppendLine(" Group By CPP_PLUIDM ")
        // sb.AppendLine("Having count(CPP_PLUIDM) > 1 ")

        $data = DB::table('CSV_PB_POT')
            ->where([
                'CPP_IP' => $ip,
                'CPP_KodeTOKO' => $request->KodeToko,
                'CPP_TglProses' => Carbon::now()
            ])
            ->groupBy('CPP_PLUIDM')
            ->havingRaw("count(CPP_PLUIDM) > 1")
            ->count();

        // if(jum > 0){
            //* ERROR MESSAGE -> Terdapat Dobel Record PLU Di File " & fi.Name & " Yang Sedang Diproses," & vbNewLine & "Harap Minta Revisi File PBA Ke IDM !
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
        // }

        if($data > 0){
            $nama_file = 'Nama File';
            $message = "Terdapat Dobel Record PLU Di File " . $nama_file . " Yang Sedang Diproses, Harap Minta Revisi File PBA Ke IDM !"; //! nama_file belum
            return ApiFormatter::error(400, $message);
        }

        //! HITUNG CSV_PB_POT
        // sb.AppendLine("Select COALESCE(Count(0),0) ")
        // sb.AppendLine("  FROM CSV_PB_POT ")
        // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
        // sb.AppendLine("	AND CPP_TglProses = CURRENT_DATE ")

        $data = DB::table('CSV_PB_POT')
            ->where([
                'CPP_IP' => $ip,
                'CPP_KodeTOKO' => $request->KodeToko,
            ])
            ->whereDate('CPP_TglProses', Carbon::now())
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
                UPDATE CSV_PB_POT A set CPP_AVGCOST = COALESCE(B.ST_AVGCOST,0),
                CPP_Stock = COALESCE(B.ST_SaldoAkhir, 0)
                from (
                    Select *
                    From tbMaster_Stock
                    Where ST_Lokasi = '01'
                ) B
                where A.CPP_PLUIGR = B.ST_PRDCD
                and A.CPP_IP = '" . $ip . "'
                and A.CPP_KodeTOKO = '" . $request->KodeToko . "'
                AND A.CPP_TglProses = CURRENT_DATE
            ");
        } catch (\Exception $e) {
            $this->KurangiAkumulasiPB($request->KodeToko, $request->noPB);
            $this->DeleteCSVPOT($request->KodeToko);
            $message = "PROSES CSV_PB_POT - MERGE AVGCOST+STOCK - GAGAL";
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
                UPDATE CSV_PB_POT A SET CPP_Stock =  COALESCE(B.SEP_QTYEKONOMIS,0)
                from (
                Select *
                From stock_ekonomis_pot
                Where sep_toko = '" . $request->KodeToko . "'
                AND sep_nopb = '" . $noPB . "'
                AND sep_ip = '" . $ip . "'
                B
                where A.CPP_PLUIGR = B.SEP_PLUIGR
                and A.CPP_IP = '" . $ip . "'
                AND A.CPP_KodeTOKO = '" . $request->KodeToko . "'
                AND A.CPP_TglProses = CURRENT_DATE
            ");
        } catch (\Exception $e) {
            $this->KurangiAkumulasiPB($request->KodeToko, $request->noPB);
            $this->DeleteCSVPOT($request->KodeToko);
            $message = "PROSES CSV_PB_POT - MERGE STOCK_EKONOMIS - GAGAL";
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
            SELECT COALESCE(Count(DISTINCT CPP_NoPB),0)
                FROM CSV_PB_POT
            WHERE EXISTS
            (
                SELECT pbo_nopb
                    FROM TBMASTER_PBOMI
                WHERE PBO_NOPB = CPP_NoPB
                    AND PBO_KODEOMI = CPP_KodeToko
                    AND PBO_TGLPB = CPP_TglPB
            )
                AND CPP_IP = '" . $ip . "'
                AND CPP_KodeTOKO = '" . $request->KodeToko . "'
                AND CPP_TglProses = CURRENT_DATE
            ");
        } catch (\Exception $e) {
            $nama_file = 'nama file';
            $message = "No Dokumen:$noPB Toko:" . $request->KodeToko . "File:" . $nama_file . "SUDAH PERNAH DIPROSES!!!"; //! nama_file belum
            return ApiFormatter::error(400, $message);
        }

        //! LAST
        skipFilePBSL:

        // $this->RefreshGridHeader();

        return;
    }

    public function uploadPot(UploadPotRequest $request){ #PROSES F8

        //! HANDLING HANYA BISA JAM 12 MALAM
        //* ERROR MESSAGE->Mohon Tunggu Sampai JAM 12 MALAM

        //If dgvHeader.RowCount > 0 And dgvDetail.RowCount > 0

        // noPB = dgvHeader.CurrentRow.Cells(0).Value
        // tglPB = dgvHeader.CurrentRow.Cells(1).Value
        // KodeToko = dgvHeader.CurrentRow.Cells(2).Value

        // $this->ProsesPBIDM(txtPathFilePBAT.Text & "\" & dgvHeader.CurrentRow.Cells(5).Value)

        // if(adaProses){
            //! SET FLAG CSV_PB_IDM
            // sb.AppendLine("UPDATE CSV_PB_POT ")
            // sb.AppendLine("   SET CPP_FLAG = '1' ")
            // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
            // sb.AppendLine("   AND CPP_noPB = '" & noPB & "' ")
            // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
            // sb.AppendLine("   AND CPP_TglPB = TO_DATE('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND CPP_FLAG IS NULL ")

            DB::table('CSV_PB_POT')
                ->where([
                    'CPP_IP' => $request->ip,
                    'CPP_noPB' =>  $request->noPB,
                    'CPP_KodeToko' =>  $request->KodeToko
                ])
                ->whereRaw("CPP_TglPB = TO_DATE('" . $request->tglPB . "','DD-MM-YYYY')")
                ->whereNull('CPP_FLAG')
                ->update([
                    'CPP_FLAG' => '1'
                ]);

            //$this->RefreshGridHeader()

            // return PROSES UPLOAD PB IDM - POT SELESAI DILAKUKAN !",
        // }



    }


}
