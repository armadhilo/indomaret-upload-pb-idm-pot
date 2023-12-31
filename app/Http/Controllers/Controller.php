<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function RefreshGridHeader($ip){
        //! DELETE TEMP_CSV_PB_POT
        // sb.AppendLine("DELETE FROM TEMP_CSV_PB_POT ")
        // sb.AppendLine(" WHERE IP = '" & IP & "' ")

        DB::table('TEMP_CSV_PB_POT')
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
            INSERT INTO TEMP_CSV_PB_POT
            (
                NOPB,
                TGLPB,
                TOKO,
                PLUIGR,
                QTY,
                NAMA_FILE,
                STOCK,
                IP
            )
            Select CPP_NOPB as NOPB,
                TO_CHAR(CPP_TglPB,'DD-MM-YYYY') as TGLPB,
                CPP_KodeToko as TOKO,
                CPP_PLUIGR as PLUIGR,
                CPP_Qty as QTY,
                CPP_FileName,
                CPP_Stock,
                CPP_IP
            From CSV_PB_POT
            Where CPP_TGLPROSES >= CURRENT_DATE - 7
            And CPP_FLAG IS NULL
            And CPP_IP = '" . $ip . "'
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

        DB::select("
            WITH A
            AS
            (
                Select NOPB,
                    TGLPB,
                    TOKO,
                    coalesce(COUNT(PLUIGR),0) as ITEM,
                    ROUND(sum(coalesce(QTY::numeric,0) / CASE WHEN PRD_UNIT = 'KG' THEN PRD_FRAC ELSE 1 end * coalesce(ST_AVGCOST::numeric,0)),2) as RUPIAH,
                    NAMA_FILE
                From TEMP_CSV_PB_POT,
                    tbMaster_Stock,
                    tbMaster_Prodmast
                Where ST_PRDCD = PLUIGR
                And IP = '" . $ip . "'
                And ST_LOKASI = '01'
                AND PRD_PRDCD = ST_PRDCD
                AND PRD_PRDCD = PLUIGR
                Group By NOPB,TGLPB,TOKO,NAMA_FILE
            )
            SELECT NOPB,
                TGLPB,
                TOKO,
                Replace(REPLACE(TO_CHAR(Sum(ITEM),'999G999G999G999'),',','.'),' ','') AS ITEM,
                Replace(REPLACE(TO_CHAR(Sum(RUPIAH),'999G999G999G999'),',','.'),' ','') AS RUPIAH,
                NAMA_FILE
            FROM A
            GROUP BY NOPB,
                TGLPB,
                TOKO,
                NAMA_FILE
            ORDER BY TOKO
        ");
    }

    public function DeleteCSVPOT($ip,$kodeToko){

        // sb.AppendLine("DELETE FROM CSV_PB_POT ")
        // sb.AppendLine(" Where CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_KodeToko = '" & kdToko & "' ")
        // sb.AppendLine("   AND CPP_TglProses = CURRENT_DATE ")

        DB::table('CSV_PB_POT')
            ->where([
                'CPP_IP' => $ip,
                'CPP_KodeToko' => $kodeToko
            ])
            ->whereRaw("CPP_TglProses = CURRENT_DATE")
            ->delete();
    }

    public function ProsesKonvensi($ip, $kodeToko, $noPB){

        //! DELETE DATA STOCK_EKONOMIS_POT
        //DELETE FROM STOCK_EKONOMIS_POT WHERE SEP_TOKO = '" & kdToko & "' AND SEP_NOPB = '" & noPB & "' "

        DB::table('STOCK_EKONOMIS_POT')
            ->where([
                'SEP_TOKO' => $kodeToko,
                'SEP_NOPB' => $noPB,
            ])
            ->delete();

        //! INSERT INTO STOCK_EKONOMIS_POT
        // sb.AppendLine("INSERT INTO STOCK_EKONOMIS_POT ( ")
        // sb.AppendLine("	SEP_TOKO, ")
        // sb.AppendLine("	SEP_NOPB, ")
        // sb.AppendLine("	SEP_TGLPB, ")
        // sb.AppendLine("	SEP_PLUIDM, ")
        // sb.AppendLine("	SEP_PLUIGR, ")
        // sb.AppendLine("	SEP_LPP, ")
        // sb.AppendLine("	SEP_PLANO, ")
        // sb.AppendLine("	SEP_QTYORDER, ")
        // sb.AppendLine("	SEP_AKUMULASI, ")
        // sb.AppendLine("	SEP_QTYEKONOMIS, ")
        // sb.AppendLine("	SEP_CREATE_BY, ")
        // sb.AppendLine("	SEP_CREATE_DT, ")
        // sb.AppendLine("	SEP_IP ")
        // sb.AppendLine(") ")
        // sb.AppendLine("SELECT toko, ")
        // sb.AppendLine("       nopb,  ")
        // sb.AppendLine("       tglpb,  ")
        // sb.AppendLine("       pluidm,  ")
        // sb.AppendLine("       pluigr,  ")
        // sb.AppendLine("       COALESCE(st_saldoakhir, 0) AS qty_lpp,  ")
        // sb.AppendLine("       SUM(COALESCE(lks_qty, 0)) AS qty_plano,  ")
        // sb.AppendLine("       qty AS qty_order,  ")
        // sb.AppendLine("       COALESCE(qty_akumulasi, 0) AS qty_akumulasi,  ")
        // sb.AppendLine("       COALESCE(st_saldoakhir,0) -  ")
        // sb.AppendLine("       ABS(COALESCE(qty_akumulasi, 0)) AS qty_ekonomis, ")
        // sb.AppendLine("       '" & UserID & "',  ")
        // sb.AppendLine("       CURRENT_TIMESTAMP,  ")
        // sb.AppendLine("       req_id  ")
        // sb.AppendLine(" FROM (  ")
        // sb.AppendLine("	      SELECT cpp_kodetoko AS toko,  ")
        // sb.AppendLine("                cpp_nopb AS nopb,  ")
        // sb.AppendLine("                DATE_TRUNC('day',cpp_tglpb) AS tglpb,  ")
        // sb.AppendLine("                cpp_pluidm AS pluidm,  ")
        // sb.AppendLine("                kat_pluigr AS pluigr,  ")
        // sb.AppendLine("                cpp_qty AS qty,  ")
        // sb.AppendLine("                COALESCE(sap_akumulasi,0) AS qty_akumulasi,  ")
        // sb.AppendLine("                cpp_ip AS req_id  ")
        // sb.AppendLine("            FROM csv_pb_pot ")
        // sb.AppendLine("		       JOIN konversi_atk ")
        // sb.AppendLine("   		   ON cpp_pluidm = kat_pluidm  ")
        // sb.AppendLine("		       LEFT JOIN stock_akumulasipb_pot ")
        // sb.AppendLine(" 		   ON kat_pluigr = sap_prdcd ")
        // sb.AppendLine("            WHERE cpp_kodetoko = '" & kdToko & "' ")
        // sb.AppendLine("            AND cpp_nopb = '" & noPB & "'  ")
        // sb.AppendLine("            AND cpp_ip = '" & IP & "'  ")
        // sb.AppendLine("            AND DATE_TRUNC('day',cpp_tglproses) = DATE_TRUNC('day',CURRENT_DATE) ")
        // sb.AppendLine("  ) as pb_pot ")
        // sb.AppendLine("  JOIN tbmaster_stock ")
        // sb.AppendLine("    ON st_prdcd = pluigr  ")
        // sb.AppendLine("   AND st_lokasi = '01'  ")
        // sb.AppendLine("  LEFT JOIN	tbmaster_lokasi  ")
        // sb.AppendLine("    ON lks_prdcd = pluigr  ")
        // sb.AppendLine("   AND NOT REGEXP_LIKE(lks_koderak,'^D|^G')  ")
        // sb.AppendLine("   AND lks_jenisrak NOT LIKE 'S%'  ")
        // sb.AppendLine(" GROUP BY toko, nopb, tglpb, pluidm, pluigr, st_saldoakhir, qty, qty_akumulasi, req_id  ")

        DB::select("
            INSERT INTO STOCK_EKONOMIS_POT (
                SEP_TOKO,
                SEP_NOPB,
                SEP_TGLPB,
                SEP_PLUIDM,
                SEP_PLUIGR,
                SEP_LPP,
                SEP_PLANO,
                SEP_QTYORDER,
                SEP_AKUMULASI,
                SEP_QTYEKONOMIS,
                SEP_CREATE_BY,
                SEP_CREATE_DT,
                SEP_IP
            )
            SELECT toko,
                nopb,
                tglpb,
                pluidm,
                pluigr,
                COALESCE(st_saldoakhir, 0) AS qty_lpp,
                SUM(COALESCE(lks_qty, 0)) AS qty_plano,
                qty AS qty_order,
                COALESCE(qty_akumulasi, 0) AS qty_akumulasi,
                COALESCE(st_saldoakhir,0) -
                ABS(COALESCE(qty_akumulasi, 0)) AS qty_ekonomis,
                '" . session('userid') . "',
                CURRENT_TIMESTAMP,
                req_id
            FROM (
                    SELECT cpp_kodetoko AS toko,
                            cpp_nopb AS nopb,
                            DATE_TRUNC('day',cpp_tglpb) AS tglpb,
                            cpp_pluidm AS pluidm,
                            kat_pluigr AS pluigr,
                            cpp_qty AS qty,
                            COALESCE(sap_akumulasi,0) AS qty_akumulasi,
                            cpp_ip AS req_id
                        FROM csv_pb_pot
                        JOIN konversi_atk
                        ON cpp_pluidm = kat_pluidm
                        LEFT JOIN stock_akumulasipb_pot
                        ON kat_pluigr = sap_prdcd
                        WHERE cpp_kodetoko = '" . $kodeToko . "'
                        AND cpp_nopb = '" . $noPB . "'
                        AND cpp_ip = '" . $ip . "'
                        AND DATE_TRUNC('day',cpp_tglproses) = DATE_TRUNC('day',CURRENT_DATE)
            ) as pb_pot
            JOIN tbmaster_stock
            ON st_prdcd = pluigr
            AND st_lokasi = '01'
            LEFT JOIN	tbmaster_lokasi
            ON lks_prdcd = pluigr
            AND NOT REGEXP_LIKE(lks_koderak,'^D|^G')
            AND lks_jenisrak NOT LIKE 'S%'
            GROUP BY toko, nopb, tglpb, pluidm, pluigr, st_saldoakhir, qty, qty_akumulasi, req_id
        ");

        //!  RUN PROCEDURE KONVERSI_POT
        // CALL KONVERSI_POT ('" & kdToko.Trim & "', '" & noPB.Trim & "', '" & IP.Trim & "', '')
        DB::select("CALL KONVERSI_POT('$kodeToko','$noPB','$ip')");

        //! UPDATE STOCK_AKUMULASIPB_POT
        // sb.AppendLine("MERGE INTO stock_akumulasipb_pot t ")
        // sb.AppendLine("USING ( ")
        // sb.AppendLine("  SELECT DISTINCT cpp_pluigr, ")
        // sb.AppendLine("         cpp_qty ")
        // sb.AppendLine("    FROM csv_pb_pot ")
        // sb.AppendLine("   WHERE cpp_kodetoko = '" & kdToko & "' ")
        // sb.AppendLine("     AND cpp_nopb = '" & noPB & "' ")
        // sb.AppendLine("     AND cpp_ip = '" & IP & "' ")
        // sb.AppendLine("     AND cpp_pluigr IS NOT NULL ")
        // sb.AppendLine("     AND cpp_tglproses = CURRENT_DATE ")
        // sb.AppendLine("     AND COALESCE(cpp_tolakan_sep,0) = 0 ")
        // sb.AppendLine(") s ")
        // sb.AppendLine("ON ( ")
        // sb.AppendLine("  t.sap_prdcd = s.cpp_pluigr ")
        // sb.AppendLine(") ")
        // sb.AppendLine("WHEN MATCHED THEN ")
        // sb.AppendLine("  UPDATE SET sap_akumulasi = sap_akumulasi + s.cpp_qty, ")
        // sb.AppendLine("             sap_tglupd = CURRENT_DATE ")
        // sb.AppendLine("WHEN NOT MATCHED THEN ")
        // sb.AppendLine("  INSERT ( ")
        // sb.AppendLine("	  sap_prdcd, ")
        // sb.AppendLine("	  sap_akumulasi, ")
        // sb.AppendLine("	  sap_tglupd ")
        // sb.AppendLine("  ) VALUES ( ")
        // sb.AppendLine("   s.cpp_pluigr, ")
        // sb.AppendLine("	  s.cpp_qty, ")
        // sb.AppendLine("	  CURRENT_DATE ")
        // sb.AppendLine("  ) ")

        DB::select("
            MERGE INTO stock_akumulasipb_pot t
            USING (
            SELECT DISTINCT cpp_pluigr, cpp_qty
            FROM csv_pb_pot
            WHERE cpp_kodetoko = '" . $kodeToko . "'
                AND cpp_nopb = '" . $noPB . "'
                AND cpp_ip = '" . $ip . "'
                AND cpp_pluigr IS NOT NULL
                AND cpp_tglproses = CURRENT_DATE
                AND COALESCE(cpp_tolakan_sep,0) = 0
            ) s
            ON (
            t.sap_prdcd = s.cpp_pluigr
            )
            WHEN MATCHED THEN
            UPDATE SET sap_akumulasi = sap_akumulasi + s.cpp_qty,
                        sap_tglupd = CURRENT_DATE
            WHEN NOT MATCHED THEN
            INSERT (
                sap_prdcd,
                sap_akumulasi,
                sap_tglupd
            ) VALUES (
                s.cpp_pluigr,
                s.cpp_qty,
                CURRENT_DATE
            )
        ");
    }

    public function KurangiAkumulasiPB($ip, $kodeToko, $noPB){

        //! ROLLBACK STOCK_AKUMULASIPB
        // sb.AppendLine("MERGE INTO stock_akumulasipb_pot t ")
        // sb.AppendLine("USING ( ")
        // sb.AppendLine("  SELECT DISTINCT cpp_pluigr, ")
        // sb.AppendLine("         cpp_qty ")
        // sb.AppendLine("    FROM csv_pb_pot ")
        // sb.AppendLine("   WHERE cpp_kodetoko = '" & kdToko & "' ")
        // sb.AppendLine("     AND cpp_nopb = '" & noPB & "' ")
        // sb.AppendLine("     AND cpp_ip = '" & IP & "' ")
        // sb.AppendLine("     AND cpp_pluigr IS NOT NULL ")
        // sb.AppendLine("     AND COALESCE(cpp_tolakan_sep,0) = 0 ")
        // sb.AppendLine(") s ")
        // sb.AppendLine("ON ( ")
        // sb.AppendLine("  t.sap_prdcd = s.cpp_pluigr ")
        // sb.AppendLine(") ")
        // sb.AppendLine("WHEN MATCHED THEN ")
        // sb.AppendLine("  UPDATE SET sap_akumulasi = sap_akumulasi - s.cpp_qty, ")
        // sb.AppendLine("             sap_tglupd = CURRENT_DATE ")

        DB::select("
            MERGE INTO stock_akumulasipb_pot t
            USING (
            SELECT DISTINCT cpp_pluigr, cpp_qty
            FROM csv_pb_pot
            WHERE cpp_kodetoko = '" & $kodeToko & "'
                AND cpp_nopb = '" & $noPB & "'
                AND cpp_ip = '" & $ip & "'
                AND cpp_pluigr IS NOT NULL
                AND COALESCE(cpp_tolakan_sep,0) = 0
            ) s
            ON (
            t.sap_prdcd = s.cpp_pluigr
            )
            WHEN MATCHED THEN
            UPDATE SET sap_akumulasi = sap_akumulasi - s.cpp_qty,
                sap_tglupd = CURRENT_DATE
        ");
    }

    public function getKodeDC($kodeToko){
        //! GET KODE DC
        //("SELECT msi_kodedc FROM master_supply_idm WHERE msi_kodetoko = '" & kodetoko & "'"

        return DB::table('master_supply_idm')->where('msi_kodetoko', $kodeToko)->first()->msi_kodedc;
    }

    public function getIP(){
        return $_SERVER['REMOTE_ADDR'];
    }

    public function CetakAll_1(){

        //! GET HEADER CETAKAN
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        //! GET HEADER CETAKAN
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) >

        //! CHECK DATA
        // sb.AppendLine("Select coalesce(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where UPPER(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        if(check_data > 0){
            // sb.AppendLine("INSERT INTO PBIDM_LISTORDER ")
            // sb.AppendLine("  ( ")
            // sb.AppendLine("  PBL_KODETOKO, ")
            // sb.AppendLine("  PBL_NOPB, ")
            // sb.AppendLine("  PBL_TGLPB, ")
            // sb.AppendLine("  PBL_PLU, ")
            // sb.AppendLine("  PBL_DESKRIPSI, ")
            // sb.AppendLine("  PBL_UNIT, ")
            // sb.AppendLine("  PBL_FRAC, ")
            // sb.AppendLine("  PBL_QTYB, ")
            // sb.AppendLine("  PBL_QTYK, ")
            // sb.AppendLine("  PBL_QTYO, ")
            // sb.AppendLine("  PBL_HRGSATUAN, ")
            // sb.AppendLine("  PBL_NILAI, ")
            // sb.AppendLine("  PBL_PPN, ")
            // sb.AppendLine("  PBL_TOTAL, ")
            // sb.AppendLine("  PBL_CREATE_BY, ")
            // sb.AppendLine("  PBL_CREATE_DT ")
            // sb.AppendLine("  ) ")
            // sb.AppendLine("Select '" & KodeToko & "' as KODETOKO, ")
            // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
            // sb.AppendLine("       TO_DATE('" & tglPB & "','DD-MM-YYYY') as TglPB,  ")
            // sb.AppendLine("       plukarton as plu,  ")
            // sb.AppendLine("       desk,  ")
            // sb.AppendLine("       unitkarton as unit,  ")
            // sb.AppendLine("       frackarton as frac, ")
            // sb.AppendLine("       qtyb as qty, ")
            // sb.AppendLine("       qtyk as frc,  ")
            // sb.AppendLine("       fdqtyb as inpcs,  ")
            // sb.AppendLine("       Round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Harga, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Nilai, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_MarginPLUIDM ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and MPI_PluIGR = PLUKARTON ")

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_LISTORDER")
            // sb = New StringBuilder
            // sb.AppendLine("Select plukarton as plu,  ")
            // sb.AppendLine("       desk,  ")
            // sb.AppendLine("       unitkarton ||'/'|| frackarton as unit,  ")
            // sb.AppendLine("       qtyb as qty, ")
            // sb.AppendLine("       qtyk as frc,  ")
            // sb.AppendLine("       fdqtyb as inpcs,  ")
            // sb.AppendLine("       Round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Harga, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Nilai, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_MarginPLUIDM ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and MPI_PluIGR = PLUKARTON ")
            // sb.AppendLine(" Order By plukarton ")
        }else{
            // sb.AppendLine("INSERT INTO PBIDM_REKAPORDER  ")
            // sb.AppendLine("  ( ")
            // sb.AppendLine("  PBL_KODETOKO, ")
            // sb.AppendLine("  PBL_NOPB, ")
            // sb.AppendLine("  PBL_TGLPB, ")
            // sb.AppendLine("  PBL_PLU, ")
            // sb.AppendLine("  PBL_DESKRIPSI, ")
            // sb.AppendLine("  PBL_UNIT, ")
            // sb.AppendLine("  PBL_QTYB, ")
            // sb.AppendLine("  PBL_QTYK, ")
            // sb.AppendLine("  PBL_QTYO, ")
            // sb.AppendLine("  PBL_HRGSATUAN, ")
            // sb.AppendLine("  PBL_NILAI, ")
            // sb.AppendLine("  PBL_PPN, ")
            // sb.AppendLine("  PBL_TOTAL, ")
            // sb.AppendLine("  PBL_CREATE_BY, ")
            // sb.AppendLine("  PBL_CREATE_DT ")
            // sb.AppendLine("  ) ")
            // sb.AppendLine("Select '" & KodeToko & "' as KODETOKO ")
            // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
            // sb.AppendLine("       TO_DATE('" & tglPB & "','YYYYMMDD') as TglPB,  ")
            // sb.AppendLine("       plukarton as plu,  ")
            // sb.AppendLine("       desk,  ")
            // sb.AppendLine("       unitkarton ||'/'|| frackarton as unit,  ")
            // sb.AppendLine("       qtyb as qty, ")
            // sb.AppendLine("       qtyk as frc,  ")
            // sb.AppendLine("       fdqtyb as inpcs,  ")
            // sb.AppendLine("       Round(avgcost * (1+ " & PersenMargin & ")) as Harga, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) as Nilai, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_LISTORDER")
            // '---- 24-03-2014

            // sb = New StringBuilder
            // sb.AppendLine("Select plukarton as plu,  ")
            // sb.AppendLine("       desk,  ")
            // sb.AppendLine("       unitkarton ||'/'|| frackarton as unit,  ")
            // sb.AppendLine("       qtyb as qty, ")
            // sb.AppendLine("       qtyk as frc,  ")
            // sb.AppendLine("       fdqtyb as inpcs,  ")
            // sb.AppendLine("       Round(avgcost * (1+ " & PersenMargin & ")) as Harga, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) as Nilai, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN, ")
            // sb.AppendLine("       fdqtyb * round(avgcost * (1+" & PersenMargin & ")) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine(" Order By plukarton ")

        }
    }

    public function CetakAll_2(){
        //! GET HEADER CETAKAN
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        //! GET HEADER CETAKAN
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        //! CHECK DATA
        // sb.AppendLine("Select coalesce(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where UPPER(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        if(check_data > 0){
            // sb.AppendLine("INSERT INTO PBIDM_REKAPORDER  ")
            // sb.AppendLine("  ( ")
            // sb.AppendLine("  PBR_KODETOKO, ")
            // sb.AppendLine("  PBR_NOPB, ")
            // sb.AppendLine("  PBR_TGLPB, ")
            // sb.AppendLine("  PBR_NAMADIVISI, ")
            // sb.AppendLine("  PBR_KODEDIVISI, ")
            // sb.AppendLine("  PBL_ITEM, ")
            // sb.AppendLine("  PBL_NILAI, ")
            // sb.AppendLine("  PBL_PPN, ")
            // sb.AppendLine("  PBL_SUBTOTAL, ")
            // sb.AppendLine("  PBL_CREATE_BY, ")
            // sb.AppendLine("  PBL_CREATE_DT ")
            // sb.AppendLine("  ) ")
            // sb.AppendLine("Select '" & KodeToko & "' as KODETOKO, ")
            // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
            // sb.AppendLine("       TO_DATE('" & tglPB & "','DD-MM-YYYY') as TglPB, ")
            // sb.AppendLine("       DIV_NamaDivisi as NamaDivisi, ")
            // sb.AppendLine("       PRD_KodeDivisi as KodeDivisi, ")
            // sb.AppendLine("       Count(PLUKARTON) as Item, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)))) as Nilai, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi, tbMaster_MarginPLUIDM ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and DIV_KodeDivisi = PRD_KodeDivisi ")
            // sb.AppendLine("   and MPI_PluIGR = PLUKARTON ")
            // sb.AppendLine(" Group By DIV_NamaDivisi, ")
            // sb.AppendLine("          PRD_KodeDivisi ")

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_REKAPORDER")
            // '---- 24-03-2014

            // sb = New StringBuilder
            // sb.AppendLine("Select DIV_NamaDivisi as NamaDivisi, ")
            // sb.AppendLine("       PRD_KodeDivisi as KodeDivisi, ")
            // sb.AppendLine("       Count(PLUKARTON) as Item, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)))) as Nilai, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi, tbMaster_MarginPLUIDM ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and DIV_KodeDivisi = PRD_KodeDivisi ")
            // sb.AppendLine("   and MPI_PluIGR = PLUKARTON ")
            // sb.AppendLine(" Group By DIV_NamaDivisi, ")
            // sb.AppendLine("          PRD_KodeDivisi ")
            // sb.AppendLine(" Order By PRD_KodeDivisi ")
        }else{
            // sb.AppendLine("INSERT INTO PBIDM_REKAPORDER  ")
            // sb.AppendLine("  ( ")
            // sb.AppendLine("  PBR_KODETOKO, ")
            // sb.AppendLine("  PBR_NOPB, ")
            // sb.AppendLine("  PBR_TGLPB, ")
            // sb.AppendLine("  PBR_NAMADIVISI, ")
            // sb.AppendLine("  PBR_KODEDIVISI, ")
            // sb.AppendLine("  PBL_ITEM, ")
            // sb.AppendLine("  PBL_NILAI, ")
            // sb.AppendLine("  PBL_PPN, ")
            // sb.AppendLine("  PBL_SUBTOTAL, ")
            // sb.AppendLine("  PBL_CREATE_BY, ")
            // sb.AppendLine("  PBL_CREATE_DT ")
            // sb.AppendLine("  ) ")
            // sb.AppendLine("Select '" & KodeToko & "' as KODETOKO ")
            // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
            // sb.AppendLine("       TO_DATE('" & tglPB & "','YYYYMMDD') as TglPB, ")
            // sb.AppendLine("       DIV_NamaDivisi as NamaDivisi, ")
            // sb.AppendLine("       PRD_KodeDivisi as KodeDivisi, ")
            // sb.AppendLine("       Count(PLUKARTON) as Item, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & "))) as Nilai, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & ") * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & ") * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and DIV_KodeDivisi = PRD_KodeDivisi ")
            // sb.AppendLine(" Group By DIV_NamaDivisi, ")
            // sb.AppendLine("          PRD_KodeDivisi ")

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_REKAPORDER")
            // '---- 24-03-2014

            // sb = New StringBuilder
            // sb.AppendLine("Select DIV_NamaDivisi as NamaDivisi, ")
            // sb.AppendLine("       PRD_KodeDivisi as KodeDivisi, ")
            // sb.AppendLine("       Count(PLUKARTON) as Item, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & "))) as Nilai, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & ") * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN, ")
            // sb.AppendLine("       SUM(fdqtyb * round(avgcost * (1+" & PersenMargin & ") * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL ")
            // sb.AppendLine("  From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
            // sb.AppendLine("   and prd_prdcd = plukarton ")
            // sb.AppendLine("   and DIV_KodeDivisi = PRD_KodeDivisi ")
            // sb.AppendLine(" Group By DIV_NamaDivisi, ")
            // sb.AppendLine("          PRD_KodeDivisi ")
            // sb.AppendLine(" Order By PRD_KodeDivisi ")
        }
    }

    public function CetakAll_3(){
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        // f = "KARTON NON DPD"
        // sb.AppendLine("INSERT INTO PBIDM_KARTONNONDPD")
        // sb.AppendLine("( ")
        // sb.AppendLine("  PBD_KODETOKO, ")
        // sb.AppendLine("  PBD_NOPB, ")
        // sb.AppendLine("  PBD_TGLPB, ")
        // sb.AppendLine("  PBD_NAMAGROUP, ")
        // sb.AppendLine("  PBD_KODERAK, ")
        // sb.AppendLine("  PBD_SUBRAK, ")
        // sb.AppendLine("  PBD_TIPERAK, ")
        // sb.AppendLine("  PBD_PLU, ")
        // sb.AppendLine("  PBD_NOURUT, ")
        // sb.AppendLine("  PBD_DESKRIPSI, ")
        // sb.AppendLine("  PBD_TAG, ")
        // sb.AppendLine("  PBD_QTY, ")
        // sb.AppendLine("  PBD_UNIT, ")
        // sb.AppendLine("  PBD_FRAC, ")
        // sb.AppendLine("  PBD_STOK,  ")
        // sb.AppendLine("  PBD_CREATE_BY, ")
        // sb.AppendLine("  PBD_CREATE_DT ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeToko & "' as KODETOKO, ")
        // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
        // sb.AppendLine("       TO_DATE('" & tglPB & "','DD-MM-YYYY') as TglPB, ")
        // sb.AppendLine("       GRR_GroupRak as NamaGroup, ")
        // sb.AppendLine("       LKS_KodeRak as KodeRak, ")
        // sb.AppendLine("       LKS_KodeSubRak as SubRak, ")
        // sb.AppendLine("       LKS_TipeRak as TipeRak, ")
        // sb.AppendLine("       PLUKARTON as PLU, ")
        // sb.AppendLine("       LKS_NoUrut as NoUrut, ")
        // sb.AppendLine("       Desk, ")
        // sb.AppendLine("       PRD_KodeTag as TAG, ")
        // sb.AppendLine("       QTYB as ""Order"", ")
        // sb.AppendLine("       UNITKarton , ")
        // sb.AppendLine("       FracKarton, ")
        // sb.AppendLine("	      Stok, ")
        // sb.AppendLine("       '" & UserID & "', ")
        // sb.AppendLine("       CURRENT_DATE ")
        // sb.AppendLine("  From temp_karton_nondpd_idm,tbMaster_Prodmast ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
        // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PRD_PRDCD = PLUKARTON ")

        // ExecQRY(sb.ToString, "INSERT INTO PBIDM_KARTONNONDPD")

        // sb = New StringBuilder
        // sb.AppendLine("Select DISTINCT GRR_GroupRak as NamaGroup, ")
        // sb.AppendLine("       LKS_KodeRak as KodeRak, ")
        // sb.AppendLine("       LKS_KodeSubRak as SubRak, ")
        // sb.AppendLine("       LKS_TipeRak as TipeRak, ")
        // sb.AppendLine("       PLUKARTON as PLU, ")
        // sb.AppendLine("       LKS_NoUrut as NoUrut, ")
        // sb.AppendLine("       Desk, ")
        // sb.AppendLine("       PRD_KodeTag as TAG, ")
        // sb.AppendLine("       QTYB as ""Order"", ")
        // sb.AppendLine("       UNITKarton ||'/'|| FracKarton as UNIT, ")
        // sb.AppendLine("	      Stok ")
        // sb.AppendLine("  From temp_karton_nondpd_idm,tbMaster_Prodmast ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
        // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PRD_PRDCD = PLUKARTON ")
        // sb.AppendLine(" Order By LKS_KodeRak,LKS_KodeSubRak,LKS_TipeRak,LKS_NoUrut ")
    }

    public function CetakAll_4(){
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        // f = "ORDER DITOLAK"
        // sb.AppendLine("Select PLU as PLUIDM, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       PRD_DeskripsiPanjang as DESK, ")
        // sb.AppendLine("       PRD_UNIT||'/'||PRD_Frac as UNIT, ")
        // sb.AppendLine("       QTYO as QTY, ")
        // sb.AppendLine("       KETA as Keterangan         ")
        // sb.AppendLine("  From temp_cetakpb_tolakan_idm,tbMaster_Prodmast ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And KCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And NODOK = '" & noPB & "' ")
        // sb.AppendLine("   And TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PRD_PRDCD = PLUIGR ")
        // sb.AppendLine("   And KETA <> 'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM' ")
    }

    public function CetakAll_5(){
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        // f = "RAK JALUR TIDAK KETEMU";
        // sb.AppendLine("INSERT INTO PBIDM_RAKJALUR_TIDAKKETEMU")
        // sb.AppendLine("( ")
        // sb.AppendLine("  PBT_KODETOKO, ")
        // sb.AppendLine("  PBT_NOPB, ")
        // sb.AppendLine("  PBT_TGLPB, ")
        // sb.AppendLine("  PBT_PLU, ")
        // sb.AppendLine("  PBT_DESKRIPSI, ")
        // sb.AppendLine("  PBT_KODERAK, ")
        // sb.AppendLine("  PBT_SUBRAK, ")
        // sb.AppendLine("  PBT_TIPERAK, ")
        // sb.AppendLine("  PBT_SHELVINGRAK, ")
        // sb.AppendLine("  PBT_QTYB, ")
        // sb.AppendLine("  PBT_QTYK, ")
        // sb.AppendLine("  PBT_UNITKARTON, ")
        // sb.AppendLine("  PBT_FRACKARTON, ")
        // sb.AppendLine("  PBT_RECORDID, ")
        // sb.AppendLine("  PBT_CREATE_BY, ")
        // sb.AppendLine("  PBT_CREATE_DT ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select DISTINCT '" & KodeToko & "' as KODETOKO, ")
        // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
        // sb.AppendLine("       TO_DATE('" & tglPB & "','DD-MM-YYYY') as TglPB, ")
        // sb.AppendLine("       NJI.PluKarton as PLU, ")
        // sb.AppendLine("       NJI.DESK, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeRak,'') END as KodeRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeSubrak,'') END as SubRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_TipeRak,'') END as TipeRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_ShelvingRak,'') END as ShelvingRak, ")
        // sb.AppendLine("       NJI.QTYB as OrderCTN, ")
        // sb.AppendLine("       NJI.QTYK as OrderPCS, ")
        // sb.AppendLine("       NJI.UnitKarton, ")
        // sb.AppendLine("       NJI.FracKarton, ")
        // sb.AppendLine("       coalesce(NJI.FDRCID,'X') as RECID, ")
        // sb.AppendLine("       '" & UserID & "', ")
        // sb.AppendLine("       CURRENT_DATE ")
        // sb.AppendLine("  From TEMP_NOJALUR_IDM NJI ") ', tbMaster_Lokasi ")
        // sb.AppendLine("  join tbMaster_Lokasi on LKS_PRDCD = PLUKARTON ")
        // sb.AppendLine(" Where LKS_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And LKS_KodeRak Not Like 'D%' ")
        // sb.AppendLine("   And REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        // sb.AppendLine("   And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("  And Not EXISTS ")
        // sb.AppendLine("    ( ")
        // sb.AppendLine("       Select grr_grouprak ")
        // sb.AppendLine("         from tbmaster_grouprak,tbmaster_lokasi lks2 ")
        // sb.AppendLine("        where grr_koderak = lks2.lks_koderak ")
        // sb.AppendLine("          and grr_subrak = lks2.lks_kodesubrak ")
        // sb.AppendLine("          and LKS_KodeRak Like 'D%' ")
        // sb.AppendLine("          And LKS_TIPERAK NOT LIKE 'S%' ")
        // sb.AppendLine("          and lks_prdcd = plukarton ")
        // sb.AppendLine("    )  ")

        // ExecQRY(sb.ToString, "INSERT INTO PBIDM_RAKJALUR_TIDAKKETEMU")


        // sb = New StringBuilder
        // sb.AppendLine("Select DISTINCT NJI.PluKarton as PLU, ")
        // sb.AppendLine("       NJI.DESK, ")

        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeRak,'') END as KodeRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeSubrak,'') END as SubRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_TipeRak,'') END as TipeRak, ")
        // sb.AppendLine("       CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_ShelvingRak,'') END as ShelvingRak, ")
        // sb.AppendLine("       NJI.QTYB as OrderCTN, ")
        // sb.AppendLine("       NJI.QTYK as OrderPCS, ")
        // sb.AppendLine("       NJI.UnitKarton||'/'||NJI.FracKarton as UNIT,  ")
        // sb.AppendLine("       coalesce(NJI.FDRCID,'X') as RECID ")
        // sb.AppendLine("  From TEMP_NOJALUR_IDM NJI, tbMaster_Lokasi ")
        // sb.AppendLine(" Where LKS_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And LKS_PRDCD = PLUKARTON ")
        // sb.AppendLine("   And LKS_KodeRak Not Like 'D%' ")
        // sb.AppendLine("   And REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        // sb.AppendLine("   And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("  And Not EXISTS ")
        // sb.AppendLine("    ( ")
        // sb.AppendLine("       Select grr_grouprak ")
        // sb.AppendLine("         from tbmaster_grouprak,tbmaster_lokasi lks2 ")
        // sb.AppendLine("        where grr_koderak = lks2.lks_koderak ")
        // sb.AppendLine("          and grr_subrak = lks2.lks_kodesubrak ")
        // sb.AppendLine("          and LKS_KodeRak Like 'D%' ")
        // sb.AppendLine("          And LKS_TIPERAK NOT LIKE 'S%' ")
        // sb.AppendLine("          and lks_prdcd = plukarton ")
        // sb.AppendLine("    )  ")
        // sb.AppendLine("  Order By NJI.PLUKarton ")
    }

    public function CetakAll_6(){
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        // f = "JALUR CETAK KERTAS"
        // sb.AppendLine("INSERT INTO PBIDM_JALURKERTAS ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  PBK_KODETOKO, ")
        // sb.AppendLine("  PBK_NOPB, ")
        // sb.AppendLine("  PBK_TGLPB, ")
        // sb.AppendLine("  PBK_NAMAGROUP, ")
        // sb.AppendLine("  PBK_KODERAK, ")
        // sb.AppendLine("  PBK_SUBRAK, ")
        // sb.AppendLine("  PBK_TIPERAK, ")
        // sb.AppendLine("  PBK_PLU, ")
        // sb.AppendLine("  PBK_NOURUT, ")
        // sb.AppendLine("  PBK_DESKRIPSI, ")
        // sb.AppendLine("  PBK_TAG, ")
        // sb.AppendLine("  PBK_QTY, ")
        // sb.AppendLine("  PBK_UNIT, ")
        // sb.AppendLine("  PBK_FRAC, ")
        // sb.AppendLine("  PBK_STOK, ")
        // sb.AppendLine("  PBK_CREATE_BY, ")
        // sb.AppendLine("  PBK_CREATE_DT ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select DISTINCT '" & KodeToko & "' as KODETOKO, ")
        // sb.AppendLine("       '" & noPB & "' as NoPB,  ")
        // sb.AppendLine("       TO_DATE('" & tglPB & "','DD-MM-YYYY') as TglPB, ")
        // sb.AppendLine("       GRR_GroupRak as NamaGroup, ")
        // sb.AppendLine("       PLUKARTON as PLU, ")
        // sb.AppendLine("       LKS_KodeRak as KodeRak, ")
        // sb.AppendLine("       LKS_KodeSubRak as Subrak, ")
        // sb.AppendLine("       LKS_TipeRak as TipeRak, ")
        // sb.AppendLine("       LKS_NoUrut as NoUrut, ")
        // sb.AppendLine("       DESK, ")
        // sb.AppendLine("       PRD_KodeTag, ")
        // sb.AppendLine("       QTYK as ""ORDER"", ")
        // sb.AppendLine("       UNITKECIL, ")
        // sb.AppendLine("       FRACKECIL, ")
        // sb.AppendLine("       STOK, ")
        // sb.AppendLine("       '" & UserID & "', ")
        // sb.AppendLine("       CURRENT_DATE ")
        // sb.AppendLine("  From TEMP_JALURKERTAS_IDM, tbMaster_Prodmast   ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
        // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PRD_PRDCD = PLUKARTON  ")
        // ExecQRY(sb.ToString, "INSERT INTO PBIDM_JALURKERTAS")

        // sb = New StringBuilder
        // sb.AppendLine("Select GRR_GroupRak as NamaGroup, ")
        // sb.AppendLine("       PLUKARTON as PLU, ")
        // sb.AppendLine("       LKS_KodeRak as KodeRak, ")
        // sb.AppendLine("       LKS_KodeSubRak as Subrak, ")
        // sb.AppendLine("       LKS_TipeRak as TipeRak, ")
        // sb.AppendLine("       LKS_NoUrut as NoUrut, ")
        // sb.AppendLine("       DESK, ")
        // sb.AppendLine("       PRD_KodeTag, ")
        // sb.AppendLine("       QTYK as ""ORDER"", ")
        // sb.AppendLine("       UNITKECIL ||' /'|| FRACKECIL as UNIT, ")
        // sb.AppendLine("       STOK ")
        // sb.AppendLine("  From TEMP_JALURKERTAS_IDM, tbMaster_Prodmast   ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
        // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PRD_PRDCD = PLUKARTON  ")
        // sb.AppendLine(" Order By coalesce(GRR_GROUPRAK,'0'),LKS_KodeRak,LKS_KodeSubRak,LKS_TipeRak,LKS_NoUrut ")

    }

    public function prosesPBIDM($ip, $kodeToko, $noPB, $tglPB, $KodeMember, $chkIDMBacaProdcrm, $kodeDCIDM){

        //! DEL TEMP_CETAKPB_TOLAKAN_IDM
        // DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM WHERE req_id = '" & IP & "' "

        DB::table('TEMP_CETAKPB_TOLAKAN_IDM')
            ->where('req_id', $ip)
            ->delete();

        // sb.AppendLine("Select TKO_KodeCustomer ")
        // sb.AppendLine("  From tbMaster_tokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data = DB::table('tbMaster_tokoIGR')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko
            ])
            ->whereRaw("COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first();

        // if(empty){
        //     ("Kode Toko " & KodeToko & " Tidak Terdaftar Di TbMaster_TokoIGR
        // }

        if(empty($data)){
            $message = "Kode Toko $kodeToko Tidak Terdaftar Di TbMaster_TokoIGR";
            throw new HttpResponseException(ApiFormatter::error(400, $message));
        }

        //! GET -> KodeSBU
        // sb.AppendLine("Select TKO_KodeSBU ")
        // sb.AppendLine("  From tbMaster_tokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $KodeSBU = DB::table('tbMaster_tokoIGR')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko
            ])
            ->whereRaw("COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->TKO_KodeSBU;

        //! GET -> PersenMargin
        // sb.AppendLine("Select coalesce(tko_persenmargin::numeric,3) / 100 ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $PersenMargin = DB::table('tbMaster_TokoIGR')
            ->selectRaw("coalesce(tko_persenmargin::numeric,3) / 100 as tko_persenmargin")
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko
            ])
            ->whereRaw("COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_persenmargin;

        //! GET -> jum
        // sb.AppendLine("Select COALESCE(count(1),0) ")
        // sb.AppendLine("  From TBTR_HEADER_POT ")
        // sb.AppendLine(" Where HDP_KodeIGR='" & KDIGR & "' ")
        // sb.AppendLine("   AND HDP_KodeToko = '" & KodeToko & "' ")
        // sb.AppendLine("   AND HDP_NoPB = '" & noPB & "' ")
        // sb.AppendLine("    AND to_char(HDP_TGLPB,'YYYY') = '" & Strings.Right(tglPB, 4) & "' ")

        $data = DB::table('TBTR_HEADER_POT')
                ->where([
                    'HDP_KodeIGR' => session('KODECABANG'),
                    'HDP_KodeToko' => $kodeToko,
                    'HDP_NoPB' => $noPB,
                ])
                ->whereYear('HDP_TGLPB', Carbon::parse($tglPB)->format('Y'))
                ->count();

        // if(jum > 0){
        //     PB Dengan No = " & noPB & ", KodeTOKO = " & KodeToko & " Sudah Pernah Diproses !
        // }

        if($data > 0){
            $message = "PB Dengan No = $noPB, KodeTOKO = $kodeToko Sudah Pernah Diproses !";
            throw new HttpResponseException(ApiFormatter::error(400, $message));
        }

        //! GET -> kodeDCIDM
        // $this->getKodeDC(KodeToko)

        $this->getKodeDC($kodeToko);

        //! ISI PLU TIDAK TERDAFTAR DI PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      cpp_nopb, ")
        // sb.AppendLine("	      cpp_tglpb, ")
        // sb.AppendLine("	      cpp_pluidm, ")
        // sb.AppendLine("	      null, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      'PLU TIDAK TERDAFTAR DI TBMASTER_PRODCRM', ")
        // Else
        //     sb.AppendLine("	      'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM', ")
        // End If
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      cpp_qty, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      cpp_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // sb.AppendLine("  From csv_pb_pot ")
        // sb.AppendLine(" Where not exists ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine(" ( ")
        //     sb.AppendLine("    Select KAT_PluIGR ")
        //     sb.AppendLine("      From KONVERSI_ATK ")
        //     sb.AppendLine("     WHERE KAT_PLUIDM = CPP_PLUIDM ")
        //     sb.AppendLine("       AND EXISTS ( ")
        //     sb.AppendLine("         SELECT st_prdcd ")
        //     sb.AppendLine("         FROM tbmaster_stock ")
        //     sb.AppendLine("         WHERE st_prdcd = kat_pluigr ")
        //     sb.AppendLine("         AND st_lokasi = '01' ")
        //     sb.AppendLine("       ) ")
        //     sb.AppendLine(" ) ")
        // Else
        //     sb.AppendLine(" ( ")
        //     sb.AppendLine("   SELECT IDM_PLUIDM  ")
        //     sb.AppendLine("     FROM TBTEMP_PLUIDM ")
        //     sb.AppendLine("    WHERE IDM_PLUIDM = cpp_pluidm ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("      AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        //     sb.AppendLine(" ) ")
        // End If
        // sb.AppendLine("   AND CPP_IP = '" & IP & "'")
        // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "'")
        // sb.AppendLine("   AND CPP_NoPB = '" & noPB & "'")
        // sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        $data = DB::table('csv_pb_pot')
            ->selectRaw("
                cpp_nopb,
                cpp_tglpb,
                cpp_pluidm,
                cpp_qty,
                cpp_KodeToko,
            ")
            ->where([
                'CPP_IP' => $ip,
                'CPP_KodeToko' => $kodeToko,
                'CPP_NoPB' => $noPB,
            ])
            ->whereRaw("CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')");

            if($chkIDMBacaProdcrm){
                $data = $data->whereNotExists(DB::raw("
                    Select KAT_PluIGR
                    From KONVERSI_ATK
                    WHERE KAT_PLUIDM = CPP_PLUIDM
                    AND EXISTS (
                        SELECT st_prdcd
                        FROM tbmaster_stock
                        WHERE st_prdcd = kat_pluigr
                        AND st_lokasi = '01'
                    )
                "));
            }else{

                $subquery = "
                    SELECT IDM_PLUIDM
                    FROM TBTEMP_PLUIDM
                    WHERE IDM_PLUIDM = cpp_pluidm
                ";

                if($kodeDCIDM <> ""){
                    $subquery .= "AND IDM_KDIDM = '" & $kodeDCIDM & "'";
                }

                $data = $data->whereNotExists(DB::raw($subquery));
            }

        $data = $data->get();

        foreach($data as $item){
            DB::table('TEMP_CETAKPB_TOLAKAN_IDM')
                ->insert([
                    'KOMI' => $KodeMember,
                    'TGL' => Carbon::now(),
                    'NODOK' => $item->cpp_nopb,
                    'TGLDOK' => $item->cpp_tglpb,
                    'PLU' => $item->cpp_pluidm,
                    'PLUIGR' => null,
                    'KETA' => $chkIDMBacaProdcrm ? 'PLU TIDAK TERDAFTAR DI TBMASTER_PRODCRM' : 'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM',
                    'TAG' => null,
                    'DESCR' => null,
                    'QTYO' => $item->cpp_qty,
                    'GROSS' => null,
                    'KCAB' => $item->cppKodeToko,
                    'KODEIGR' => session('KODECABANG'),
                    'REQ_ID' => $ip,
                ]);
        }

        //! PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      CPP_NoPB, ")
        // sb.AppendLine("	      CPP_TglPB, ")
        // sb.AppendLine("	      CPP_PLUIDM, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      'PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR', ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_Qty, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // sb.AppendLine("  From csv_pb_pot ")
        // sb.AppendLine(" Where exists ")
        // sb.AppendLine(" ( ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("    Select KAT_PluIGR ")
        //     sb.AppendLine("      From KONVERSI_ATK ")
        //     sb.AppendLine("     WHERE KAT_PLUIDM = CPP_PLUIDM  ")
        //     sb.AppendLine("       AND KAT_PLUIGR IS NULL ")
        // Else
        //     sb.AppendLine("   SELECT IDM_PLUIDM  ")
        //     sb.AppendLine("     FROM TBTEMP_PLUIDM ")
        //     sb.AppendLine("    WHERE IDM_PLUIDM = cpp_pluidm ")
        //     sb.AppendLine("      AND IDM_PLUIGR IS NULL ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("      AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        // End If
        // sb.AppendLine(" ) ")
        // sb.AppendLine("   AND CPP_IP = '" & IP & "'")
        // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "'")
        // sb.AppendLine("   AND CPP_NoPB = '" & noPB & "'")
        // sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        $data = DB::table('csv_pb_pot')
            ->selectRaw("
                CPP_NoPB,
                CPP_TglPB,
                CPP_PLUIDM,
                CPP_Qty,
                CPP_KodeToko,
            ")
            ->where([
                'CPP_IP' => $ip,
                'CPP_KodeToko' => $kodeToko,
                'CPP_NoPB' => $noPB,
            ])
            ->whereRaw("CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')");

            if($chkIDMBacaProdcrm){
                $data = $data->whereNotExists(DB::raw("
                    Select KAT_PluIGR
                    From KONVERSI_ATK
                    WHERE KAT_PLUIDM = CPP_PLUIDM
                    AND KAT_PLUIGR IS NULL
                "));
            }else{

                $subquery = "
                    SELECT IDM_PLUIDM
                    FROM TBTEMP_PLUIDM
                    WHERE IDM_PLUIDM = cpp_pluidm
                    AND IDM_PLUIGR IS NULL
                ";

                if($kodeDCIDM <> ""){
                    $subquery .= "AND IDM_KDIDM = '" & $kodeDCIDM & "'";
                }

                $data = $data->whereNotExists(DB::raw($subquery));
            }

        $data = $data->get();

        foreach($data as $item){
            DB::table('TEMP_CETAKPB_TOLAKAN_IDM')
                ->insert([
                    'KOMI' => $KodeMember,
                    'TGL' => Carbon::now(),
                    'NODOK' => $item->cpp_nopb,
                    'TGLDOK' => $item->cpp_tglpb,
                    'PLU' => $item->cpp_pluidm,
                    'PLUIGR' => null,
                    'KETA' => 'PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR',
                    'TAG' => null,
                    'DESCR' => null,
                    'QTYO' => $item->cpp_qty,
                    'GROSS' => null,
                    'KCAB' => $item->cppKodeToko,
                    'KODEIGR' => session('KODECABANG'),
                    'REQ_ID' => $ip,
                ]);
        }

        //! ==================
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      CPP_NoPB, ")
        // sb.AppendLine("	      CPP_TglPB, ")
        // sb.AppendLine("	      CPP_PLUIDM, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR, ")
        //     sb.AppendLine("	      'PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST', ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR, ")
        //     sb.AppendLine("	      'PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST', ")
        // End If
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_QTY, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM csv_pb_pot, KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("	   AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("	   AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("	   AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT PRD_PRDCD  ")
        //     sb.AppendLine("        FROM tbMaster_ProdMast ")
        //     sb.AppendLine("       Where PRD_PRDCD = KAT_PLUIGR ")
        //     sb.AppendLine("         And PRD_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")

        //     ExecQRY(sb.ToString, "PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST")
        // Else
        //     sb.AppendLine("	 FROM csv_pb_pot,TBTEMP_PLUIDM  ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT PRD_PRDCD  ")
        //     sb.AppendLine("        FROM tbMaster_ProdMast ")
        //     sb.AppendLine("       Where PRD_PRDCD = IDM_PLUIGR ")
        //     sb.AppendLine("         And PRD_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If

        //     ExecQRY(sb.ToString, "PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST")
        // End If
        //! ===================


        //! AVG.COST <= 0 - 1
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      CPP_NoPB, ")
        // sb.AppendLine("	      CPP_TglPB, ")
        // sb.AppendLine("	      CPP_PLUIDM, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR, ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR, ")
        // End If
        // sb.AppendLine("	      'AVG.COST IS NULL', ")
        // sb.AppendLine("	      PRD_KodeTag, ")
        // sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60), ")
        // sb.AppendLine("	      CPP_QTY, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		  AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		  AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		  AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT ST_AvgCost  ")
        //     sb.AppendLine("        FROM tbMaster_Stock  ")
        //     sb.AppendLine("       Where ST_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("         And ST_Lokasi = '01'  ")
        //     sb.AppendLine("         And ST_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("         And ST_AvgCost IS NOT NULL ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = KAT_PLUIGR ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        // Else
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,TBTEMP_PLUIDM  ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		  AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		  AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		  AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   ) ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT ST_AvgCost  ")
        //     sb.AppendLine("        FROM tbMaster_Stock  ")
        //     sb.AppendLine("       Where ST_PRDCD Like SUBSTR(IDM_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("         And ST_Lokasi = '01'  ")
        //     sb.AppendLine("         And ST_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("         And ST_AvgCost IS NOT NULL ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = IDM_PLUIGR ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        // End If


        //! AVG.COST <= 0 - 2
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      CPP_NOPB, ")
        // sb.AppendLine("	      CPP_TglPB, ")
        // sb.AppendLine("	      CPP_PLUIDM, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR, ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR, ")
        // End If
        // sb.AppendLine("	      'AVG.COST <= 100', ")
        // sb.AppendLine("	      PRD_KodeTag, ")
        // sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60), ")
        // sb.AppendLine("	      CPP_QTY, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT ST_AvgCost  ")
        //     sb.AppendLine("        FROM tbMaster_Stock  ")
        //     sb.AppendLine("       Where ST_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("         And ST_Lokasi = '01'  ")
        //     sb.AppendLine("         And ST_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("         And COALESCE(ST_AvgCost,0) <= 100 ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("    SELECT PHI_PRDCD ")
        //     sb.AppendLine("      FROM PLU_HADIAH_IDM ")
        //     sb.AppendLine("     WHERE PHI_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = KAT_PLUIGR ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        // Else
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,TBTEMP_PLUIDM  ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("      SELECT ST_AvgCost  ")
        //     sb.AppendLine("        FROM tbMaster_Stock  ")
        //     sb.AppendLine("       Where ST_PRDCD Like SUBSTR(IDM_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("         And ST_Lokasi = '01'  ")
        //     sb.AppendLine("         And ST_KodeIGR = '" & KDIGR & "'  ")
        //     sb.AppendLine("         And COALESCE(ST_AvgCost,0) <= 100 ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("    SELECT PHI_PRDCD ")
        //     sb.AppendLine("      FROM PLU_HADIAH_IDM ")
        //     sb.AppendLine("     WHERE PHI_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = IDM_PLUIGR ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        // End If


        //! STOCK EKONOMIS POT TIDAK MENCUKUPI
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeMember & "', ")
        // sb.AppendLine("       CURRENT_DATE,  ")
        // sb.AppendLine("	      CPP_NoPB, ")
        // sb.AppendLine("	      CPP_TglPB, ")
        // sb.AppendLine("	      CPP_PLUIDM, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR, ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR, ")
        // End If
        // sb.AppendLine("	      'STOCK EKONOMIS POT TIDAK MENCUKUPI', ")
        // sb.AppendLine("	      PRD_KodeTag, ")
        // sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60), ")
        // sb.AppendLine("	      CPP_QTY, ")
        // sb.AppendLine("	      null, ")
        // sb.AppendLine("	      CPP_KodeToko, ")
        // sb.AppendLine("	      '" & KDIGR & "', ")
        // sb.AppendLine("	      '" & IP & "' ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		  AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		  AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		  AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )    ")
        //     sb.AppendLine("   AND CPP_TOLAKAN_SEP > 0 ")
        //     sb.AppendLine("   AND CPP_STOCK <= 0 ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = KAT_PLUIGR ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        // Else
        //     sb.AppendLine("	 FROM csv_pb_pot, TBMASTER_PRODMAST,TBTEMP_PLUIDM  ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   ( ")
        //     sb.AppendLine("   SELECT PLUIGR  ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        //     sb.AppendLine("		  AND NODOK = '" & noPB & "' ")
        //     sb.AppendLine("		  AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("		  AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   ) ")
        //     sb.AppendLine("   AND CPP_TOLAKAN_SEP > 0 ")
        //     sb.AppendLine("   AND CPP_STOCK <= 0 ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     sb.AppendLine("   AND PRD_PRDCD = IDM_PLUIGR ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        // End If

        //! GET -> jum
        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where upper(table_name) = 'TEMP_CETAKPB_TOLAKAN_IDM2' ")

        // if(jum = 0 ){
        //     sb.AppendLine("CREATE TABLE TEMP_CETAKPB_TOLAKAN_IDM2 ")
        //     sb.AppendLine("AS ")
        //     sb.AppendLine("SELECT KOMI, ")
        //     sb.AppendLine("       TGL, ")
        //     sb.AppendLine("       NODOK, ")
        //     sb.AppendLine("       TGLDOK, ")
        //     sb.AppendLine("       PLU, ")
        //     sb.AppendLine("       PLUIGR, ")
        //     sb.AppendLine("       KETA, ")
        //     sb.AppendLine("       PRD_KODETAG AS TAG, ")
        //     sb.AppendLine("       DESCR, ")
        //     sb.AppendLine("       QTYO, ")
        //     sb.AppendLine("       KCAB, ")
        //     sb.AppendLine("       KODEIGR, ")
        //     sb.AppendLine("       REQ_ID ")
        //     sb.AppendLine("FROM ")
        //     sb.AppendLine("( ")
        //     sb.AppendLine("Select '" & KodeMember & "' as KOMI,  ")
        //     sb.AppendLine("       CURRENT_DATE as TGL,   ")
        //     sb.AppendLine("	    CPP_NoPB as NODOK,  ")
        //     sb.AppendLine("	    CPP_TglPB as TGLDOK,  ")
        //     sb.AppendLine("	    CPP_PLUIDM as PLU,  ")
        //     If chkIDMBacaProdcrm.Checked Then
        //         sb.AppendLine("	      KAT_PLUIGR as PLUIGR,  ")
        //     Else
        //         sb.AppendLine("	      IDM_PLUIGR as PLUIGR,  ")
        //     End If
        //     sb.AppendLine("	      'PRODMAST IGR DISCONTINUE Tag:NXQ' as KETA, ")
        //     sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60) as DESCR,  ")
        //     sb.AppendLine("	      CPP_QTY as QTYO, ")
        //     sb.AppendLine("	      CPP_KodeToko as KCAB, ")
        //     sb.AppendLine("	      '" & KDIGR & "' as KODEIGR, ")
        //     sb.AppendLine("	      '" & IP & "' as REQ_ID, ")
        //     sb.AppendLine("         Min(PRD_PRDCD) AS PLUKECIL  ")
        //     If chkIDMBacaProdcrm.Checked Then
        //         sb.AppendLine("  FROM CSV_PB_POT, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //         sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //         sb.AppendLine("   AND CPP_KodeTOKO = '" & KodeToko & "'  ")
        //         sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //         sb.AppendLine("   AND NOT EXISTS  ")
        //         sb.AppendLine("   (  ")
        //         sb.AppendLine("   SELECT PLUIGR ")
        //         sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //         sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //         sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //         sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //         sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //         sb.AppendLine("		 AND PLU = CPP_PLUIDM ")
        //         sb.AppendLine("   )     ")
        //         sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM  ")
        //         sb.AppendLine("   AND PRD_PRDCD like SubStr(KAT_PLUIGR,1,6)||'%' ")
        //         sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        //         sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ")
        //         sb.AppendLine(" GROUP BY CPP_NOPB,  ")
        //         sb.AppendLine("	        CPP_TGLPB,  ")
        //         sb.AppendLine("	        CPP_PLUIDM,  ")
        //         sb.AppendLine("	        KAT_PLUIGR, ")
        //         sb.AppendLine("	        SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //         sb.AppendLine("	        CPP_QTY, ")
        //         sb.AppendLine("	        CPP_KodeToko ")
        //         sb.AppendLine(") X,tbMaster_Prodmast ")
        //         sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //         sb.AppendLine("  AND PRD_KodeTag IN ('N','X','Q') ")
        //     Else
        //         sb.AppendLine("	 FROM CSV_PB_POT, TBMASTER_PRODMAST,TBTEMP_PLUIDM   ")
        //         sb.AppendLine(" WHERE CPP_IP = '" & IP & "'  ")
        //         sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "'  ")
        //         sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //         sb.AppendLine("   AND NOT EXISTS  ")
        //         sb.AppendLine("   (  ")
        //         sb.AppendLine("   SELECT PLUIGR ")
        //         sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //         sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //         sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //         sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //         sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //         sb.AppendLine("		 AND PLU = CPP_PLUIDM ")
        //         sb.AppendLine("   ) ")
        //         sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //         If kodeDCIDM <> "" Then
        //             sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //         End If
        //         sb.AppendLine("   AND PRD_PRDCD like SubStr(IDM_PLUIGR,1,6)||'%'  ")
        //         sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ")
        //         sb.AppendLine(" GROUP BY CPP_NoPB,  ")
        //         sb.AppendLine("	       CPP_TglPB,  ")
        //         sb.AppendLine("	       CPP_PLUIDM,  ")
        //         sb.AppendLine("	       IDM_PLUIGR, ")
        //         sb.AppendLine("	       SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //         sb.AppendLine("	       CPP_QTY, ")
        //         sb.AppendLine("	       CPP_KodeToko ")
        //         sb.AppendLine(") X,tbMaster_Prodmast ")
        //         sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //         sb.AppendLine("  AND PRD_KodeTag IN ('N','X','Q') ")
        //     End If
        //     ExecQRY(sb.ToString, "CREATE TABLE TEMP_CETAKPB_TOLAKAN_IDM2-PRODMAST-NXQ")
        // }else{
        //     sb.AppendLine("DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM2 ")
        //     sb.AppendLine(" WHERE REQ_ID = '" & IP & "' ")
        //     ExecQRY(sb.ToString, "DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM2")
        // }


        //! INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 - 1-PRODMAST-NXQ
        // sb.AppendLine("INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 ")
        // sb.AppendLine("SELECT KOMI, ")
        // sb.AppendLine("       TGL, ")
        // sb.AppendLine("       NODOK, ")
        // sb.AppendLine("       TGLDOK, ")
        // sb.AppendLine("       PLU, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       KETA, ")
        // sb.AppendLine("       PRD_KODETAG AS TAG, ")
        // sb.AppendLine("       DESCR, ")
        // sb.AppendLine("       QTYO, ")
        // sb.AppendLine("       KCAB, ")
        // sb.AppendLine("       KODEIGR, ")
        // sb.AppendLine("       REQ_ID ")
        // sb.AppendLine("FROM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("Select '" & KodeMember & "' as KOMI,  ")
        // sb.AppendLine("       CURRENT_DATE as TGL,   ")
        // sb.AppendLine("	      CPP_NoPB as NODOK,  ")
        // sb.AppendLine("	      CPP_TGLPB as TGLDOK,  ")
        // sb.AppendLine("	      CPP_PLUIDM as PLU,  ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR as PLUIGR,  ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR as PLUIGR,  ")
        // End If
        // sb.AppendLine("	      'PRODMAST IGR DISCONTINUE Tag:NXQ' as KETA, ")
        // sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60) as DESCR,  ")
        // sb.AppendLine("	      CPP_QTY as QTYO, ")
        // sb.AppendLine("	      CPP_KodeToko as KCAB, ")
        // sb.AppendLine("	      '" & KDIGR & "' as KODEIGR,  ")
        // sb.AppendLine("	      '" & IP & "' as REQ_ID, ")
        // sb.AppendLine("        Min(PRD_PRDCD) AS PLUKECIL  ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM CSV_PB_POT, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS  ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM  ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND NOT EXISTS  ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM2  ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM  ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM  ")
        //     sb.AppendLine("   AND PRD_PRDCD like SubStr(KAT_PLUIGR,1,6)||'%'  ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        //     sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ")
        //     sb.AppendLine(" GROUP BY CPP_NOPB,  ")
        //     sb.AppendLine("	        CPP_TGLPB,  ")
        //     sb.AppendLine("	        CPP_PLUIDM,  ")
        //     sb.AppendLine("	        KAT_PLUIGR,	                  	       ")
        //     sb.AppendLine("	        SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //     sb.AppendLine("	        CPP_QTY,  ")
        //     sb.AppendLine("	        CPP_KodeToko ")
        //     sb.AppendLine(") X,tbMaster_Prodmast ")
        //     sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //     sb.AppendLine("  AND PRD_KodeTag IN ('N','X','Q') ")
        // Else
        //     sb.AppendLine("	 FROM CSV_PB_POT, TBMASTER_PRODMAST,TBTEMP_PLUIDM ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM2  ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        //     sb.AppendLine("   AND PRD_PRDCD like SubStr(IDM_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0' ")
        //     sb.AppendLine(" GROUP BY CPP_NoPB,  ")
        //     sb.AppendLine("	       CPP_TglPB,  ")
        //     sb.AppendLine("	       CPP_PLUIDM,  ")
        //     sb.AppendLine("	       IDM_PLUIGR,	")
        //     sb.AppendLine("	       SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //     sb.AppendLine("	       CPP_QTY,  ")
        //     sb.AppendLine("	       CPP_KodeToko ")
        //     sb.AppendLine(") X,tbMaster_Prodmast ")
        //     sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //     sb.AppendLine("  AND PRD_KodeTag IN ('N','X','Q') ")
        // End If


        //! INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 - 1-FLAGAKTIVASI-X
        // sb.AppendLine("INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 ")
        // sb.AppendLine("SELECT KOMI, ")
        // sb.AppendLine("       TGL, ")
        // sb.AppendLine("       NODOK, ")
        // sb.AppendLine("       TGLDOK, ")
        // sb.AppendLine("       PLU, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       KETA, ")
        // sb.AppendLine("       PRD_KODETAG AS TAG, ")
        // sb.AppendLine("       DESCR, ")
        // sb.AppendLine("       QTYO, ")
        // sb.AppendLine("       KCAB, ")
        // sb.AppendLine("       KODEIGR, ")
        // sb.AppendLine("       REQ_ID ")
        // sb.AppendLine("FROM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("Select '" & KodeMember & "' as KOMI,  ")
        // sb.AppendLine("       CURRENT_DATE as TGL,   ")
        // sb.AppendLine("	      CPP_NoPB as NODOK,  ")
        // sb.AppendLine("	      CPP_TGLPB as TGLDOK,  ")
        // sb.AppendLine("	      CPP_PLUIDM as PLU,  ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR as PLUIGR,  ")
        // Else
        //     sb.AppendLine("	      IDM_PLUIGR as PLUIGR,  ")
        // End If
        // sb.AppendLine("	      'PRODMAST IGR FLAG AKTIVASI:X' as KETA, ")
        // sb.AppendLine("	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60) as DESCR,  ")
        // sb.AppendLine("	      CPP_QTY as QTYO, ")
        // sb.AppendLine("	      CPP_KodeToko as KCAB, ")
        // sb.AppendLine("	      '" & KDIGR & "' as KODEIGR,  ")
        // sb.AppendLine("	      '" & IP & "' as REQ_ID, ")
        // sb.AppendLine("        Min(PRD_PRDCD) AS PLUKECIL  ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	 FROM CSV_PB_POT, TBMASTER_PRODMAST,KONVERSI_ATK ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS  ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM  ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND NOT EXISTS  ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM2  ")
        //     sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	     AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		 AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		 AND PLU = CPP_PLUIDM  ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND CPP_PLUIDM = KAT_PLUIDM  ")
        //     sb.AppendLine("   AND PRD_PRDCD like SubStr(KAT_PLUIGR,1,6)||'%'  ")
        //     sb.AppendLine("   AND CPP_PLUIGR = KAT_PLUIGR ")
        //     sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ")
        //     sb.AppendLine(" GROUP BY CPP_NOPB,  ")
        //     sb.AppendLine("	        CPP_TGLPB,  ")
        //     sb.AppendLine("	        CPP_PLUIDM,  ")
        //     sb.AppendLine("	        KAT_PLUIGR,	                  	       ")
        //     sb.AppendLine("	        SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //     sb.AppendLine("	        CPP_QTY,  ")
        //     sb.AppendLine("	        CPP_KodeToko ")
        //     sb.AppendLine(") X,tbMaster_Prodmast ")
        //     sb.AppendLine(", TBMASTER_FLAGAKT ")
        //     sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //     sb.AppendLine("  AND prd_flag_aktivasi IN ('X') ")
        //     sb.AppendLine("  AND prd_flag_aktivasi = AKT_KODEFLAG ")
        // Else
        //     sb.AppendLine("	 FROM CSV_PB_POT, TBMASTER_PRODMAST,TBTEMP_PLUIDM ")
        //     sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("   AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("   AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM  ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND NOT EXISTS ")
        //     sb.AppendLine("   (  ")
        //     sb.AppendLine("   SELECT PLUIGR ")
        //     sb.AppendLine("	  FROM TEMP_CETAKPB_TOLAKAN_IDM2  ")
        //     sb.AppendLine("	 WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("	   AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("		AND NODOK = '" & noPB & "'  ")
        //     sb.AppendLine("		AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("		AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("   )     ")
        //     sb.AppendLine("   AND CPP_PLUIDM = IDM_PLUIDM ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("   AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        //     sb.AppendLine("   AND PRD_PRDCD like SubStr(IDM_PLUIGR,1,6)||'%' ")
        //     sb.AppendLine("   AND SubStr(PRD_PRDCD,-1,1) <> '0' ")
        //     sb.AppendLine(" GROUP BY CPP_NoPB,  ")
        //     sb.AppendLine("	       CPP_TglPB,  ")
        //     sb.AppendLine("	       CPP_PLUIDM,  ")
        //     sb.AppendLine("	       IDM_PLUIGR,	")
        //     sb.AppendLine("	       SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ")
        //     sb.AppendLine("	       CPP_QTY,  ")
        //     sb.AppendLine("	       CPP_KodeToko ")
        //     sb.AppendLine(") X,tbMaster_Prodmast ")
        //     sb.AppendLine(", TBMASTER_FLAGAKT ")
        //     sb.AppendLine("WHERE PRD_PRDCD = PLUKECIL ")
        //     sb.AppendLine("  AND prd_flag_aktivasi IN ('X') ")
        //     sb.AppendLine("  AND prd_flag_aktivasi = AKT_KODEFLAG ")
        // End If

        //! INSERT Into TEMP_CETAKPB_TOLAKAN_IDM
        // sb.AppendLine("INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   KOMI, ")
        // sb.AppendLine("   TGL, ")
        // sb.AppendLine("   NODOK, ")
        // sb.AppendLine("   TGLDOK, ")
        // sb.AppendLine("   PLU, ")
        // sb.AppendLine("   PLUIGR, ")
        // sb.AppendLine("   KETA, ")
        // sb.AppendLine("   TAG, ")
        // sb.AppendLine("   DESCR, ")
        // sb.AppendLine("   QTYO, ")
        // sb.AppendLine("   GROSS, ")
        // sb.AppendLine("   KCAB, ")
        // sb.AppendLine("   KODEIGR, ")
        // sb.AppendLine("   REQ_ID ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select KOMI, ")
        // sb.AppendLine("       TGL, ")
        // sb.AppendLine("       NODOK, ")
        // sb.AppendLine("       TGLDOK, ")
        // sb.AppendLine("       PLU, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       KETA, ")
        // sb.AppendLine("       TAG, ")
        // sb.AppendLine("       DESCR, ")
        // sb.AppendLine("       QTYO, ")
        // sb.AppendLine("       ST_AVGCOST * QTYO as GROSS, ")
        // sb.AppendLine("       KCAB, ")
        // sb.AppendLine("       KODEIGR, ")
        // sb.AppendLine("       REQ_ID ")
        // sb.AppendLine("  FROM TEMP_CETAKPB_TOLAKAN_IDM2 IDM2,tbMaster_Stock ")
        // sb.AppendLine(" Where ST_PRDCD Like SUBSTR(PLUIGR,1,6)||'%' ")
        // sb.AppendLine("   And ST_Lokasi = '01' ")
        // sb.AppendLine("   And COALESCE(ST_RecordID,'0') <> '1' ")
        // sb.AppendLine("   And REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   AND KCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   AND NODOK = '" & noPB & "' ")
        // sb.AppendLine("   AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   AND NOT EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("   SELECT PLUIGR  ")
        // sb.AppendLine("	    FROM TEMP_CETAKPB_TOLAKAN_IDM IDM ")
        // sb.AppendLine("	   WHERE KOMI = '" & KodeMember & "' ")
        // sb.AppendLine("	     AND REQ_ID = '" & IP & "'		  ")
        // sb.AppendLine("		 AND NODOK = '" & noPB & "' ")
        // sb.AppendLine("		 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("		 AND IDM.PLU = IDM2.PLU ")
        // sb.AppendLine("   )    ")

        //! '------------------------------------'
        //! '+ 03-07-2013 ISI TEMP_PB_IDMREADY2 +'
        //! '------------------------------------'

        //! DELETE FROM TEMP_PBIDM_READY2
        // sb.AppendLine("DELETE FROM TEMP_PBIDM_READY2 ")
        // sb.AppendLine(" WHERE REQ_ID = '" & IP & "' ")

        //! INSERT INTO TEMP_PBIDM_READY2
        // sb.AppendLine("INSERT INTO TEMP_PBIDM_READY2 ")
        // sb.AppendLine("( ")
        // sb.AppendLine("      fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      prc_pluigr ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select NULL as FDRCID, ")
        // sb.AppendLine("	    CPP_NOPB as FDNOUO, ")
        // sb.AppendLine("	    CPP_PLUIDM as FDKODE, ")
        // sb.AppendLine("	    CPP_QTY as FDQTYB, ")
        // sb.AppendLine("	    CPP_KodeToko as  FDKCAB, ")
        // sb.AppendLine("	    CPP_TglPB as FDTGPB, ")
        // sb.AppendLine("	    NULL as  FDKSUP, ")
        // sb.AppendLine("	    CPP_IP as REQ_ID, ")
        // sb.AppendLine("	    CPP_FILENAME as  NAMA_FILE, ")
        // If chkIDMBacaProdcrm.Checked Then
        //     sb.AppendLine("	      KAT_PLUIGR ")
        //     sb.AppendLine("   From csv_pb_pot A, KONVERSI_ATK ")
        //     sb.AppendLine("  Where CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("    AND CPP_KodeToko = '" & KodeToko & "' ")
        //     sb.AppendLine("    AND CPP_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("    AND CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("    AND NOT EXISTS ")
        //     sb.AppendLine("           (   ")
        //     sb.AppendLine("              SELECT PLUIGR    ")
        //     sb.AppendLine("                FROM TEMP_CETAKPB_TOLAKAN_IDM   ")
        //     sb.AppendLine("               WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("                 AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("                 AND NODOK = '" & noPB & "'   ")
        //     sb.AppendLine("                 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("                 AND PLU = CPP_PLUIDM ")
        //     sb.AppendLine("           )  ")
        //     sb.AppendLine("    AND KAT_pluidm = CPP_PLUIDM ")
        //     sb.AppendLine("    AND CPP_PLUIGR = KAT_PLUIGR ")
        //     sb.AppendLine("    AND CPP_FLAG  IS NULL ")
        // Else
        //     sb.AppendLine("	       IDM_PLUIGR ")
        //     sb.AppendLine("   From csv_pb_pot A, TBTEMP_PLUIDM ")
        //     sb.AppendLine("  Where CPP_IP = '" & IP & "' ")
        //     sb.AppendLine("    AND CPP_KodeTOKO = '" & KodeToko & "' ")
        //     sb.AppendLine("    AND CPP_NOPB = '" & noPB & "' ")
        //     sb.AppendLine("    AND CPP_TGLPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("    AND NOT EXISTS ")
        //     sb.AppendLine("           (   ")
        //     sb.AppendLine("              SELECT PLUIGR    ")
        //     sb.AppendLine("                FROM TEMP_CETAKPB_TOLAKAN_IDM   ")
        //     sb.AppendLine("               WHERE KOMI = '" & KodeMember & "'  ")
        //     sb.AppendLine("                 AND REQ_ID = '" & IP & "' ")
        //     sb.AppendLine("                 AND NODOK = '" & noPB & "'   ")
        //     sb.AppendLine("                 AND TGLDOK = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        //     sb.AppendLine("                 AND PLU = CPP_PLUIDM  ")
        //     sb.AppendLine("           )  ")
        //     sb.AppendLine("    AND IDM_pluidm = CPP_PLUIDM ")
        //     sb.AppendLine("    AND CPP_FLAG IS NULL ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("    AND IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        // End If

        //GET -> jum
        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where upper(table_name) = 'TEMP_PBIDM_READY' ")

        // if(jum = 0){
            //! CREATE TABLE TEMP_PBIDM_READY
            // sb.AppendLine("CREATE TABLE TEMP_PBIDM_READY ")
            // sb.AppendLine("AS     ")
            // sb.AppendLine("Select E.*,ST_AvgCost as AVGCOST  ")
            // sb.AppendLine("  From  ")
            // sb.AppendLine("(     ")
            // sb.AppendLine("    Select D.*,   ")
            // sb.AppendLine("           CASE WHEN FracKarton = 1 THEN 0 ELSE FDQTYB / FracKarton END as QTYB,  ")
            // sb.AppendLine("           CASE WHEN FracKarton = 1 THEN FDQTYB ELSE MOD(FDQTYB,FracKarton) END as QTYK,  ")
            // sb.AppendLine("           CASE WHEN     ")
            // sb.AppendLine("             CASE WHEN FracKarton = 1 THEN FDQTYB ELSE FDQTYB / FracKecil END < PRD_MinJual  ")
            // sb.AppendLine("           THEN 'T'  ")
            // sb.AppendLine("           ELSE '' END AS TolakMinJ      ")
            // sb.AppendLine("      From  ")
            // sb.AppendLine("    ( ")
            // sb.AppendLine("    Select C.*,PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual ")
            // sb.AppendLine("      From ")
            // sb.AppendLine("    (         ")
            // sb.AppendLine("        Select B.*, CASE WHEN min(prd_prdcd) IS NULL THEN PluKarton ELSE min(prd_prdcd) END as PLUKecil--, PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual  ")
            // sb.AppendLine("          From  ")
            // sb.AppendLine("        (   ")

            // sb.AppendLine("        Select A.FDRCID, A.FDNOUO, A.FDKODE, MAX(A.FDQTYB) as FDQTYB, A.FDKCAB, A.FDTGPB, A.FDKSUP, A.REQ_ID, A.NAMA_FILE , prd_deskripsipanjang as DESK, prd_flagbkp1 as BKP, prd_prdcd as PluKarton,prd_unit as UnitKarton,prd_frac as FracKarton  ")
            // sb.AppendLine("          From temp_pbidm_ready2 A, tbmaster_prodmast  ")
            // sb.AppendLine("         Where REQ_ID = '" & IP & "'   ")
            // sb.AppendLine("           AND FDKCAB = '" & KodeToko & "' ")
            // sb.AppendLine("           AND FDNOUO = '" & noPB & "'   ")
            // sb.AppendLine("           AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("           AND prd_prdcd = prc_pluigr  ")
            // sb.AppendLine("         GROUP By A.FDRCID,  ")
            // sb.AppendLine("		          A.FDNOUO,  ")
            // sb.AppendLine("				  A.FDKODE,  ")
            // sb.AppendLine("				  A.FDTGPB,  ")
            // sb.AppendLine("				  A.FDKCAB,  ")
            // sb.AppendLine("				  A.FDKSUP,  ")
            // sb.AppendLine("				  A.REQ_ID, ")
            // sb.AppendLine("				  A.NAMA_FILE,  ")
            // sb.AppendLine("				  prd_deskripsipanjang,  ")
            // sb.AppendLine("				  prd_flagbkp1,  ")
            // sb.AppendLine("				  prd_prdcd, ")
            // sb.AppendLine("				  prd_unit, ")
            // sb.AppendLine("				  prd_frac ")
            // sb.AppendLine("        ) B, tbMaster_Prodmast  ")
            // sb.AppendLine("         Where PRD_PRDCD <> SUBSTR(PLUKarton,1,6)||'0'  ")
            // sb.AppendLine("           And PRD_PRDCD Like SUBSTR(PLUKarton,1,6)||'%'  ")
            // sb.AppendLine("           AND COALESCE(prd_KodeTag,'A') NOT IN ('N','X','Q') ")
            // sb.AppendLine("         Group By fdrcid,  ")
            // sb.AppendLine("               fdnouo,  ")
            // sb.AppendLine("               fdkode,  ")
            // sb.AppendLine("               fdqtyb,  ")
            // sb.AppendLine("               fdkcab,  ")
            // sb.AppendLine("               fdtgpb,  ")
            // sb.AppendLine("               fdksup,  ")
            // sb.AppendLine("               req_id,  ")
            // sb.AppendLine("               nama_file,  ")
            // sb.AppendLine("               Desk, ")
            // sb.AppendLine("               PluKarton,  ")
            // sb.AppendLine("               UnitKarton,  ")
            // sb.AppendLine("               FracKarton,  ")
            // sb.AppendLine("               BKP  ")
            // sb.AppendLine("    ) C, tbMaster_prodmast     ")
            // sb.AppendLine("    Where PRD_PRDCD = PluKecil     ")
            // sb.AppendLine("    )D         ")
            // sb.AppendLine(") E, tbMaster_Stock  ")
            // sb.AppendLine("Where ST_PRDCD = PLUKARTON  ")
            // sb.AppendLine("  And ST_Lokasi = '01'  ")
            // sb.AppendLine("  And COALESCE(ST_RecordID,'0') <> '1' ")

        // }else{
            //! DELETE FROM TEMP_PBIDM_READY
            // sb.AppendLine("DELETE FROM TEMP_PBIDM_READY ")
            // sb.AppendLine(" WHERE REQ_ID = '" & IP & "' ")

            //! INSERT INTO TEMP_PBIDM_READY
            // sb.AppendLine("INSERT INTO TEMP_PBIDM_READY  ")
            // sb.AppendLine(" ( ")
            // sb.AppendLine("       fdrcid, ")
            // sb.AppendLine("       fdnouo, ")
            // sb.AppendLine("       fdkode, ")
            // sb.AppendLine("       fdqtyb, ")
            // sb.AppendLine("       fdkcab, ")
            // sb.AppendLine("       fdtgpb, ")
            // sb.AppendLine("       fdksup, ")
            // sb.AppendLine("       req_id, ")
            // sb.AppendLine("       nama_file, ")
            // sb.AppendLine("       desk, ")
            // sb.AppendLine("       bkp, ")
            // sb.AppendLine("       plukarton, ")
            // sb.AppendLine("       unitkarton, ")
            // sb.AppendLine("       frackarton, ")
            // sb.AppendLine("       plukecil, ")
            // sb.AppendLine("       unitkecil, ")
            // sb.AppendLine("       frackecil, ")
            // sb.AppendLine("       prd_minjual, ")
            // sb.AppendLine("       qtyb, ")
            // sb.AppendLine("       qtyk, ")
            // sb.AppendLine("       tolakminj, ")
            // sb.AppendLine("       avgcost ")
            // sb.AppendLine(" ) ")
            // sb.AppendLine("Select E.*,ST_AvgCost as AVGCOST  ")
            // sb.AppendLine("  From  ")
            // sb.AppendLine("(     ")
            // sb.AppendLine("    Select D.*,   ")
            // sb.AppendLine("           CASE WHEN FracKarton = 1 THEN 0 ELSE FDQTYB / FracKarton END as QTYB,  ")
            // sb.AppendLine("           CASE WHEN FracKarton = 1 THEN FDQTYB ELSE MOD(FDQTYB,FracKarton) END as QTYK,  ")
            // sb.AppendLine("           CASE WHEN     ")
            // sb.AppendLine("             CASE WHEN FracKarton = 1 THEN FDQTYB ELSE FDQTYB / FracKecil END < PRD_MinJual  ")
            // sb.AppendLine("           THEN 'T'  ")
            // sb.AppendLine("           ELSE '' END AS TolakMinJ ")
            // sb.AppendLine("      From  ")
            // sb.AppendLine("    ( ")
            // sb.AppendLine("    Select C.*,PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual ")
            // sb.AppendLine("      From ")
            // sb.AppendLine("    (         ")
            // sb.AppendLine("        Select B.*, CASE WHEN min(prd_prdcd) IS NULL THEN PluKarton ELSE min(prd_prdcd) END as PLUKecil--, PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual  ")
            // sb.AppendLine("          From  ")
            // sb.AppendLine("        (   ")
            // sb.AppendLine("        Select A.FDRCID, A.FDNOUO, A.FDKODE, MAX(A.FDQTYB) as FDQTYB, A.FDKCAB, A.FDTGPB, A.FDKSUP, A.REQ_ID, A.NAMA_FILE , prd_deskripsipanjang as DESK, prd_flagbkp1 as BKP, prd_prdcd as PluKarton,prd_unit as UnitKarton,prd_frac as FracKarton  ")
            // sb.AppendLine("          From temp_pbidm_ready2 A, tbmaster_prodmast  ")
            // sb.AppendLine("         Where REQ_ID = '" & IP & "'   ")
            // sb.AppendLine("           AND FDKCAB = '" & KodeToko & "' ")
            // sb.AppendLine("           AND FDNOUO = '" & noPB & "'   ")
            // sb.AppendLine("           AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("           AND prd_prdcd = prc_pluigr  ")
            // sb.AppendLine("         GROUP By A.FDRCID,  ")
            // sb.AppendLine("		          A.FDNOUO,  ")
            // sb.AppendLine("				  A.FDKODE,  ")
            // sb.AppendLine("				  A.FDTGPB,  ")
            // sb.AppendLine("				  A.FDKCAB,  ")
            // sb.AppendLine("				  A.FDKSUP,  ")
            // sb.AppendLine("				  A.REQ_ID, ")
            // sb.AppendLine("				  A.NAMA_FILE,  ")
            // sb.AppendLine("				  prd_deskripsipanjang,  ")
            // sb.AppendLine("				  prd_flagbkp1,  ")
            // sb.AppendLine("				  prd_prdcd, ")
            // sb.AppendLine("				  prd_unit, ")
            // sb.AppendLine("				  prd_frac ")
            // sb.AppendLine("        ) B, tbMaster_Prodmast  ")
            // sb.AppendLine("         Where PRD_PRDCD <> SUBSTR(PLUKarton,1,6)||'0'  ")
            // sb.AppendLine("           And PRD_PRDCD Like SUBSTR(PLUKarton,1,6)||'%'  ")
            // sb.AppendLine("           AND COALESCE(prd_KodeTag,'A') NOT IN ('N','X','Q') ")
            // sb.AppendLine("         Group By fdrcid,  ")
            // sb.AppendLine("               fdnouo,  ")
            // sb.AppendLine("               fdkode,  ")
            // sb.AppendLine("               fdqtyb,  ")
            // sb.AppendLine("               fdkcab,  ")
            // sb.AppendLine("               fdtgpb,  ")
            // sb.AppendLine("               fdksup,  ")
            // sb.AppendLine("               req_id,  ")
            // sb.AppendLine("               nama_file,  ")
            // sb.AppendLine("               Desk, ")
            // sb.AppendLine("               PluKarton,  ")
            // sb.AppendLine("               UnitKarton,  ")
            // sb.AppendLine("               FracKarton,  ")
            // sb.AppendLine("               BKP  ")
            // sb.AppendLine("    ) C, tbMaster_prodmast     ")
            // sb.AppendLine("    Where PRD_PRDCD = PluKecil     ")
            // sb.AppendLine("    )D         ")
            // sb.AppendLine(") E, tbMaster_Stock  ")
            // sb.AppendLine("Where ST_PRDCD = PLUKARTON  ")
            // sb.AppendLine("  And ST_Lokasi = '01'  ")
            // sb.AppendLine("  And COALESCE(ST_RecordID,'0') <> '1' ")
        // }

        //! UPDATE TEMP_PBIDM_READY SUPAYA ITEM HANDHELD IN PIECES SEMUA
        // sb.AppendLine("UPDATE TEMP_PBIDM_READY ")
        // sb.AppendLine("   SET QTYB = 0, ")
        // sb.AppendLine("       QTYK = QTYK + QTYB * FRACKARTON ")
        // sb.AppendLine(" WHERE REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("   AND FDNOUO = '" & noPB & "' ")
        // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   AND QTYB > 0 ")

        //! GET -> AdaKarton
        // sb.AppendLine("Select COALESCE(Count(1),0)  ")
        // sb.AppendLine("  From temp_pbidm_ready ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'  ")
        // sb.AppendLine("   AND FDNOUO = '" & noPB & "'  ")
        // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   AND QTYB > 0 ")

        // If jum > 0 Then
        //     AdaKartonan = True
        // End If

        //! GET -> AdaKecil
        // sb.AppendLine("Select COALESCE(Count(1),0)  ")
        // sb.AppendLine("  From temp_pbidm_ready ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'  ")
        // sb.AppendLine("   AND FDNOUO = '" & noPB & "'  ")
        // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   AND QTYK > 0 ")

        // If jum > 0 Then
        //     AdaKecil = True
        // End If

        // sb.AppendLine("Select COALESCE(Count(1),0) ")
        // sb.AppendLine("  From tbtr_counterpbomi ")
        // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
        // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

        // if(jum = 0){
            //! Insert Into tbtr_CounterPbOMI
            // sb.AppendLine("Insert Into tbtr_CounterPbOMI ")
            // sb.AppendLine("( ")
            // sb.AppendLine(" cou_kodeigr,  ")
            // sb.AppendLine(" cou_kodeomi,  ")
            // sb.AppendLine(" cou_tgl,  ")
            // sb.AppendLine(" cou_nodokumen, ")
            // sb.AppendLine(" cou_create_by, ")
            // sb.AppendLine(" cou_create_dt     ")
            // sb.AppendLine(") ")
            // sb.AppendLine("VALUES ")
            // sb.AppendLine("( ")
            // sb.AppendLine("  '" & KDIGR & "', ")
            // sb.AppendLine("  '" & KodeToko & "', ")
            // sb.AppendLine("  CURRENT_DATE, ")
            // sb.AppendLine("  '', ")
            // sb.AppendLine("  '" & UserID & "', ")
            // sb.AppendLine("  current_timestamp   ")
            // sb.AppendLine(") ")

            // If AdaKecil Then CounterKecil = 1
            // If AdaKartonan Then If AdaKecil Then CounterKarton = 2 Else CounterKarton = 1
        // }

        // sb.AppendLine("Select COALESCE(length(rtrim(cou_nodokumen)),0) ")
        // sb.AppendLine("  From tbtr_counterpbomi ")
        // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
        // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

        // if(jum >= 8){

            //! SET COU_NoDokumen = ''
            // sb.AppendLine("UPDATE TbTr_CounterPBOMI ")
            // If AdaKartonan And AdaKecil Then
            //     sb.AppendLine("   SET COU_NoDokumen = 'YY', ")
            // Else
            //     sb.AppendLine("   SET COU_NoDokumen = 'Y', ")
            // End If
            // sb.AppendLine("       COU_Modify_By = '" & UserID & "', ")
            // sb.AppendLine("       COU_Modify_Dt = current_timestamp	    ")
            // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
            // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

            // If AdaKecil Then CounterKecil = 1
            // If AdaKartonan Then If AdaKecil Then CounterKarton = 2 Else CounterKarton = 1

        // }else{
            //! SET COU_NoDokumen = RTRIM(COU_NoDokumen) + Y/YY
            // sb.AppendLine("UPDATE TbTr_CounterPBOMI ")
            // If AdaKartonan And AdaKecil Then
            //     sb.AppendLine("   SET COU_NoDokumen = RTRIM(COU_NoDokumen)||'YY', ")
            // Else
            //     sb.AppendLine("   SET COU_NoDokumen = RTRIM(COU_NoDokumen)||'Y', ")
            // End If
            // sb.AppendLine("       COU_Modify_By = '" & UserID & "', ")
            // sb.AppendLine("       COU_Modify_Dt = current_timestamp	    ")
            // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
            // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

            // If AdaKecil Then CounterKecil = jum + 1
            // If AdaKartonan Then CounterKarton = CounterKecil + 1
        // }

        //! INSERT INTO TBTR_TOLAKANPBOMI
        // sb.AppendLine("INSERT INTO TBTR_TolakanPBOMI ")
        // sb.AppendLine("( ")
        // sb.AppendLine(" TLKO_KodeIGR, ")
        // sb.AppendLine(" TLKO_KodeOMI, ")
        // sb.AppendLine(" TLKO_TglPB, ")
        // sb.AppendLine(" TLKO_NoPB, ")
        // sb.AppendLine(" TLKO_PluIGR, ")
        // sb.AppendLine(" TLKO_PluOMI, ")
        // sb.AppendLine(" TLKO_PTAG, ")
        // sb.AppendLine(" TLKO_DESC, ")
        // sb.AppendLine(" TLKO_KetTolakan, ")
        // sb.AppendLine(" TLKO_QtyOrder, ")
        // sb.AppendLine(" TLKO_LastCost, ")
        // sb.AppendLine(" TLKO_Nilai, ")
        // sb.AppendLine(" TLKO_Create_By, ")
        // sb.AppendLine(" TLKO_Create_Dt ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select KODEIGR, ")
        // sb.AppendLine("       KCAB, ")
        // sb.AppendLine("       TGLDOK, ")
        // sb.AppendLine("       NODOK, ")
        // sb.AppendLine("       PLUIGR, ")
        // sb.AppendLine("       PLU, ")
        // sb.AppendLine("       TAG, ")
        // sb.AppendLine("       DESCR, ")
        // sb.AppendLine("       KETA, ")
        // sb.AppendLine("       QTYO, ")
        // sb.AppendLine("       ST_AVGCOST, ")
        // sb.AppendLine("       GROSS, ")
        // sb.AppendLine("       '" & UserID & "', ")
        // sb.AppendLine("       current_timestamp ")
        // sb.AppendLine("  From TEMP_CETAKPB_TOLAKAN_IDM ")
        // sb.AppendLine("  join tbMaster_Stock ")
        // sb.AppendLine("  ON ST_PRDCD = SUBSTR(PLUIGR,1,6)||'0' ")
        // sb.AppendLine("  And ST_Lokasi = '01' ")
        // sb.AppendLine("  Where REQ_ID = '" & IP & "' ")

        //! MERGE INTO TBTR_TOLAKANPBOMI-IDM_Tag
        // if(chkIDMBacaProdcrm.Checked ){
        //     if(kodeDCIDM <> "" ){
        //         sb.AppendLine("update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.IDM_KodeTag ")
        //         sb.AppendLine("from (       ")
        //         sb.AppendLine("      SELECT IDM_PLUIDM, ")
        //         sb.AppendLine("      IDM_KodeTag ")
        //         sb.AppendLine("      FROM tbMaster_Pluidm")
        //         sb.AppendLine("      Where IDM_KodeIDM = '" & kodeDCIDM & "' ")
        //         sb.AppendLine("      And Exists ( ")
        //         sb.AppendLine("          Select tlko_pluomi ")
        //         sb.AppendLine("          From tbtr_TolakanPbOMI ")
        //         sb.AppendLine("          Where tlko_PluOMI = IDM_PLUIDM ")
        //         sb.AppendLine("   	     And tlko_KodeOMI = '" & KodeToko & "' ")
        //         sb.AppendLine("    		 And tlko_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("   		 And tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //         sb.AppendLine("        ) ")
        //         sb.AppendLine(") b where a.TLKO_PLUOMI = b.IDM_PLUIDM ")
        //         sb.AppendLine("    and a.tlko_KodeOMI = '" & KodeToko & "' ")
        //         sb.AppendLine("    And a.tlko_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("    And a.tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //     }else{
        //         sb.AppendLine("update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.PRC_KodeTag    ")
        //         sb.AppendLine("from (")
        //         sb.AppendLine("    SELECT PRC_PLUIDM, ")
        //         sb.AppendLine("           PRC_KodeTag")
        //         sb.AppendLine("     FROM tbMaster_Prodcrm")
        //         sb.AppendLine("     Where Exists")
        //         sb.AppendLine("     ( ")
        //         sb.AppendLine("     Select tlko_pluomi")
        //         sb.AppendLine("     From tbtr_TolakanPbOMI ")
        //         sb.AppendLine("           Where tlko_PluOMI = PRC_PLUIDM ")
        //         sb.AppendLine("           And tlko_KodeOMI = '" & KodeToko & "' ")
        //         sb.AppendLine("           And tlko_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("   	      And tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //         sb.AppendLine("        ) ")
        //         sb.AppendLine(" ) b ")
        //         sb.AppendLine("where a.TLKO_PLUOMI = b.PRC_PLUIDM ")
        //         sb.AppendLine("      and a.tlko_KodeOMI = '" & KodeToko & "' ")
        //         sb.AppendLine("      And a.tlko_NoPB = '" & noPB & "' ")
        //         sb.AppendLine("      And a.tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     }
        // }else{
        //     sb.AppendLine("update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.IDM_Tag   ")
        //     sb.AppendLine("from (      ")
        //     sb.AppendLine("      SELECT IDM_PLUIDM, ")
        //     sb.AppendLine("             IDM_Tag ")
        //     sb.AppendLine("      FROM TBTEMP_PLUIDM ")
        //     sb.AppendLine("      Where Exists ")
        //     sb.AppendLine("       (  ")
        //     sb.AppendLine("           Select tlko_pluomi ")
        //     sb.AppendLine("           From tbtr_TolakanPbOMI ")
        //     sb.AppendLine("           Where tlko_PluOMI = IDM_PLUIDM ")
        //     sb.AppendLine("           And tlko_KodeOMI = '" & KodeToko & "' ")
        //     sb.AppendLine(" 		  And tlko_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("           And tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        //     sb.AppendLine("        ) ")
        //     If kodeDCIDM <> "" Then
        //         sb.AppendLine("     And IDM_KDIDM = '" & kodeDCIDM & "' ")
        //     End If
        //     sb.AppendLine(" ) b where a.TLKO_PLUOMI = b.IDM_PLUIDM ")
        //     sb.AppendLine("    and a.tlko_KodeOMI = '" & KodeToko & "' ")
        //     sb.AppendLine("    And a.tlko_NoPB = '" & noPB & "' ")
        //     sb.AppendLine("    And a.tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // }

        //! MERGE INTO TBTR_TOLAKANPBOMI-PRD_KodeTag
        // sb.AppendLine("UPDATE TBTR_TOLAKANPBOMI A SET TLKO_TAG_IGR = b.PRD_KodeTag  ")
        // sb.AppendLine("from (")
        // sb.AppendLine("         SELECT PRD_PRDCD, ")
        // sb.AppendLine("         PRD_KodeTAG ")
        // sb.AppendLine("         FROM TbMaster_Prodmast ")
        // sb.AppendLine("         Where Exists ")
        // sb.AppendLine("         ( ")
        // sb.AppendLine("           Select tlko_pluomi")
        // sb.AppendLine("           From tbtr_TolakanPbOMI ")
        // sb.AppendLine("           Where tlko_PluIGR = PRD_PRDCD ")
        // sb.AppendLine("           And tlko_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("           And tlko_NoPB = '" & noPB & "' ")
        // sb.AppendLine("           And tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("         ) ")
        // sb.AppendLine(") b where a.TLKO_PLUOMI = b.PRD_PRDCD")
        // sb.AppendLine("    And a.tlko_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("    And a.tlko_NoPB = '" & noPB & "' ")
        // sb.AppendLine("    And a.tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! MERGE INTO TBTR_TOLAKANPBOMI-ST_AvgCost
        // sb.AppendLine(" UPDATE TBTR_TOLAKANPBOMI A SET TLKO_NILAI = b.RUPIAH, ")
        // sb.AppendLine("                               TLKO_MARGIN = b.MARGIN, ")
        // sb.AppendLine("                               TLKO_LPP = b.LPP ")
        // sb.AppendLine("     from ( ")
        // sb.AppendLine("     SELECT ST_PRDCD, ")
        // sb.AppendLine("            round(st_avgcost * (1 + COALESCE(MPI_MARGIN,3)/100)) as RUPIAH, ")
        // sb.AppendLine("            round(st_avgcost * (COALESCE(MPI_MARGIN,3)/100)) as MARGIN, ")
        // sb.AppendLine("            COALESCE(ST_SALDOAKHIR,0) as LPP ")
        // sb.AppendLine("      	 FROM TbMaster_Stock, ")
        // sb.AppendLine("             TbMaster_MarginPluIDM ")
        // sb.AppendLine("      	Where Exists ")
        // sb.AppendLine("         ( ")
        // sb.AppendLine("         Select tlko_pluomi ")
        // sb.AppendLine("      	    From tbtr_TolakanPbOMI ")
        // sb.AppendLine("      	    Where tlko_PluIGR = ST_PRDCD ")
        // sb.AppendLine("             And tlko_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("             And tlko_NoPB = '" & noPB & "' ")
        // sb.AppendLine("             And tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("         ) ")
        // sb.AppendLine("         And ST_Lokasi = '01' ")
        // sb.AppendLine("         And ST_PRDCD = MPI_PluIGR ")
        // sb.AppendLine("   ) b ")
        // sb.AppendLine("   where a.TLKO_PLUIGR = b.ST_PRDCD")
        // sb.AppendLine("   and a.tlko_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And a.tlko_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And a.tlko_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! '-----------------------------------'
        //! '+ SIAPKAN DATA JALUR TIDAK KETEMU +'
        //! '-----------------------------------'

        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where upper(table_name) = 'TEMP_NOJALUR_IDM' ")

        // if(jum = 0){
            //! Create Table TEMP_NOJALUR_IDM 1
            // sb.AppendLine("CREATE TABLE TEMP_NOJALUR_IDM ")
            // sb.AppendLine("AS ")
            // sb.AppendLine("SELECT fdrcid, ")
            // sb.AppendLine("      fdnouo, ")
            // sb.AppendLine("      fdkode, ")
            // sb.AppendLine("      fdqtyb, ")
            // sb.AppendLine("      fdkcab, ")
            // sb.AppendLine("      fdtgpb, ")
            // sb.AppendLine("      fdksup, ")
            // sb.AppendLine("      req_id, ")
            // sb.AppendLine("      nama_file, ")
            // sb.AppendLine("      desk, ")
            // sb.AppendLine("      bkp, ")
            // sb.AppendLine("      plukarton, ")
            // sb.AppendLine("      unitkarton, ")
            // sb.AppendLine("      frackarton, ")
            // sb.AppendLine("      plukecil, ")
            // sb.AppendLine("      unitkecil, ")
            // sb.AppendLine("      frackecil, ")
            // sb.AppendLine("      prd_minjual, ")
            // sb.AppendLine("      qtyb, ")
            // sb.AppendLine("      qtyk, ")
            // sb.AppendLine("      tolakminj, ")
            // sb.AppendLine("      avgcost ")
            // sb.AppendLine("  From temp_pbidm_ready pbi ")
            // sb.AppendLine(" Where pbi.REQ_ID = '" & IP & "' ")
            // sb.AppendLine("   AND pbi.FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("   And pbi.fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   And pbi.fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   And Not EXISTS ")
            // sb.AppendLine("   ( ")
            // sb.AppendLine("    Select lks_koderak ")
            // sb.AppendLine("      From tbMaster_Lokasi ")
            // sb.AppendLine("     Where LKS_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("       And LKS_PRDCD = pbi.PLUKarton ")
            // sb.AppendLine("       And LKS_TIPERAK NOT LIKE  'S%' ")
            // sb.AppendLine("   ) ")
            // sb.AppendLine("   And COALESCE(pbi.TolakMinJ,'X') <> 'T' ")
            // sb.AppendLine("   And Not EXISTS ")
            // sb.AppendLine("   ( ")
            // sb.AppendLine("    Select K.PLUKARTON ")
            // sb.AppendLine("      From TEMP_JALURKERTAS_IDM K ")
            // sb.AppendLine("     Where K.REQ_ID = '" & IP & "' ")
            // sb.AppendLine("       AND K.FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("       And K.fdnouo = '" & noPB & "' ")
            // sb.AppendLine("       And K.fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("       And K.PLUKARTON = pbi.PLUKARTON ")
            // sb.AppendLine("   ) ")

        // }else{
            //! Delete From TEMP_NOJALUR_IDM
            // sb.AppendLine("Delete From TEMP_NOJALUR_IDM ")
            // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
            // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
            // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

            //! INSERT INTO TEMP_NOJALUR_IDM 1
            // sb.AppendLine("INSERT INTO TEMP_NOJALUR_IDM ")
            // sb.AppendLine("( ")
            // sb.AppendLine("      fdrcid, ")
            // sb.AppendLine("      fdnouo, ")
            // sb.AppendLine("      fdkode, ")
            // sb.AppendLine("      fdqtyb, ")
            // sb.AppendLine("      fdkcab, ")
            // sb.AppendLine("      fdtgpb, ")
            // sb.AppendLine("      fdksup, ")
            // sb.AppendLine("      req_id, ")
            // sb.AppendLine("      nama_file, ")
            // sb.AppendLine("      desk, ")
            // sb.AppendLine("      bkp, ")
            // sb.AppendLine("      plukarton, ")
            // sb.AppendLine("      unitkarton, ")
            // sb.AppendLine("      frackarton, ")
            // sb.AppendLine("      plukecil, ")
            // sb.AppendLine("      unitkecil, ")
            // sb.AppendLine("      frackecil, ")
            // sb.AppendLine("      prd_minjual, ")
            // sb.AppendLine("      qtyb, ")
            // sb.AppendLine("      qtyk, ")
            // sb.AppendLine("      tolakminj, ")
            // sb.AppendLine("      avgcost ")
            // sb.AppendLine(") ")
            // sb.AppendLine("SELECT fdrcid, ")
            // sb.AppendLine("      fdnouo, ")
            // sb.AppendLine("      fdkode, ")
            // sb.AppendLine("      fdqtyb, ")
            // sb.AppendLine("      fdkcab, ")
            // sb.AppendLine("      fdtgpb, ")
            // sb.AppendLine("      fdksup, ")
            // sb.AppendLine("      req_id, ")
            // sb.AppendLine("      nama_file, ")
            // sb.AppendLine("      desk, ")
            // sb.AppendLine("      bkp, ")
            // sb.AppendLine("      plukarton, ")
            // sb.AppendLine("      unitkarton, ")
            // sb.AppendLine("      frackarton, ")
            // sb.AppendLine("      plukecil, ")
            // sb.AppendLine("      unitkecil, ")
            // sb.AppendLine("      frackecil, ")
            // sb.AppendLine("      prd_minjual, ")
            // sb.AppendLine("      qtyb, ")
            // sb.AppendLine("      qtyk, ")
            // sb.AppendLine("      tolakminj, ")
            // sb.AppendLine("      avgcost ")
            // sb.AppendLine("  From temp_pbidm_ready pbi ")
            // sb.AppendLine(" Where pbi.REQ_ID = '" & IP & "' ")
            // sb.AppendLine("   AND pbi.FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("   And pbi.fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   And pbi.fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   And Not EXISTS ")
            // sb.AppendLine("   ( ")
            // sb.AppendLine("    Select lks_koderak ")
            // sb.AppendLine("      From tbMaster_Lokasi ")
            // sb.AppendLine("     Where LKS_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("       And LKS_PRDCD = pbi.PLUKarton ")
            // sb.AppendLine("       And LKS_TIPERAK NOT LIKE  'S%' ")
            // sb.AppendLine("   ) ")
            // sb.AppendLine("   And COALESCE(pbi.TolakMinJ,'X') <> 'T' ")
            // sb.AppendLine("   And Not EXISTS ")
            // sb.AppendLine("   ( ")
            // sb.AppendLine("    Select K.PLUKARTON ")
            // sb.AppendLine("      From TEMP_JALURKERTAS_IDM K ")
            // sb.AppendLine("     Where K.REQ_ID = '" & IP & "' ")
            // sb.AppendLine("       AND K.FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("       And K.fdnouo = '" & noPB & "' ")
            // sb.AppendLine("       And K.fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("       And K.PLUKARTON = pbi.PLUKARTON ")
            // sb.AppendLine("   ) ")
        // }

        //! INSERT INTO TEMP_NOJALUR_IDM 2
        // sb.AppendLine("INSERT INTO TEMP_NOJALUR_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("      fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine(") ")
        // sb.AppendLine("SELECT fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine("  From temp_pbidm_ready ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'  ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("    Select lks_koderak ")
        // sb.AppendLine("      From tbMaster_Lokasi ")
        // sb.AppendLine("     Where LKS_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("       And LKS_PRDCD = PLUKarton ")
        // sb.AppendLine("       And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   ) ")
        // sb.AppendLine("   And NOT EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("     Select grr_koderak ")
        // sb.AppendLine("       From tbMaster_Lokasi,tbMaster_GroupRak ")
        // sb.AppendLine("      Where LKS_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("        And LKS_PRDCD = PLUKarton ")
        // sb.AppendLine("        And GRR_Koderak = LKS_KodeRak ")
        // sb.AppendLine("        And GRR_Subrak  = LKS_KodeSubrak ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   ) ")
        // sb.AppendLine("   And COALESCE(TolakMinJ,'X') <> 'T' ")
        // sb.AppendLine("   And QTYK > 0 ")

        //! INSERT INTO TEMP_NOJALUR_IDM 3
        // sb.AppendLine("INSERT INTO TEMP_NOJALUR_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("      fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine(") ")
        // sb.AppendLine("SELECT fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine("  From temp_pbidm_ready  ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'   ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "'  ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')  ")
        // sb.AppendLine("   And EXISTS  ")
        // sb.AppendLine("   (  ")
        // sb.AppendLine("     Select grr_koderak  ")
        // sb.AppendLine("       From tbMaster_Lokasi,tbMaster_GroupRak  ")
        // sb.AppendLine("      Where LKS_KodeIGR = '" & KDIGR & "'  ")
        // sb.AppendLine("        And LKS_PRDCD = PLUKarton  ")
        // sb.AppendLine("        And GRR_Koderak = LKS_KodeRak  ")
        // sb.AppendLine("        And GRR_Subrak  = LKS_KodeSubrak ")
        // sb.AppendLine("        And COALESCE(LKS_Noid,'X') Like '%B'   ")
        // sb.AppendLine("        And COALESCE(GRR_FlagCetakan,'X') <> 'Y' ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   )  ")
        // sb.AppendLine("   And NOT EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("     Select grr_koderak  ")
        // sb.AppendLine("       From tbMaster_Lokasi,tbMaster_GroupRak  ")
        // sb.AppendLine("      Where LKS_KodeIGR = '" & KDIGR & "'  ")
        // sb.AppendLine("        And LKS_PRDCD = PLUKarton  ")
        // sb.AppendLine("        And GRR_Koderak = LKS_KodeRak  ")
        // sb.AppendLine("        And GRR_Subrak  = LKS_KodeSubrak ")
        // sb.AppendLine("        And COALESCE(LKS_Noid,'X') Like '%P'  ")
        // sb.AppendLine("        And COALESCE(GRR_FlagCetakan,'X') <> 'Y' ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   ) ")
        // sb.AppendLine("   And NOT EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("     Select grr_koderak  ")
        // sb.AppendLine("       From tbMaster_Lokasi,tbMaster_GroupRak  ")
        // sb.AppendLine("      Where LKS_KodeIGR = '" & KDIGR & "'  ")
        // sb.AppendLine("        And LKS_PRDCD = PLUKarton  ")
        // sb.AppendLine("        And GRR_Koderak = LKS_KodeRak  ")
        // sb.AppendLine("        And GRR_Subrak  = LKS_KodeSubrak         ")
        // sb.AppendLine("        And COALESCE(GRR_FlagCetakan,'X') = 'Y' ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   ) ")
        // sb.AppendLine("   And COALESCE(TolakMinJ,'X') <> 'T' ")
        // sb.AppendLine("   And QTYK > 0  ")

        //! INSERT INTO TEMP_NOJALUR_IDM 4
        // sb.AppendLine("INSERT INTO TEMP_NOJALUR_IDM ")
        // sb.AppendLine("( ")
        // sb.AppendLine("      fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine(") ")
        // sb.AppendLine("SELECT fdrcid, ")
        // sb.AppendLine("      fdnouo, ")
        // sb.AppendLine("      fdkode, ")
        // sb.AppendLine("      fdqtyb, ")
        // sb.AppendLine("      fdkcab, ")
        // sb.AppendLine("      fdtgpb, ")
        // sb.AppendLine("      fdksup, ")
        // sb.AppendLine("      req_id, ")
        // sb.AppendLine("      nama_file, ")
        // sb.AppendLine("      desk, ")
        // sb.AppendLine("      bkp, ")
        // sb.AppendLine("      plukarton, ")
        // sb.AppendLine("      unitkarton, ")
        // sb.AppendLine("      frackarton, ")
        // sb.AppendLine("      plukecil, ")
        // sb.AppendLine("      unitkecil, ")
        // sb.AppendLine("      frackecil, ")
        // sb.AppendLine("      prd_minjual, ")
        // sb.AppendLine("      qtyb, ")
        // sb.AppendLine("      qtyk, ")
        // sb.AppendLine("      tolakminj, ")
        // sb.AppendLine("      avgcost ")
        // sb.AppendLine("  From temp_pbidm_ready  ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'   ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "'  ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')    ")
        // sb.AppendLine("   And EXISTS  ")
        // sb.AppendLine("   (  ")
        // sb.AppendLine("     Select grr_koderak  ")
        // sb.AppendLine("       From tbMaster_Lokasi,tbMaster_GroupRak  ")
        // sb.AppendLine("      Where LKS_KodeIGR = '" & KDIGR & "'  ")
        // sb.AppendLine("        And LKS_PRDCD = PLUKarton  ")
        // sb.AppendLine("        And GRR_Koderak = LKS_KodeRak  ")
        // sb.AppendLine("        And GRR_Subrak  = LKS_KodeSubrak ")
        // sb.AppendLine("        And LKS_NOID IS NULL  ")
        // sb.AppendLine("        And LKS_KodeRak Like 'D%' ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   ) ")
        // sb.AppendLine("   AND NOT EXISTS ")
        // sb.AppendLine("   ( ")
        // sb.AppendLine("     Select LKS_NOID ")
        // sb.AppendLine("       From tbMaster_Lokasi ")
        // sb.AppendLine("      Where LKS_PRDCD = PLUKarton ")
        // sb.AppendLine("        And LKS_NOID Like '%P' ")
        // sb.AppendLine("        And LKS_TIPERAK NOT LIKE  'S%' ")
        // sb.AppendLine("   )  ")
        // sb.AppendLine("   And COALESCE(TolakMinJ,'X') <> 'T'     ")
        // sb.AppendLine("   And QTYK > 0 ")

        //! INSERT INTO TEMP_NOJALUR_IDM 5
        // sb.AppendLine("INSERT INTO TEMP_NOJALUR_IDM ")
        // sb.AppendLine("Select 'B', ")
        // sb.AppendLine("       FDNOUO, ")
        // sb.AppendLine("       FDKODE, ")
        // sb.AppendLine("       FDQTYB, ")
        // sb.AppendLine("       FDKCAB, ")
        // sb.AppendLine("       FDTGPB, ")
        // sb.AppendLine("       FDKSUP, ")
        // sb.AppendLine("       REQ_ID, ")
        // sb.AppendLine("       NAMA_FILE, ")
        // sb.AppendLine("       DESK, ")
        // sb.AppendLine("       BKP, ")
        // sb.AppendLine("       PLUKARTON, ")
        // sb.AppendLine("       UNITKARTON, ")
        // sb.AppendLine("       FRACKARTON, ")
        // sb.AppendLine("       PLUKECIL, ")
        // sb.AppendLine("       UNITKECIL, ")
        // sb.AppendLine("       FRACKECIL, ")
        // sb.AppendLine("       PRD_MINJUAL, ")
        // sb.AppendLine("       QTYB, ")
        // sb.AppendLine("       QTYK, ")
        // sb.AppendLine("       TOLAKMINJ, ")
        // sb.AppendLine("       AVGCOST ")
        // sb.AppendLine("  From temp_pbidm_ready  ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
        // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'   ")
        // sb.AppendLine("   And fdnouo = '" & noPB & "'  ")
        // sb.AppendLine("   And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')    ")
        // sb.AppendLine("   And NOT EXISTS  ")
        // sb.AppendLine("   (  ")
        // sb.AppendLine("     Select BRC_Barcode  ")
        // sb.AppendLine("       From tbMaster_Barcode ")
        // sb.AppendLine("      Where BRC_PRDCD = PLUKECIL ")
        // sb.AppendLine("   ) ")

        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where upper(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        //! INSERT KE MASDPB BULKY
        // if(jum > 0){
            // sb.AppendLine("Insert Into tbMaster_PBOmi ")
            // sb.AppendLine("( ")
            // sb.AppendLine(" pbo_kodeigr, ")
            // sb.AppendLine(" pbo_recordid, ")
            // sb.AppendLine(" pbo_nourut, ")
            // sb.AppendLine(" pbo_batch, ")
            // sb.AppendLine(" pbo_tglpb, ")
            // sb.AppendLine(" pbo_nopb, ")
            // sb.AppendLine(" pbo_kodesbu, ")
            // sb.AppendLine(" pbo_kodemember, ")
            // sb.AppendLine(" pbo_kodeomi, ")
            // sb.AppendLine(" pbo_kodedivisi, ")
            // sb.AppendLine(" pbo_kodedepartemen, ")
            // sb.AppendLine(" pbo_kodekategoribrg, ")
            // sb.AppendLine(" pbo_pluomi, ")
            // sb.AppendLine(" pbo_pluigr, ")
            // sb.AppendLine(" pbo_hrgsatuan, ")
            // sb.AppendLine(" pbo_qtyorder, ")
            // sb.AppendLine(" pbo_qtyrealisasi, ")
            // sb.AppendLine(" pbo_nilaiorder, ")
            // sb.AppendLine(" pbo_ppnorder, ")
            // sb.AppendLine(" pbo_distributionfee, ")
            // sb.AppendLine(" pbo_create_by, ")
            // sb.AppendLine(" pbo_create_dt,  ")
            // sb.AppendLine(" pbo_TglStruk  ")
            // sb.AppendLine(") ")
            // sb.AppendLine("Select '" & KDIGR & "', ")
            // sb.AppendLine("       NULL, ")
            // sb.AppendLine("       row_number() over(), ")
            // sb.AppendLine("       '" & CounterKarton & "', ")
            // sb.AppendLine("       fdtgpb, ")
            // sb.AppendLine("       fdnouo, ")
            // sb.AppendLine("       '" & KodeSBU & "', ")
            // sb.AppendLine("       '" & KodeMember & "', ")
            // sb.AppendLine("       fdkcab, ")
            // sb.AppendLine("       prd_kodedivisi, ")
            // sb.AppendLine("       prd_kodedepartement, ")
            // sb.AppendLine("       prd_kodekategoribarang, ")
            // sb.AppendLine("       fdkode, ")
            // sb.AppendLine("       plukecil, ")
            // sb.AppendLine("       round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + (COALESCE(MPI_MARGIN,3)/100) ),0), ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END, ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END, ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0), ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END, ")
            // sb.AppendLine("       0, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       current_timestamp, ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine("  FROM temp_pbidm_ready ")
            // sb.AppendLine("  JOIN tbmaster_prodmast on prd_prdcd = PLUKarton ")
            // sb.AppendLine("  JOIN tbMaster_MarginPluIDM on MPI_PLUIGR = PLUKARTON ")
            // sb.AppendLine("  WHERE req_id = '" & IP & "' ")
            // sb.AppendLine("     and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("     and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("     and qtyb > 0 ")
            // sb.AppendLine("     and COALESCE(TolakMinJ,'X') <> 'T' ")
        // }else{
            // sb.AppendLine("Insert Into tbMaster_PBOmi ")
            // sb.AppendLine("( ")
            // sb.AppendLine(" pbo_kodeigr, ")
            // sb.AppendLine(" pbo_recordid, ")
            // sb.AppendLine(" pbo_nourut, ")
            // sb.AppendLine(" pbo_batch, ")
            // sb.AppendLine(" pbo_tglpb, ")
            // sb.AppendLine(" pbo_nopb, ")
            // sb.AppendLine(" pbo_kodesbu, ")
            // sb.AppendLine(" pbo_kodemember, ")
            // sb.AppendLine(" pbo_kodeomi, ")
            // sb.AppendLine(" pbo_kodedivisi, ")
            // sb.AppendLine(" pbo_kodedepartemen, ")
            // sb.AppendLine(" pbo_kodekategoribrg, ")
            // sb.AppendLine(" pbo_pluomi, ")
            // sb.AppendLine(" pbo_pluigr, ")
            // sb.AppendLine(" pbo_hrgsatuan, ")
            // sb.AppendLine(" pbo_qtyorder, ")
            // sb.AppendLine(" pbo_qtyrealisasi, ")
            // sb.AppendLine(" pbo_nilaiorder, ")
            // sb.AppendLine(" pbo_ppnorder, ")
            // sb.AppendLine(" pbo_distributionfee, ")
            // sb.AppendLine(" pbo_create_by, ")
            // sb.AppendLine(" pbo_create_dt,  ")
            // sb.AppendLine(" pbo_TglStruk  ")
            // sb.AppendLine(") ")
            // sb.AppendLine("Select '" & KDIGR & "', ")
            // sb.AppendLine("       NULL, ")
            // sb.AppendLine("       Row_number() over(), ")
            // sb.AppendLine("       '" & CounterKarton & "', ")
            // sb.AppendLine("       fdtgpb, ")
            // sb.AppendLine("       fdnouo, ")
            // sb.AppendLine("       '" & KodeSBU & "', ")
            // sb.AppendLine("       '" & KodeMember & "', ")
            // sb.AppendLine("       fdkcab, ")
            // sb.AppendLine("       prd_kodedivisi, ")
            // sb.AppendLine("       prd_kodedepartement, ")
            // sb.AppendLine("       prd_kodekategoribarang, ")
            // sb.AppendLine("       fdkode, ")
            // sb.AppendLine("       plukecil, ")
            // sb.AppendLine("       round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + " & PersenMargin & "),0), ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END, ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END, ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + " & PersenMargin & "),0), ")
            // sb.AppendLine("       QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + " & PersenMargin & "),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END, ")
            // sb.AppendLine("       0, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       current_timestamp, ")
            // sb.AppendLine("       CURRENT_DATE ")
            // sb.AppendLine(" From temp_pbidm_ready,tbmaster_prodmast ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and qtyb > 0 ")
            // sb.AppendLine("   and prd_prdcd = PLUKarton ")
            // sb.AppendLine("   and COALESCE(TolakMinJ,'X') <> 'T' ")
        // }

        //! GET -> PBO_NoUrut
        // sb.AppendLine("Select COALESCE(Max(pbo_nourut),1) ")
        // sb.AppendLine("  From tbMaster_PbOMI ")
        // sb.AppendLine(" Where PBO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY')")

        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where upper(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        //! INSERT KE MASDPB PIECES
        // if(jum > 0){
            // sb.AppendLine("Insert Into tbMaster_PBOmi ")
            // sb.AppendLine("( ")
            // sb.AppendLine(" pbo_kodeigr, ")
            // sb.AppendLine(" pbo_recordid, ")
            // sb.AppendLine(" pbo_nourut, ")
            // sb.AppendLine(" pbo_batch, ")
            // sb.AppendLine(" pbo_tglpb, ")
            // sb.AppendLine(" pbo_nopb, ")
            // sb.AppendLine(" pbo_kodesbu, ")
            // sb.AppendLine(" pbo_kodemember, ")
            // sb.AppendLine(" pbo_kodeomi, ")
            // sb.AppendLine(" pbo_kodedivisi, ")
            // sb.AppendLine(" pbo_kodedepartemen, ")
            // sb.AppendLine(" pbo_kodekategoribrg, ")
            // sb.AppendLine(" pbo_pluomi, ")
            // sb.AppendLine(" pbo_pluigr, ")
            // sb.AppendLine(" pbo_hrgsatuan, ")
            // sb.AppendLine(" pbo_qtyorder, ")
            // sb.AppendLine(" pbo_qtyrealisasi, ")
            // sb.AppendLine(" pbo_nilaiorder, ")
            // sb.AppendLine(" pbo_ppnorder, ")
            // sb.AppendLine(" pbo_distributionfee, ")
            // sb.AppendLine(" pbo_create_by, ")
            // sb.AppendLine(" pbo_create_dt, ")
            // sb.AppendLine(" pbo_TglStruk  ")
            // sb.AppendLine(") ")
            // sb.AppendLine("Select '" & KDIGR & "', ")
            // sb.AppendLine("       NULL, ")
            // sb.AppendLine("       row_number() over() + " & PBO_NoUrut & ", ")
            // sb.AppendLine("       '" & CounterKecil & "', ")
            // sb.AppendLine("       fdtgpb, ")
            // sb.AppendLine("       fdnouo, ")
            // sb.AppendLine("       '" & KodeSBU & "', ")
            // sb.AppendLine("       '" & KodeMember & "', ")
            // sb.AppendLine("       fdkcab, ")
            // sb.AppendLine("       prd_kodedivisi, ")
            // sb.AppendLine("       prd_kodedepartement, ")
            // sb.AppendLine("       prd_kodekategoribarang, ")
            // sb.AppendLine("       fdkode, ")
            // sb.AppendLine("       plukecil, ")
            // sb.AppendLine("       round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0), ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END, ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END, ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0), ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END, ")
            // sb.AppendLine("       0, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       current_timestamp, ")
            // sb.AppendLine("       CURRENT_DATE  ")
            // sb.AppendLine("  FROM temp_pbidm_ready")
            // sb.AppendLine("  JOIN tbmaster_prodmast on prd_prdcd = PLUKarton ")
            // sb.AppendLine("  JOIN tbMaster_MarginPluIDM on MPI_PLUIGR = PLUKARTON ")
            // sb.AppendLine(" WHERE req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and qtyK > 0 ")
            // sb.AppendLine("   and COALESCE(TolakMinJ,'X') <> 'T' ")
        // }else{
            // sb.AppendLine("Insert Into tbMaster_PBOmi ")
            // sb.AppendLine("( ")
            // sb.AppendLine(" pbo_kodeigr, ")
            // sb.AppendLine(" pbo_recordid, ")
            // sb.AppendLine(" pbo_nourut, ")
            // sb.AppendLine(" pbo_batch, ")
            // sb.AppendLine(" pbo_tglpb, ")
            // sb.AppendLine(" pbo_nopb, ")
            // sb.AppendLine(" pbo_kodesbu, ")
            // sb.AppendLine(" pbo_kodemember, ")
            // sb.AppendLine(" pbo_kodeomi, ")
            // sb.AppendLine(" pbo_kodedivisi, ")
            // sb.AppendLine(" pbo_kodedepartemen, ")
            // sb.AppendLine(" pbo_kodekategoribrg, ")
            // sb.AppendLine(" pbo_pluomi, ")
            // sb.AppendLine(" pbo_pluigr, ")
            // sb.AppendLine(" pbo_hrgsatuan, ")
            // sb.AppendLine(" pbo_qtyorder, ")
            // sb.AppendLine(" pbo_qtyrealisasi, ")
            // sb.AppendLine(" pbo_nilaiorder, ")
            // sb.AppendLine(" pbo_ppnorder, ")
            // sb.AppendLine(" pbo_distributionfee, ")
            // sb.AppendLine(" pbo_create_by, ")
            // sb.AppendLine(" pbo_create_dt, ")
            // sb.AppendLine(" pbo_TglStruk  ")
            // sb.AppendLine(") ")
            // sb.AppendLine("Select '" & KDIGR & "', ")
            // sb.AppendLine("       NULL, ")
            // sb.AppendLine("       row_number() over() + " & PBO_NoUrut & ", ")
            // sb.AppendLine("       '" & CounterKecil & "', ")
            // sb.AppendLine("       fdtgpb, ")
            // sb.AppendLine("       fdnouo, ")
            // sb.AppendLine("       '" & KodeSBU & "', ")
            // sb.AppendLine("       '" & KodeMember & "', ")
            // sb.AppendLine("       fdkcab, ")
            // sb.AppendLine("       prd_kodedivisi, ")
            // sb.AppendLine("       prd_kodedepartement, ")
            // sb.AppendLine("       prd_kodekategoribarang, ")
            // sb.AppendLine("       fdkode, ")
            // sb.AppendLine("       plukecil, ")
            // sb.AppendLine("       round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + " & PersenMargin & "),0), ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END, ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END, ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + " & PersenMargin & "),0), ")
            // sb.AppendLine("       QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + " & PersenMargin & "),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END, ")
            // sb.AppendLine("       0, ")
            // sb.AppendLine("       '" & UserID & "', ")
            // sb.AppendLine("       current_timestamp, ")
            // sb.AppendLine("       CURRENT_DATE  ")
            // sb.AppendLine("  From temp_pbidm_ready,tbmaster_prodmast ")
            // sb.AppendLine(" Where req_id = '" & IP & "' ")
            // sb.AppendLine("   and fdnouo = '" & noPB & "' ")
            // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY')   ")
            // sb.AppendLine("   and qtyK > 0 ")
            // sb.AppendLine("   and prd_prdcd = PLUKarton ")
            // sb.AppendLine("   and COALESCE(TolakMinJ,'X') <> 'T' ")
        // }

        //! '-------------------------------'
        //! '+ UPDATE RECID TBMASTER_PBOMI +'
        //! '-------------------------------'

        //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR KARTON
        // sb.AppendLine("Update tbMaster_PBOMI ")
        // sb.AppendLine("   Set pbo_recordID = '3' ")
        // sb.AppendLine(" Where EXISTS ")
        // sb.AppendLine(" ( ")
        // sb.AppendLine("	Select PluKecil ")
        // sb.AppendLine("	  From TEMP_KARTON_NONDPD_IDM ")
        // sb.AppendLine("	 Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("       And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("       And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("       And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("       And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%' ")
        // sb.AppendLine(" ) ")
        // sb.AppendLine("   And PBO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   And PBO_Batch = '" & CounterKarton & "' ")

        //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR PIECES
        // sb.AppendLine("Update tbMaster_PBOMI ")
        // sb.AppendLine("   Set pbo_recordID = '3' ")
        // sb.AppendLine(" Where EXISTS ")
        // sb.AppendLine(" ( ")
        // sb.AppendLine("	Select PluKecil ")
        // sb.AppendLine("	  From TEMP_NOJALUR_IDM ")
        // sb.AppendLine("	 Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("       And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("       And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("       And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("       And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%' ")
        // sb.AppendLine("  And Not EXISTS ")
        // sb.AppendLine("    ( ")
        // sb.AppendLine("       Select grr_grouprak ")
        // sb.AppendLine("         from tbmaster_grouprak ")
        // sb.AppendLine("         join tbmaster_lokasi lks2 on grr_koderak = lks2.lks_koderak ")
        // sb.AppendLine("          and grr_subrak = lks2.lks_kodesubrak ")
        // sb.AppendLine("          and LKS_KodeRak Like 'D%' ")
        // sb.AppendLine("          And LKS_TIPERAK NOT LIKE 'S%' ")
        // sb.AppendLine("          and lks_prdcd = plukarton ")
        // sb.AppendLine("    )  ")
        // sb.AppendLine(" ) ")
        // sb.AppendLine("   And PBO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR PIECES
        // sb.AppendLine("Update tbMaster_PBOMI ")
        // sb.AppendLine("   Set pbo_recordID = '3' ")
        // sb.AppendLine(" Where EXISTS ")
        // sb.AppendLine(" ( ")
        // sb.AppendLine("	Select PluKecil ")
        // sb.AppendLine("	  From TEMP_JALURKERTAS_IDM ")
        // sb.AppendLine("	 Where REQ_ID = '" & IP & "' ")
        // sb.AppendLine("       And FDKCAB = '" & KodeToko & "' ")
        // sb.AppendLine("       And fdnouo = '" & noPB & "' ")
        // sb.AppendLine("       And fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("       And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%' ")
        // sb.AppendLine(" ) ")
        // sb.AppendLine("   And PBO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! GET -> jumItmCSV
        // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
        // sb.AppendLine("  From csv_pb_pot ")
        // sb.AppendLine(" Where CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   And CPP_KodeToko = '" & KodeToko & "' ")
        // sb.AppendLine("   And CPP_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! GET -> jumTolakan
        // sb.AppendLine("Select COALESCE(Count(1),0)  ")
        // sb.AppendLine("  From temp_cetakpb_tolakan_idm ")
        // sb.AppendLine(" Where REQ_ID = '" & IP & "'   ")
        // sb.AppendLine("   And KCAB = '" & KodeToko & "'    ")
        // sb.AppendLine("   And nodok = '" & noPB & "'   ")
        // sb.AppendLine("   And tgldok = to_date('" & tglPB & "','DD-MM-YYYY') ")

        // If jumItmCSV - jumTolakan <= 0 Then MsgBox("Semua Item Ditolak !!, Silahkan Cek Di TBTR_TOLAKANPBOMI" & vbNewLine & "TOKO : " & KodeToko & ",NOPB : " & noPB & " TGLPB : " & tglPB) : Exit Sub

        //! CEK ADA YANG MASUK PBOMI GA??
        // sb.AppendLine("Select COALESCE(count(pbo_pluigr),0) ")
        // sb.AppendLine("  From tbMaster_PBOMI ")
        // sb.AppendLine(" Where PBO_KodeIGR = '" & KDIGR & "'   ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "'    ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "'   ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! INSERT INTO tbtr_tolakanpbomi - TOTAL QTY ORDER 0
        // sb.AppendLine("INSERT INTO tbtr_tolakanpbomi ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  TLKO_KodeIGR, ")
        // sb.AppendLine("  TLKO_KodeOMI, ")
        // sb.AppendLine("  TLKO_TglPB, ")
        // sb.AppendLine("  TLKO_NOPB, ")
        // sb.AppendLine("  TLKO_PLUIGR, ")
        // sb.AppendLine("  TLKO_PLUOMI, ")
        // sb.AppendLine("  TLKO_DESC, ")
        // sb.AppendLine("  TLKO_KETTOLAKAN, ")
        // sb.AppendLine("  TLKO_QtyOrder, ")
        // sb.AppendLine("  TLKO_LastCost, ")
        // sb.AppendLine("  TLKO_Nilai, ")
        // sb.AppendLine("  TLKO_Create_By, ")
        // sb.AppendLine("  TLKO_Create_Dt ")
        // sb.AppendLine(") ")
        // sb.AppendLine("SELECT '" & KDIGR & "', ")
        // sb.AppendLine("       FDKCAB, ")
        // sb.AppendLine("       FDTGPB, ")
        // sb.AppendLine("       FDNOUO, ")
        // sb.AppendLine("       PLUKECIL, ")
        // sb.AppendLine("       FDKODE, ")
        // sb.AppendLine("       DESK, ")
        // sb.AppendLine("       'TOTAL QTY ORDER 0', ")
        // sb.AppendLine("       0, ")
        // sb.AppendLine("       0, ")
        // sb.AppendLine("       0, ")
        // sb.AppendLine("       '" & UserID & "', ")
        // sb.AppendLine("       current_timestamp  ")
        // sb.AppendLine("  FROM temp_pbidm_ready ")
        // sb.AppendLine(" Where req_id = '" & IP & "'  ")
        // sb.AppendLine("   and fdnouo = '" & noPB & "'  ")
        // sb.AppendLine("   and fdtgpb = to_date('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   and fdqtyb = 0  ")

        //! SET FLAG CSV_PB_IDM
        // sb.AppendLine("UPDATE CSV_PB_POT ")
        // sb.AppendLine("   SET CPP_FLAG = '1' ")
        // sb.AppendLine(" WHERE CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_noPB = '" & noPB & "' ")
        // sb.AppendLine("   AND CPP_KodeToko = '" & KodeToko & "' ")
        // sb.AppendLine("   AND CPP_TglPB = TO_DATE('" & tglPB & "','DD-MM-YYYY') ")
        // sb.AppendLine("   AND CPP_FLAG IS NULL ")

        // if(nothing update FLAG CSV_PB_IDM){
        //     ("Total Permintaan Semua Item Jumlahnya NOL (Silahkan Cek Di TBTR_TOLAKAN_PBOMI) !! " & vbNewLine & "TOKO : " & KodeToko & ",NOPB : " & noPB & " TGLPB : " & tglPB

        //     //! PANGGIL FUNCTION
        //     RefreshGridHeader();
        //     return;
        // }

        //! GET -> rphOrder
        // sb.AppendLine("Select sum(COALESCE(pbo_nilaiorder,0))  ")
        // sb.AppendLine("  From tbMaster_PBOMI ")
        // sb.AppendLine(" Where PBO_KodeIGR = '" & KDIGR & "'   ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "'    ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "'   ")
        // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

        //! Insert Into TBTR_HEADER_POT
        // sb.AppendLine("INSERT INTO TBTR_HEADER_POT ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  HDP_kodeigr, ")
        // sb.AppendLine("  HDP_flag, ")
        // sb.AppendLine("  HDP_tgltransaksi, ")
        // sb.AppendLine("  HDP_kodetoko, ")
        // sb.AppendLine("  HDP_nopb, ")
        // sb.AppendLine("  HDP_tglpb, ")
        // sb.AppendLine("  HDP_itempb, ")
        // sb.AppendLine("  HDP_itemvalid, ")
        // sb.AppendLine("  HDP_rphvalid, ")
        // sb.AppendLine("  HDP_filepb, ")
        // sb.AppendLine("  HDP_create_by, ")
        // sb.AppendLine("  HDP_create_dt   ")
        // sb.AppendLine(")   ")
        // sb.AppendLine("VALUES ")
        // sb.AppendLine("( ")
        // sb.AppendLine("   '" & KDIGR & "', ")
        // sb.AppendLine("	'2', ")
        // sb.AppendLine("	CURRENT_DATE, ")
        // sb.AppendLine("	'" & KodeToko & "', ")
        // sb.AppendLine("	'" & noPB & "', ")
        // sb.AppendLine("	to_date('" & Strings.Right(tglPB, 4) & Mid(tglPB, 4, 2) & Strings.Left(tglPB, 2) & "','YYYYMMDD'), ")
        // sb.AppendLine("	" & jumItmCSV & ", ")
        // sb.AppendLine("	" & jumItmCSV - jumTolakan & ", ")
        // sb.AppendLine("	" & rphOrder & ", ")
        // sb.AppendLine("	'" & FilePB & "', ")
        // sb.AppendLine("	'" & UserID & "', ")
        // sb.AppendLine("	current_timestamp ")
        // sb.AppendLine(") ")

        //! Insert Into DCP_DATA_POT
        // sb.AppendLine("INSERT INTO DCP_DATA_POT ")
        // sb.AppendLine("( ")
        // sb.AppendLine("  DDP_KodeSBU, ")
        // sb.AppendLine("  DDP_KodeToko, ")
        // sb.AppendLine("  DDP_NoPB, ")
        // sb.AppendLine("  DDP_TglPB, ")
        // sb.AppendLine("  DDP_PRDCD, ")
        // sb.AppendLine("  DDP_PLUIDM, ")
        // sb.AppendLine("  DDP_Deskripsi, ")
        // sb.AppendLine("  DDP_Unit, ")
        // sb.AppendLine("  DDP_Frac, ")
        // sb.AppendLine("  DDP_FlagBKP1, ")
        // sb.AppendLine("  DDP_FlagBKP2, ")
        // sb.AppendLine("  DDP_QtyOrder,   ")
        // sb.AppendLine("  DDP_TglUpload, ")
        // sb.AppendLine("  DDP_IP   ")
        // sb.AppendLine(") ")
        // sb.AppendLine("Select '" & KodeSBU & "', ")
        // sb.AppendLine("       PBO_KodeOMI, ")
        // sb.AppendLine("	      PBO_NoPB, ")
        // sb.AppendLine("	      PBO_TglPB, ")
        // sb.AppendLine("	      PBO_PluIGR, ")
        // sb.AppendLine("	      PBO_PluOMI, ")
        // sb.AppendLine("	      SUBSTR(PRD_DeskripsiPendek,1,20), ")
        // sb.AppendLine("	      PRD_Unit, ")
        // sb.AppendLine("	      PRD_Frac, ")
        // sb.AppendLine("	      PRD_FlagBKP1, ")
        // sb.AppendLine("	      PRD_FlagBKP2, ")
        // sb.AppendLine("	      PBO_QtyOrder, ")
        // sb.AppendLine("	      CURRENT_DATE, ")
        // sb.AppendLine("	      '" & IP & "' ")
        // sb.AppendLine("  From tbMaster_PbOMI, ")
        // sb.AppendLine("       tbMaster_Prodmast ")
        // sb.AppendLine(" Where PRD_PRDCD = PBO_PLUIGR ")
        // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
        // sb.AppendLine("   And PBo_TglPB = TO_DATE('" & tglPB & "','DD-MM-YYYY') ")

        // CetakALL_1(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_2(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_3(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_4(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_5(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_6(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
    }
}
