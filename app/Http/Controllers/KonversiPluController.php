<?php

namespace App\Http\Controllers;

use App\Helper\DatabaseConnection;
use App\Http\Requests\DetailKasirRequest;
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
        // sb.AppendLine("SELECT PRD_DeskripsiPanjang ")
        // sb.AppendLine("  FROM tbMaster_Prodmast ")
        // sb.AppendLine(" WHERE PRD_PRDCD = '" & txtPluIGR.Text & "'  ")

    }

    public function datatables(){
        // sb.AppendLine("SELECT KAT_PLUIDM, ")
        // sb.AppendLine("       KAT_PLUIGR, ")
        // sb.AppendLine("       KAT_DESKRIPSI, ")
        // sb.AppendLine("       KAT_FLAGAKTIF ")
        // sb.AppendLine("  FROM KONVERSI_ATK ")
        // sb.AppendLine(" ORDER BY KAT_PLUIDM ")

    }

    public function actionSave(){
        //! CEK DI TABLE KONVERSI ATK UNTUK PLU DAN IGR YANG DI PILIH APAKAH SUDAH ADA
        //! CEK SUDAH ADA BELUM PLUNYA
        // sb.AppendLine("SELECT coalesce(Count(0),0) ")
        // sb.AppendLine("  FROM KONVERSI_ATK ")
        // sb.AppendLine(" WHERE KAT_PLUIDM = '" & txtPluIDM.Text & "' ")
        // sb.AppendLine("   AND KAT_PLUIGR = '" & txtPluIGR.Text & "' ")

        if(jum > 0){
            //! CEK SUDAH ADA YG AKTIF??
            // sb.AppendLine("Select coalesce(Count(0),0) ")
            // sb.AppendLine("  FROM KONVERSI_ATK ")
            // sb.AppendLine(" WHERE KAT_PLUIDM = '" & txtPluIDM.Text & "' ")
            // sb.AppendLine("   AND KAT_FLAGAKTIF > 0 ")
            // sb.AppendLine("   AND KAT_PLUIGR <> '" & txtPluIGR.Text & "' ")

            if(jum > 0){
                //* ERROR MESSAGE -> PLU IDM : " & txtPluIDM.Text & " Sudah Mempunyai PLU IGR Yang Aktif !
                return;
            }else{
                //! UPDATE KONVERSI_ATK
                // sb.AppendLine("UPDATE KONVERSI_ATK ")
                // sb.AppendLine("SET KAT_DESKRIPSI = '" & Replace(txtDeskripsi.Text, "'", "''") & "', ")
                // sb.AppendLine("KAT_FLAGAKTIF = '" & IIf(chkAktif.Checked, 1, 0) & "', ")
                // sb.AppendLine("KAT_MODIFY_BY = '" & UserID & "', ")
                // sb.AppendLine("KAT_MODIFY_DT = CURRENT_TIMESTAMP ")
                // sb.AppendLine("WHERE KAT_PLUIDM = '" & txtPluIDM.Text & "' ")
                // sb.AppendLine("AND KAT_PLUIGR = '" & txtPluIGR.Text & "' ")

                // sb.AppendLine("INSERT INTO KONVERSI_ATK ")
                // sb.AppendLine("( ")
                // sb.AppendLine("  KAT_PLUIDM, ")
                // sb.AppendLine("  KAT_PLUIGR, ")
                // sb.AppendLine("  KAT_DESKRIPSI, ")
                // sb.AppendLine("  KAT_FLAGAKTIF, ")
                // sb.AppendLine("  KAT_Create_By, ")
                // sb.AppendLine("  KAT_Create_Dt ")
                // sb.AppendLine(") ")
                // sb.AppendLine("VALUES ")
                // sb.AppendLine("( ")
                // sb.AppendLine("  '" & txtPluIDM.Text & "', ")
                // sb.AppendLine("  '" & txtPluIGR.Text & "', ")
                // sb.AppendLine("  '" & Replace(txtDeskripsi.Text, "'", "''") & "', ")
                // sb.AppendLine("  '" & IIf(chkAktif.Checked, 1, 0) & "', ")
                // sb.AppendLine("  '" & UserID & "', ")
                // sb.AppendLine("  CURRENT_TIMESTAMP ")
                // sb.AppendLine(") ")

                // return //* MESSAGE SUCCESS -> Data Berhasil Diupdate !!
                //REFRESH DATATABLES
            }

        }

        //! INSERT KONVERSI_ATK
        // sb.AppendLine("INSERT INTO KONVERSI_ATK ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  KAT_PLUIDM, ")
        // sb.AppendLine("  KAT_PLUIGR, ")
        // sb.AppendLine("  KAT_DESKRIPSI, ")
        // sb.AppendLine("  KAT_FLAGAKTIF, ")
        // sb.AppendLine("  KAT_Create_By, ")
        // sb.AppendLine("  KAT_Create_Dt ")
        // sb.AppendLine(") ")
        // sb.AppendLine("VALUES ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  '" & txtPluIDM.Text & "', ")
        // sb.AppendLine("  '" & txtPluIGR.Text & "', ")
        // sb.AppendLine("  '" & Replace(txtDeskripsi.Text, "'", "''") & "', ")
        // sb.AppendLine("  '" & IIf(chkAktif.Checked, 1, 0) & "', ")
        // sb.AppendLine("  '" & UserID & "', ")
        // sb.AppendLine("  CURRENT_TIMESTAMP ")
        // sb.AppendLine(") ")

        // return //* MESSAGE SUCCESS -> Data Berhasil Disimpan !!
        //REFRESH DATATABLES
    }

    //! =======ANOTHER FUNCTION=========//!

    //! di double click akan menmpel plu igr dan deskripsi
    public function helpIgr(){
        // sb.AppendLine("SELECT PRD_PRDCD as PLUIGR,PRD_DeskripsiPanjang as DESK ")
        // sb.AppendLine("  FROM tbMaster_Prodmast ")
        // sb.AppendLine(" WHERE PRD_PRDCD LIKE '%0' ")
        // If RadioPLUIGR.Checked Then
        // sb.AppendLine("   AND PRD_PRDCD LIKE '%" & txtPLUIGR.Text & "%' ")
        // ElseIf RadioDeskripsi.Checked Then
        // sb.AppendLine("   AND PRD_DeskripsiPanjang LIKE '%" & txtDeskripsi.Text & "%' ")
        // End If
        // sb.AppendLine(" ORDER BY PRD_DeskripsiPanjang ")
    }
}
