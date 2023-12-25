<?php

namespace App\Http\Controllers;

use App\Helper\DatabaseConnection;
use App\Http\Requests\DetailKasirRequest;
use App\Http\Requests\TableRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KonversiAtkController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        //harus login dulu sebelum upload pb pot
        //error message -> USERNAME ATAU PASSWORD SALAH

        // sb.AppendLine("Select coalesce(Count(1),0) ")
        // sb.AppendLine("  From tbmaster_user ")
        // sb.AppendLine(" Where kodeigr = '" & KDIGR & "' ")
        // sb.AppendLine("   And userid = '" & Replace(txtUser.Text, "'", "") & "' ")
        // sb.AppendLine("   And userpassword = '" & Replace(txtPassword.Text, "'", "") & "'  ")

        return view('home');
    }

    public function actionUpload(){

        //! CHECK MASTER_SUPPLY_IDM
        // sb.AppendLine("  SELECT COUNT(DISTINCT msi_kodedc)  ")
        // sb.AppendLine("  FROM master_supply_idm  ")
        // sb.AppendLine("  WHERE msi_kodeigr = '" & KDIGR & "' ")

        if(jum > 0){
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

        //! DELETE DATA STOCK_AKUMULASIPB_POT
        // DELETE FROM STOCK_AKUMULASIPB_POT WHERE SAP_TGLUPD < CURRENT_DATE

        //! AFTER UPLOAD .DBF bisa lebih dari 1, disimpen di directory tertentu
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
    }

    //? FLOW
    //? 1.	PILIH FILE DBF DI DERECTORY YANG TELAH DI SIMPAN
    //? 2.	F3 UNTUK TARIK DATA SEHINGGA TAMPIL DI GRID (HEADER DAN DETAIL)
    //? 3.	F8 UNTUK PROSES UPLOAD DATA

    public function selectedDataTable(){
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

    }

    public function tarikData(){ #PROSES F3

        #Path File PBAT Kosong!",

        // sb.AppendLine("Select TKO_KodeCustomer ")
        // sb.AppendLine("From tbMaster_tokoIGR ")
        // sb.AppendLine("Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        if(jum == 0){
            //* ERROR MESSAGE -> KODE TOKO " & KodeToko & " TIDAK TERDAFTAR DI MASTER CUSTOMER !
            goto skipFilePBSL;
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

        //! GET -> noPB
        // sb.AppendLine("SELECT DISTINCT DOCNO FROM DBF_PBA ")
        // sb.AppendLine("WHERE kodetoko = '" & KodeToko & "' ")
        // sb.AppendLine("AND ip = '" & IP & "' ")
        // sb.AppendLine("AND tglupload = CURRENT_DATE ")
        // sb.AppendLine("LIMIT 1 ")

        //! KALAU ADA REVISI PB UNTUK HARI INI - HAPUS CSV_PB_BOT
        // $this->DeleteCSVPOT(KodeToko);

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

        if(jum == 0){
            //* ERROR MESSAGE -> PROSES CSV_PB_POT - INSERT - GAGAL
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
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

        if(jum > 0){
            //* ERROR MESSAGE -> Terdapat Dobel Record PLU Di File " & fi.Name & " Yang Sedang Diproses," & vbNewLine & "Harap Minta Revisi File PBA Ke IDM !
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
        }

        //! HITUNG CSV_PB_POT
        // sb.AppendLine("Select COALESCE(Count(0),0) ")
        // sb.AppendLine("  FROM CSV_PB_POT ")
        // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
        // sb.AppendLine("	AND CPP_TglProses = CURRENT_DATE ")

        if(jum = 0){
            //* ERROR MESSAGE -> TIDAK ADA DATA CSV_PB_POT YANG BISA JADI PB DI IGR!!
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
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

        if(jum = 0){
            //* PROSES CSV_PB_POT - MERGE AVGCOST+STOCK - GAGAL
            // $this->KurangiAkumulasiPB(KodeToko, noPB);
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
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

        if(jum = 0){
             //* PROSES CSV_PB_POT - MERGE STOCK_EKONOMIS - GAGAL
            // $this->KurangiAkumulasiPB(KodeToko, noPB);
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
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

        if(jum > 0){
            //* ERROR MESSAGE -> No Dokumen:" & noPB & vbNewLine & "Toko:" & KodeToko & vbNewLine & "File:" & fi.Name & vbNewLine & vbNewLine & "SUDAH PERNAH DIPROSES!!!"
            // $this->KurangiAkumulasiPB(KodeToko, noPB);
            // $this->DeleteCSVPOT(KodeToko);
            // goto skipFilePBSL
        }

        //! LAST
        skipFilePBSL:

        // $this->RefreshGridHeader();

        return;
    }

    public function uploadPot(){ #PROSES F8

        //! HANDLING HANYA BISA JAM 12 MALAM
        //* ERROR MESSAGE->Mohon Tunggu Sampai JAM 12 MALAM

        //If dgvHeader.RowCount > 0 And dgvDetail.RowCount > 0

        // noPB = dgvHeader.CurrentRow.Cells(0).Value
        // tglPB = dgvHeader.CurrentRow.Cells(1).Value
        // KodeToko = dgvHeader.CurrentRow.Cells(2).Value

        // $this->ProsesPBIDM(txtPathFilePBAT.Text & "\" & dgvHeader.CurrentRow.Cells(5).Value)

        if(adaProses){
            //! SET FLAG CSV_PB_IDM
            // sb.AppendLine("UPDATE CSV_PB_POT ")
            // sb.AppendLine("   SET CPP_FLAG = '1' ")
            // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
            // sb.AppendLine("   AND CPP_noPB = '" & noPB & "' ")
            // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
            // sb.AppendLine("   AND CPP_TglPB = TO_DATE('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND CPP_FLAG IS NULL ")

            //$this->RefreshGridHeader()

            // return PROSES UPLOAD PB IDM - POT SELESAI DILAKUKAN !",
        }



    }


}
