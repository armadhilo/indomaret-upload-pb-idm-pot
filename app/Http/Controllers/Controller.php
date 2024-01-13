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

    public function DeleteCSVPOT($kodeToko){

        // sb.AppendLine("DELETE FROM CSV_PB_POT ")
        // sb.AppendLine(" Where CPP_IP = '" & IP & "' ")
        // sb.AppendLine("   AND CPP_KodeToko = '" & kdToko & "' ")
        // sb.AppendLine("   AND CPP_TglProses = CURRENT_DATE ")

        DB::table('csv_pb_pot')
            ->where([
                'cpp_ip' => $this->getIP(),
                'cpp_kodetoko' => $kodeToko
            ])
            ->whereRaw("cpp_tglproses = CURRENT_DATE")
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

    public function KurangiAkumulasiPB($kodeToko, $noPB){

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

        $ip = $this->getIP();

        DB::select("
            MERGE INTO stock_akumulasipb_pot t
            USING (
            SELECT DISTINCT cpp_pluigr, cpp_qty
            FROM csv_pb_pot
            WHERE cpp_kodetoko = '$kodeToko'
                AND cpp_nopb = '$noPB'
                AND cpp_ip = '$ip'
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

    public function CetakAll_1($kodeToko,$ip,$noPB,$tglPB, $PersenMargin){

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! CHECK DATA
        // sb.AppendLine("Select coalesce(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where UPPER(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        //! CETAK DATA 1
        $check = DB::table('information_schema.columns')
            ->whereRaw("UPPER(table_name) = 'TBMASTER_MARGINPLUIDM'")
            ->count();

        if($check > 0){

            //! INSERT INTO PBIDM_LISTORDER
            DB::select("
                INSERT INTO PBIDM_LISTORDER
                (
                PBL_KODETOKO,
                PBL_NOPB,
                PBL_TGLPB,
                PBL_PLU,
                PBL_DESKRIPSI,
                PBL_UNIT,
                PBL_FRAC,
                PBL_QTYB,
                PBL_QTYK,
                PBL_QTYO,
                PBL_HRGSATUAN,
                PBL_NILAI,
                PBL_PPN,
                PBL_TOTAL,
                PBL_CREATE_BY,
                PBL_CREATE_DT
                )
            Select '" . $kodeToko . "' as KODETOKO,
                    '" . $noPB . "' as NoPB,
                    TO_DATE('" . $tglPB . "','DD-MM-YYYY') as TglPB,
                    plukarton as plu,
                    desk,
                    unitkarton as unit,
                    frackarton as frac,
                    qtyb as qty,
                    qtyk as frc,
                    fdqtyb as inpcs,
                    Round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Harga,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Nilai,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL,
                    '" . session('userid') . "',
                    CURRENT_DATE
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_MarginPLUIDM
            Where req_id = '" . $ip . "'
                and fdnouo = '" . $noPB . "'
                and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                and prd_prdcd = plukarton
                and MPI_PluIGR = PLUKARTON
            ");

            $data['data'] = DB::select("
                Select plukarton as plu,
                    desk,
                    unitkarton ||'/'|| frackarton as unit,
                    qtyb as qty,
                    qtyk as frc,
                    fdqtyb as inpcs,
                    Round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Harga,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) as Nilai,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN,
                    fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100))) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_MarginPLUIDM
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
                    and MPI_PluIGR = PLUKARTON
                Order By plukarton
            ");
        }else{
            //! INSERT INTO PBIDM_LISTORDER
            DB::select("
                INSERT INTO PBIDM_REKAPORDER
                (
                    PBL_KODETOKO,
                    PBL_NOPB,
                    PBL_TGLPB,
                    PBL_PLU,
                    PBL_DESKRIPSI,
                    PBL_UNIT,
                    PBL_QTYB,
                    PBL_QTYK,
                    PBL_QTYO,
                    PBL_HRGSATUAN,
                    PBL_NILAI,
                    PBL_PPN,
                    PBL_TOTAL,
                    PBL_CREATE_BY,
                    PBL_CREATE_DT
                )
                Select '" . $kodeToko . "' as KODETOKO
                    '" . $noPB . "' as NoPB,
                    TO_DATE('" . $tglPB . "','YYYYMMDD') as TglPB,
                    plukarton as plu,
                    desk,
                    unitkarton ||'/'|| frackarton as unit,
                    qtyb as qty,
                    qtyk as frc,
                    fdqtyb as inpcs,
                    Round(avgcost * (1+ " . $PersenMargin . ")) as Harga,
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) as Nilai,
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN,
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTAL,
                    '" . session('userid') . "',
                    CURRENT_DATE
                From temp_pbidm_ready, tbMaster_prodmast
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
            ");

            // sb = New StringBuilder
            $data['data'] = DB::select("
                Select plukarton as plu,
                    desk,
                    unitkarton ||'/'|| frackarton as unit,
                    qtyb as qty
                    qtyk as frc,
                    fdqtyb as inpcs,
                    Round(avgcost * (1+ " . $PersenMargin . ")) as Harga
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) as Nilai
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END as PPN
                    fdqtyb * round(avgcost * (1+" . $PersenMargin . ")) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END) as TOTA
                From temp_pbidm_ready, tbMaster_prodmas
                Where req_id = '" . $ip . "
                    and fdnouo = '" . $noPB . "
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarto
                Order By plukarto
            ");
        }

        return $data;
    }

    public function CetakAll_2($kodeToko,$ip,$noPB,$tglPB, $PersenMargin){
        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! CHECK DATA
        // sb.AppendLine("Select coalesce(COUNT(1),0)  ")
        // sb.AppendLine("  From information_schema.columns ")
        // sb.AppendLine(" Where UPPER(table_name) = 'TBMASTER_MARGINPLUIDM' ")

        //! CETAK DATA 1
        $check = DB::table('information_schema.columns')
            ->whereRaw("UPPER(table_name) = 'TBMASTER_MARGINPLUIDM'")
            ->count();

        if($check > 0){
            //! INSERT INTO PBIDM_REKAPORDER
            DB::select("
                INSERT INTO PBIDM_REKAPORDER (
                    PBR_KODETOKO,
                    PBR_NOPB,
                    PBR_TGLPB,
                    PBR_NAMADIVISI,
                    PBR_KODEDIVISI,
                    PBL_ITEM,
                    PBL_NILAI,
                    PBL_PPN,
                    PBL_SUBTOTAL,
                    PBL_CREATE_BY,
                    PBL_CREATE_DT
                    )
                Select '" . $kodeToko . "' as KODETOKO,
                        '" . $noPB . "' as NoPB,
                        TO_DATE('" . $tglPB . "','DD-MM-YYYY') as TglPB,
                        DIV_NamaDivisi as NamaDivisi,
                        PRD_KodeDivisi as KodeDivisi,
                        Count(PLUKARTON) as Item,
                        SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)))) as Nilai,
                        SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN,
                        SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL,
                        '" . session('userid') . "',
                        CURRENT_DATE
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi, tbMaster_MarginPLUIDM
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
                    and DIV_KodeDivisi = PRD_KodeDivisi
                    and MPI_PluIGR = PLUKARTON
                Group By DIV_NamaDivisi,
                    PRD_KodeDivisi
            ");

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_REKAPORDER")
            // '---- 24-03-2014

            // sb = New StringBuilder
            $data['data'] = DB::select("
                Select DIV_NamaDivisi as NamaDivisi,
                    PRD_KodeDivisi as KodeDivisi,
                    Count(PLUKARTON) as Item,
                    SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)))) as Nilai,
                    SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN,
                    SUM(fdqtyb * round(avgcost * (1+(coalesce(MPI_MARGIN,3)/100)) * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi, tbMaster_MarginPLUIDM
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
                    and DIV_KodeDivisi = PRD_KodeDivisi
                    and MPI_PluIGR = PLUKARTON
                Group By DIV_NamaDivisi,
                    PRD_KodeDivisi
                Order By PRD_KodeDivisi
            ");
        }else{
            //! INSERT INTO PBIDM_REKAPORDER
            DB::select("
                INSERT INTO PBIDM_REKAPORDER (
                    PBR_KODETOKO,
                    PBR_NOPB,
                    PBR_TGLPB,
                    PBR_NAMADIVISI,
                    PBR_KODEDIVISI,
                    PBL_ITEM,
                    PBL_NILAI,
                    PBL_PPN,
                    PBL_SUBTOTAL,
                    PBL_CREATE_BY,
                    PBL_CREATE_DT
                    )
                Select '" . $kodeToko . "' as KODETOKO
                    '" . $noPB . "' as NoPB,
                    TO_DATE('" . $tglPB . "','YYYYMMDD') as TglPB,
                    DIV_NamaDivisi as NamaDivisi,
                    PRD_KodeDivisi as KodeDivisi,
                    Count(PLUKARTON) as Item,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . "))) as Nilai,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . ") * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . ") * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL,
                    '" . session('userid') . "',
                    CURRENT_DATE
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
                    and DIV_KodeDivisi = PRD_KodeDivisi
                Group By DIV_NamaDivisi,
                    PRD_KodeDivisi
            ");

            // ExecQRY(sb.ToString, "INSERT INTO PBIDM_REKAPORDER")
            // '---- 24-03-2014

            // sb = New StringBuilder
            $data['data'] = DB::select("
                Select DIV_NamaDivisi as NamaDivisi,
                    PRD_KodeDivisi as KodeDivisi,
                    Count(PLUKARTON) as Item,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . "))) as Nilai,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . ") * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END)) as PPN,
                    SUM(fdqtyb * round(avgcost * (1+" . $PersenMargin . ") * (1 + CASE WHEN coalesce(PRD_FlagBKP1,'X') = 'Y' THEN (COALESCE(PRD_PPN,0) / 100) ELSE 0 END))) as SUBTOTAL
                From temp_pbidm_ready, tbMaster_prodmast, tbMaster_Divisi
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and prd_prdcd = plukarton
                    and DIV_KodeDivisi = PRD_KodeDivisi
                Group By DIV_NamaDivisi,
                        PRD_KodeDivisi
                Order By PRD_KodeDivisi
            ");
        }

        return $data;
    }

    public function CetakAll_3($kodeToko,$ip,$noPB,$tglPB){
        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! INSERT INTO PBIDM_KARTONNONDPD
        //? = "KARTON NON DPD"
        DB::select("
            INSERT INTO PBIDM_KARTONNONDP
            (
                PBD_KODETOKO,
                PBD_NOPB,
                PBD_TGLPB,
                PBD_NAMAGROUP,
                PBD_KODERAK,
                PBD_SUBRAK,
                PBD_TIPERAK,
                PBD_PLU,
                PBD_NOURUT,
                PBD_DESKRIPSI,
                PBD_TAG,
                PBD_QTY,
                PBD_UNIT,
                PBD_FRAC,
                PBD_STOK,
                PBD_CREATE_BY,
                PBD_CREATE_DT
            )
            Select '" . $kodeToko . "' as KODETOKO,
                '" . $noPB . "' as NoPB,
                TO_DATE('" . $tglPB . "','DD-MM-YYYY') as TglPB,
                GRR_GroupRak as NamaGroup,
                LKS_KodeRak as KodeRak,
                LKS_KodeSubRak as SubRak,
                LKS_TipeRak as TipeRak,
                PLUKARTON as PLU,
                LKS_NoUrut as NoUrut,
                Desk,
                PRD_KodeTag as TAG,
                QTYB as Order,
                UNITKarton,
                FracKarton,
                Stok,
                '" . session('userid') . "',
                CURRENT_DATE
            From temp_karton_nondpd_idm,tbMaster_Prodmast
            Where REQ_ID = '" . $ip . "'
            And FDKCAB = '" . $kodeToko . "'
            And FDNOUO = '" . $noPB . "'
            And FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            And PRD_PRDCD = PLUKARTON
        ");

        $data['data'] = DB::select("
            Select DISTINCT GRR_GroupRak as NamaGroup,
                LKS_KodeRak as KodeRak,
                LKS_KodeSubRak as SubRak,
                LKS_TipeRak as TipeRak,
                PLUKARTON as PLU,
                LKS_NoUrut as NoUrut,
                Desk,
                PRD_KodeTag as TAG,
                QTYB as Order,
                UNITKarton ||'/'|| FracKarton as UNIT,
                Stok
            From temp_karton_nondpd_idm,tbMaster_Prodmast
            Where REQ_ID = '" . $ip . "'
                And FDKCAB = '" . $kodeToko . "'
                And FDNOUO = '" . $noPB . "'
                And FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                And PRD_PRDCD = PLUKARTON
            Order By LKS_KodeRak,LKS_KodeSubRak,LKS_TipeRak,LKS_NoUrut
        ");
    }

    public function CetakAll_4($kodeToko,$ip,$noPB,$tglPB){
        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! ISI dtOrderDitolak
        //? f = "ORDER DITOLAK"
        $data['data'] = DB::select("
            Select PLU as PLUIDM,
                PLUIGR,
                PRD_DeskripsiPanjang as DESK,
                PRD_UNIT||'/'||PRD_Frac as UNIT,
                QTYO as QTY,
                KETA as Keterangan
            From temp_cetakpb_tolakan_idm,tbMaster_Prodmast
            Where REQ_ID = '" . $ip . "'
                And KCAB = '" . $kodeToko . "'
                And NODOK = '" . $noPB . "'
                And TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')
                And PRD_PRDCD = PLUIGR
                And KETA <> 'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM'
        ");

        return $data;
    }

    public function CetakAll_5($kodeToko,$ip,$noPB,$tglPB){
        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! INSERT INTO PBIDM_RAKJALUR_TIDAKKETEMU
        //? f = "RAK JALUR TIDAK KETEMU";
        DB::select("
            INSERT INTO PBIDM_RAKJALUR_TIDAKKETEM
            (
                PBT_KODETOKO,
                PBT_NOPB,
                PBT_TGLPB,
                PBT_PLU,
                PBT_DESKRIPSI,
                PBT_KODERAK,
                PBT_SUBRAK,
                PBT_TIPERAK,
                PBT_SHELVINGRAK,
                PBT_QTYB,
                PBT_QTYK,
                PBT_UNITKARTON,
                PBT_FRACKARTON,
                PBT_RECORDID,
                PBT_CREATE_BY,
                PBT_CREATE_DT
            )
            Select DISTINCT '" . $kodeToko . "' as KODETOKO,
                '" . $noPB . "' as NoPB,
                TO_DATE('" . $tglPB . "','DD-MM-YYYY') as TglPB,
                NJI.PluKarton as PLU,
                NJI.DESK,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeRak,'') END as KodeRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeSubrak,'') END as SubRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_TipeRak,'') END as TipeRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_ShelvingRak,'') END as ShelvingRak,
                NJI.QTYB as OrderCTN,
                NJI.QTYK as OrderPCS,
                NJI.UnitKarton,
                NJI.FracKarton,
                coalesce(NJI.FDRCID,'X') as RECID,
                '" . session('userid') . "',
                CURRENT_DATE
            From TEMP_NOJALUR_IDM NJI), tbMaster_Lokasi
            join tbMaster_Lokasi on LKS_PRDCD = PLUKARTON
            Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                And LKS_KodeRak Not Like 'D%'
                And REQ_ID = '" . $ip . "'
                And FDKCAB = '" . $kodeToko . "'
                And fdnouo = '" . $noPB . "'
                And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                And LKS_TIPERAK NOT LIKE  'S%'
                And Not EXISTS
                (
                    Select grr_grouprak
                        from tbmaster_grouprak,tbmaster_lokasi lks2
                    where grr_koderak = lks2.lks_koderak
                        and grr_subrak = lks2.lks_kodesubrak
                        and LKS_KodeRak Like 'D%'
                        And LKS_TIPERAK NOT LIKE 'S%'
                        and lks_prdcd = plukarton
                )
        ");

        // sb = New StringBuilder
        $data['data'] = DB::select("
            Select DISTINCT NJI.PluKarton as PLU,
                NJI.DESK,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeRak,'') END as KodeRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_KodeSubrak,'') END as SubRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_TipeRak,'') END as TipeRak,
                CASE WHEN NJI.FDRCID = 'B' THEN '' ELSE coalesce(lks_ShelvingRak,'') END as ShelvingRak,
                NJI.QTYB as OrderCTN,
                NJI.QTYK as OrderPCS,
                NJI.UnitKarton||'/'||NJI.FracKarton as UNIT,
                coalesce(NJI.FDRCID,'X') as RECID
            From TEMP_NOJALUR_IDM NJI, tbMaster_Lokasi
            Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                And LKS_PRDCD = PLUKARTON
                And LKS_KodeRak Not Like 'D%'
                And REQ_ID = '" . $ip . "'
                And FDKCAB = '" . $kodeToko . "'
                And fdnouo = '" . $noPB . "'
                And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                And LKS_TIPERAK NOT LIKE  'S%'
                And Not EXISTS(
                    Select grr_grouprak
                    from tbmaster_grouprak,tbmaster_lokasi lks2
                    where grr_koderak = lks2.lks_koderak
                    and grr_subrak = lks2.lks_kodesubrak
                    and LKS_KodeRak Like 'D%'
                    And LKS_TIPERAK NOT LIKE 'S%'
                    and lks_prdcd = plukarton
                )
            Order By NJI.PLUKarton
        ");

        return $data;
    }

    public function CetakAll_6($kodeToko,$ip,$noPB,$tglPB){
        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first();

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbMaster_TokoIGR')
            ->select('TKO_NamaOMI')
            ->where([
                'TKO_KodeIGR' => session('KODECABANG'),
                'TKO_KodeOMI' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->get();

        //! INSERT INTO PBIDM_JALURKERTAS
        //? f = "JALUR CETAK KERTAS"
        DB::select("
            INSERT INTO PBIDM_JALURKERTAS(
                PBK_KODETOKO,
                PBK_NOPB,
                PBK_TGLPB,
                PBK_NAMAGROUP,
                PBK_KODERAK,
                PBK_SUBRAK,
                PBK_TIPERAK,
                PBK_PLU,
                PBK_NOURUT,
                PBK_DESKRIPSI,
                PBK_TAG,
                PBK_QTY,
                PBK_UNIT,
                PBK_FRAC,
                PBK_STOK,
                PBK_CREATE_BY,
                PBK_CREATE_DT
            )
            Select DISTINCT '" . $kodeToko . "' as KODETOKO,
                '" . $noPB . "' as NoPB,
                TO_DATE('" . $tglPB . "','DD-MM-YYYY') as TglPB,
                GRR_GroupRak as NamaGroup,
                PLUKARTON as PLU,
                LKS_KodeRak as KodeRak,
                LKS_KodeSubRak as Subrak,
                LKS_TipeRak as TipeRak,
                LKS_NoUrut as NoUrut,
                DESK,
                PRD_KodeTag,
                QTYK as ORDER,
                UNITKECIL,
                FRACKECIL,
                STOK,
                '" . session('userid') . "',
                CURRENT_DATE
            From TEMP_JALURKERTAS_IDM, tbMaster_Prodmast
            Where REQ_ID = '" . $ip . "'
            And FDKCAB = '" . $kodeToko . "'
            And FDNOUO = '" . $noPB . "'
            And FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            And PRD_PRDCD = PLUKARTON
        ");

        $data['data'] = DB::select("
            Select GRR_GroupRak as NamaGroup,
                PLUKARTON as PLU,
                LKS_KodeRak as KodeRak,
                LKS_KodeSubRak as Subrak,
                LKS_TipeRak as TipeRak,
                LKS_NoUrut as NoUrut,
                DESK,
                PRD_KodeTag,
                QTYK as ORDER,
                UNITKECIL ||' /'|| FRACKECIL as UNIT,
                STOK
            From TEMP_JALURKERTAS_IDM, tbMaster_Prodmast
            Where REQ_ID = '" . $ip . "'
                And FDKCAB = '" . $kodeToko . "'
                And FDNOUO = '" . $noPB . "'
                And FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                And PRD_PRDCD = PLUKARTON
            Order By coalesce(GRR_GROUPRAK,'0'),LKS_KodeRak,LKS_KodeSubRak,LKS_TipeRak,LKS_NoUrut
        ");

        return $data;
    }

    public function prosesPBIDM(){


        DB::beginTransaction();
        try{
            $ip = $this->getIP();
            $noPB = 'TZ4Z133';
            $kodeToko = 'TZ4Z';
            $tglPB = '10-10-2023';

            $namaFile = 'PBATZ4Z.DBF';
            $fullPathFile = 'full-path/PBATZ4Z.DBF';

            $chkIDMBacaProdcrm = true;

            //DEFAULT VARIABLE
            $CounterKarton = 0;
            $CounterKecil = 0;
            $AdaKarton = False;
            $AdaKecil = False;

            //! DEL TEMP_CETAKPB_TOLAKAN_IDM
            // DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM WHERE req_id = '" & IP & "' "

            DB::table('temp_cetakpb_tolakan_idm')
                ->where('req_id', $ip)
                ->delete();

            // sb.AppendLine("Select TKO_KodeCustomer ")
            // sb.AppendLine("  From tbMaster_tokoIGR ")
            // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
            // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

            $data = DB::table('tbmaster_tokoigr')
                ->select('tko_kodecustomer')
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first();

            // if(empty){
            //     ("Kode Toko " & KodeToko & " Tidak Terdaftar Di TbMaster_TokoIGR
            // }

            if(empty($data)){

                DB::rollback();

                $message = "Kode Toko $kodeToko Tidak Terdaftar Di TbMaster_TokoIGR";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            $KodeMember = $data->tko_kodecustomer;

            //! GET -> KodeSBU
            // sb.AppendLine("Select TKO_KodeSBU ")
            // sb.AppendLine("  From tbMaster_tokoIGR ")
            // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
            // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

            $KodeSBU = DB::table('tbmaster_tokoigr')
                ->select('tko_kodesbu')
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first()->tko_kodesbu;

            //! GET -> PersenMargin
            // sb.AppendLine("Select coalesce(tko_persenmargin::numeric,3) / 100 ")
            // sb.AppendLine("  From tbMaster_TokoIGR ")
            // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
            // sb.AppendLine("   And COALESCE(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

            $PersenMargin = DB::table('tbmaster_tokoigr')
                ->selectRaw("coalesce(tko_persenmargin::numeric,3) / 100 as tko_persenmargin")
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first()->tko_persenmargin;

            //! GET -> jum
            // sb.AppendLine("Select COALESCE(count(1),0) ")
            // sb.AppendLine("  From TBTR_HEADER_POT ")
            // sb.AppendLine(" Where HDP_KodeIGR='" & KDIGR & "' ")
            // sb.AppendLine("   AND HDP_KodeToko = '" & KodeToko & "' ")
            // sb.AppendLine("   AND HDP_NoPB = '" & noPB & "' ")
            // sb.AppendLine("    AND to_char(HDP_TGLPB,'YYYY') = '" & Strings.Right(tglPB, 4) & "' ")

            $data = DB::table('tbtr_header_pot')
                    ->where([
                        'hdp_kodeigr' => session('KODECABANG'),
                        'hdp_kodetoko' => $kodeToko,
                        'hdp_nopb' => $noPB,
                    ])
                    ->whereYear('hdp_tglpb', Carbon::parse($tglPB)->format('Y'))
                    ->count();

            // if(jum > 0){
            //     PB Dengan No = " & noPB & ", KodeTOKO = " & KodeToko & " Sudah Pernah Diproses !
            // }

            if($data > 0){

                DB::rollback();

                $message = "PB Dengan No = $noPB, KodeTOKO = $kodeToko Sudah Pernah Diproses !";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            //! GET -> kodeDCIDM
            // $this->getKodeDC(KodeToko)

            $kodeDCIDM = $this->getKodeDC($kodeToko);

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
                ->select(
                    'cpp_nopb',
                    'cpp_tglpb',
                    'cpp_pluidm',
                    'cpp_qty',
                    'cpp_kodetoko',
                )
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_kodetoko' => $kodeToko,
                    'cpp_nopb' => $noPB,
                ])
                ->whereRaw("cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')");

            if($chkIDMBacaProdcrm){
                $data = $data->whereRaw("
                    not exists
                    (
                    Select KAT_PluIGR
                        From KONVERSI_ATK
                        WHERE KAT_PLUIDM = CPP_PLUIDM
                        AND EXISTS (
                            SELECT st_prdcd
                            FROM tbmaster_stock
                            WHERE st_prdcd = kat_pluigr
                            AND st_lokasi = '01'
                        )
                    )
                ");
            }else{

                $subquery = "
                    SELECT idm_pluidm
                    FROM tbtemp_pluidm
                    WHERE idm_pluidm = cpp_pluidm
                ";

                if($kodeDCIDM <> ""){
                    $subquery .= "AND idm_kdidm = '" & $kodeDCIDM & "'";
                }

                $data = $data->whereRaw("
                    not exists(
                        $subquery
                    )
                ");
            }

            $data = $data->get();

            foreach($data as $item){
                DB::table('temp_cetakpb_tolakan_idm')
                    ->insert([
                        'komi' => $KodeMember,
                        'tgl' => Carbon::now(),
                        'nodok' => $item->cpp_nopb,
                        'tgldok' => $item->cpp_tglpb,
                        'plu' => $item->cpp_pluidm,
                        'pluigr' => null,
                        'keta' => $chkIDMBacaProdcrm ? 'PLU TIDAK TERDAFTAR DI TBMASTER_PRODCRM' : 'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM',
                        'tag' => null,
                        'descr' => null,
                        'qtyo' => $item->cpp_qty,
                        'gross' => null,
                        'kcab' => $item->cpp_kodetoko,
                        'kodeigr' => session('KODECABANG'),
                        'req_id' => $ip,
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
                ->select(
                    'cpp_nopb',
                    'cpp_tglpb',
                    'cpp_pluidm',
                    'cpp_qty',
                    'cpp_kodetoko',
                )
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_kodetoko' => $kodeToko,
                    'cpp_nopb' => $noPB,
                ])
                ->whereRaw("cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')");

            if($chkIDMBacaProdcrm){
                $data = $data->whereRaw("
                    not exists
                    (
                        Select kat_pluigr
                        From konversi_atk
                        WHERE kat_pluidm = cpp_pluidm
                        AND kat_pluigr IS NULL
                    )
                ");
            }else{

                $subquery = "
                    SELECT idm_pluidm
                    FROM tbtemp_pluidm
                    WHERE idm_pluidm = cpp_pluidm
                    AND idm_pluigr IS NULL
                ";

                if($kodeDCIDM <> ""){
                    $subquery .= "AND idm_kdidm = '" & $kodeDCIDM & "'";
                }

                $data = $data->whereRaw("
                    not exists(
                        $subquery
                    )
                ");
            }

            $data = $data->get();


            foreach($data as $item){
                DB::table('temp_cetakpb_tolakan_idm')
                    ->insert([
                        'komi' => $KodeMember,
                        'tgl' => Carbon::now(),
                        'nodok' => $item->cpp_nopb,
                        'tgldok' => $item->cpp_tglpb,
                        'plu' => $item->cpp_pluidm,
                        'pluigr' => null,
                        'keta' => 'PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR',
                        'tag' => null,
                        'descr' => null,
                        'qtyo' => $item->cpp_qty,
                        'gross' => null,
                        'kcab' => $item->cpp_kodetoko,
                        'kodeigr' => session('KODECABANG'),
                        'req_id' => $ip,
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

            $query = "INSERT Into temp_cetakpb_tolakan_idm ";
            $query .= "( ";
            $query .= "   komi, ";
            $query .= "   tgl, ";
            $query .= "   nodok, ";
            $query .= "   tgldok, ";
            $query .= "   plu, ";
            $query .= "   pluigr, ";
            $query .= "   keta, ";
            $query .= "   tag, ";
            $query .= "   descr, ";
            $query .= "   qtyo, ";
            $query .= "   gross, ";
            $query .= "   kcab, ";
            $query .= "   kodeigr, ";
            $query .= "   req_id ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      cpp_nopb, ";
            $query .= "	      cpp_tglpb, ";
            $query .= "	      cpp_pluidm, ";

            if($chkIDMBacaProdcrm){
                $query .= "	      kat_pluigr, ";
                $query .= "	      'PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST', ";
            }else{
                $query .= "	      idm_pluigr, ";
                $query .= "	      'PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST', ";
            }

            $query .= "	      null, ";
            $query .= "	      null, ";
            $query .= "	      cpp_qty, ";
            $query .= "	      null, ";
            $query .= "	      cpp_kodetoko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM csv_pb_pot, konversi_atk ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	  FROM temp_cetakpb_tolakan_idm ";
                $query .= "	 WHERE komi = '" . $KodeMember . "' ";
                $query .= "	   AND req_id = '" . $ip . "'		  ";
                $query .= "	   AND nodok = '" . $noPB . "' ";
                $query .= "	   AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "	   AND plu = cpp_pluidm ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT prd_prdcd  ";
                $query .= "        FROM tbmaster_prodmast ";
                $query .= "       Where prd_prdcd = kat_pluigr ";
                $query .= "         And prd_kodeigr = '" . session('KODECABANG') . "'  ";
                $query .= "   )    ";
                $query .= "   AND cpp_pluidm = kat_pluidm ";
                $query .= "   AND cpp_pluigr = kat_pluigr ";

                //PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST
                DB::select($query);
            }else{
                $query .= "	 FROM csv_pb_pot,tbtemp_pluidm  ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	  FROM temp_cetakpb_tolakan_idm ";
                $query .= "	 WHERE komi = '" . $KodeMember . "' ";
                $query .= "	   AND req_id = '" . $ip . "'		  ";
                $query .= "		 AND nodok = '" . $noPB . "' ";
                $query .= "		 AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		 AND plu = cpp_pluidm ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT prd_prdcd  ";
                $query .= "        FROM tbmaster_prodmast ";
                $query .= "       Where prd_prdcd = idm_pluigr ";
                $query .= "         And prd_kodeigr = '" . session('KODECABANG') . "'  ";
                $query .= "   )    ";
                $query .= "   AND cpp_pluidm = idm_pluidm ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND idm_kdidm = '" . $kodeDCIDM . "' ";
                }

                //PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST
                DB::select($query);
            }

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

            if($chkIDMBacaProdcrm){
                $subquery1 = 'KAT_PLUIGR';
                $subquery2 = "
                    FROM csv_pb_pot, TBMASTER_PRODMAST,KONVERSI_ATK
                    WHERE CPP_IP = '" . $ip . "'
                    AND CPP_KodeToko = '" . $kodeToko . "'
                    AND CPP_NoPB = '" . $noPB . "'
                    AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                    AND NOT EXISTS
                    (
                    SELECT PLUIGR
                            FROM TEMP_CETAKPB_TOLAKAN_IDM
                        WHERE KOMI = '" . $KodeMember . "'
                            AND REQ_ID = '" . $ip . "'
                            AND NODOK = '" . $noPB . "'
                            AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')
                            AND PLU = CPP_PLUIDM
                    )
                    AND NOT EXISTS
                    (
                        SELECT ST_AvgCost
                            FROM tbMaster_Stock
                        Where ST_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%'
                            And ST_Lokasi = '01'
                            And ST_KodeIGR = '" . session('KODECABANG') . "'
                            And ST_AvgCost IS NOT NULL
                    )
                    AND CPP_PLUIDM = KAT_PLUIDM
                    AND PRD_PRDCD = KAT_PLUIGR
                    AND CPP_PLUIGR = KAT_PLUIGR
                ";
            }else{
                $subquery1 = 'IDM_PLUIGR';
                $subquery2 = "
                    FROM csv_pb_pot, TBMASTER_PRODMAST,TBTEMP_PLUIDM
                    WHERE CPP_IP = '" . $ip . "'
                    AND CPP_KodeToko = '" . $kodeToko . "'
                    AND CPP_NoPB = '" . $noPB . "'
                    AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                    AND NOT EXISTS
                    (
                    SELECT PLUIGR
                            FROM TEMP_CETAKPB_TOLAKAN_IDM
                        WHERE KOMI = '" . $KodeMember . "'
                            AND REQ_ID = '" . $ip . "'
                            AND NODOK = '" . $noPB . "'
                            AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')
                            AND PLU = CPP_PLUIDM
                    )
                    AND NOT EXISTS
                    (
                        SELECT ST_AvgCost
                            FROM tbMaster_Stock
                        Where ST_PRDCD Like SUBSTR(IDM_PLUIGR,1,6)||'%'
                            And ST_Lokasi = '01'
                            And ST_KodeIGR = '" . session('KODECABANG') . "'
                            And ST_AvgCost IS NOT NULL
                    )
                    AND CPP_PLUIDM = IDM_PLUIDM
                    AND PRD_PRDCD = IDM_PLUIGR
                ";

                if($kodeDCIDM <> ""){
                    $subquery2 .= "AND IDM_KDIDM = '" . $kodeDCIDM . "'";
                }
            }

            DB::select("
                INSERT Into TEMP_CETAKPB_TOLAKAN_IDM
                (
                    KOMI,
                    TGL,
                    NODOK,
                    TGLDOK,
                    PLU,
                    PLUIGR,
                    KETA,
                    TAG,
                    DESCR,
                    QTYO,
                    GROSS,
                    KCAB,
                    KODEIGR,
                    REQ_ID
                )
                Select '" . $KodeMember . "',
                    CURRENT_DATE,
                    CPP_NoPB,
                    CPP_TglPB,
                    CPP_PLUIDM,
                    $subquery1,
                    'AVG.COST IS NULL',
                    PRD_KodeTag,
                    SUBSTR(PRD_DESKRIPSIPANJANG,1,60),
                    CPP_QTY,
                    null,
                    CPP_KodeToko,
                    '" . session('KODECABANG') . "',
                    '" . $ip . "'
                    $subquery2
            ");

            DB::select($query);

            //DONE

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

            $query = '';
            $query .= "INSERT Into temp_cetakpb_tolakan_idm ";
            $query .= "( ";
            $query .= "   komi, ";
            $query .= "   tgl, ";
            $query .= "   nodok, ";
            $query .= "   tgldok, ";
            $query .= "   plu, ";
            $query .= "   pluigr, ";
            $query .= "   keta, ";
            $query .= "   tag, ";
            $query .= "   descr, ";
            $query .= "   qtyo, ";
            $query .= "   gross, ";
            $query .= "   kcab, ";
            $query .= "   kodeigr, ";
            $query .= "   req_id ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      cpp_nopb, ";
            $query .= "	      cpp_tglpb, ";
            $query .= "	      cpp_pluidm, ";

            if($chkIDMBacaProdcrm){
                $query .= "	      kat_pluigr, ";
            }else{
                $query .= "	      idm_pluigr, ";
            }

            $query .= "	      'avg.cost <= 100', ";
            $query .= "	      prd_kodetag, ";
            $query .= "	      SUBSTR(prd_deskripsipanjang,1,60), ";
            $query .= "	      cpp_qty, ";
            $query .= "	      null, ";
            $query .= "	      cpp_kodetoko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM csv_pb_pot, tbmaster_prodmast,konversi_atk ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	    FROM temp_cetakpb_tolakan_idm ";
                $query .= "	   WHERE komi = '" . $KodeMember . "' ";
                $query .= "	     AND req_id = '" . $ip . "'		  ";
                $query .= "		 AND nodok = '" . $noPB . "' ";
                $query .= "		 AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		 AND plu = cpp_pluidm ";
                $query .= "   )    ";
                $query .= "   AND EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT st_avgcost  ";
                $query .= "        FROM tbmaster_stock  ";
                $query .= "       Where st_prdcd Like SUBSTR(kat_pluigr,1,6)||'%' ";
                $query .= "         And st_lokasi = '01'  ";
                $query .= "         And st_kodeigr = '" . session('KODECABANG') . "'  ";
                $query .= "         And COALESCE(st_avgcost,0) <= 100 ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "    SELECT phi_prdcd ";
                $query .= "      FROM plu_hadiah_idm ";
                $query .= "     WHERE phi_prdcd Like SUBSTR(kat_pluigr,1,6)||'%' ";
                $query .= "   )    ";
                $query .= "   AND cpp_pluidm = kat_pluidm ";
                $query .= "   AND prd_prdcd = kat_pluigr ";
                $query .= "   AND cpp_pluigr = kat_pluigr ";
            }else{
                $query .= "	 FROM csv_pb_pot, tbmaster_prodmast,tbtemp_pluidm  ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	  FROM temp_cetakpb_tolakan_idm ";
                $query .= "	 WHERE komi = '" . $KodeMember . "' ";
                $query .= "	     AND req_id = '" . $ip . "'		  ";
                $query .= "		 AND nodok = '" . $noPB . "' ";
                $query .= "		 AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		 AND plu = cpp_pluidm ";
                $query .= "   )    ";
                $query .= "   AND EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT st_avgcost  ";
                $query .= "        FROM tbmaster_stock  ";
                $query .= "       Where st_prdcd Like SUBSTR(idm_pluigr,1,6)||'%' ";
                $query .= "         And st_lokasi = '01'  ";
                $query .= "         And st_kodeigr = '" . session('KODECABANG') . "'  ";
                $query .= "         And COALESCE(st_avgcost,0) <= 100 ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "    SELECT phi_prdcd ";
                $query .= "      FROM plu_hadiah_idm ";
                $query .= "     WHERE phi_prdcd Like SUBSTR(kat_pluigr,1,6)||'%' ";
                $query .= "   )    ";
                $query .= "   AND cpp_pluidm = idm_pluidm ";
                $query .= "   AND prd_prdcd = idm_pluigr ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND idm_kdidm = '" . $kodeDCIDM . "' ";
                }
            }

            DB::select($query);

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

            $query = '';
            $query .= "INSERT Into temp_cetakpb_tolakan_idm ";
            $query .= "( ";
            $query .= "   komi, ";
            $query .= "   tgl, ";
            $query .= "   nodok, ";
            $query .= "   tgldok, ";
            $query .= "   plu, ";
            $query .= "   pluigr, ";
            $query .= "   keta, ";
            $query .= "   tag, ";
            $query .= "   descr, ";
            $query .= "   qtyo, ";
            $query .= "   gross, ";
            $query .= "   kcab, ";
            $query .= "   kodeigr, ";
            $query .= "   req_id ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      cpp_nopb, ";
            $query .= "	      cpp_tglpb, ";
            $query .= "	      cpp_pluidm, ";

            if($chkIDMBacaProdcrm){
                $query .= "	      kat_pluigr, ";
            }else{
                $query .= "	      idm_pluigr, ";
            }

            $query .= "	      'STOCK EKONOMIS POT TIDAK MENCUKUPI', ";
            $query .= "	      prd_kodetag, ";
            $query .= "	      SUBSTR(prd_deskripsipanjang,1,60), ";
            $query .= "	      cpp_qty, ";
            $query .= "	      null, ";
            $query .= "	      cpp_kodetoko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM csv_pb_pot, tbmaster_prodmast,konversi_atk ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	    FROM temp_cetakpb_tolakan_idm ";
                $query .= "	   WHERE komi = '" . $KodeMember . "' ";
                $query .= "	     AND req_id = '" . $ip . "'		  ";
                $query .= "		  AND nodok = '" . $noPB . "' ";
                $query .= "		  AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		  AND plu = cpp_pluidm ";
                $query .= "   )    ";
                $query .= "   AND cpp_tolakan_sep > 0 ";
                $query .= "   AND cpp_stock <= 0 ";
                $query .= "   AND cpp_pluidm = kat_pluidm ";
                $query .= "   AND prd_prdcd = kat_pluigr ";
                $query .= "   AND cpp_pluigr = kat_pluigr ";
            }else{
                $query .= "	 FROM csv_pb_pot, tbmaster_prodmast,tbtemp_pluidm  ";
                $query .= " WHERE cpp_ip = '" . $ip . "' ";
                $query .= "   AND cpp_kodetoko = '" . $kodeToko . "' ";
                $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT pluigr  ";
                $query .= "	    FROM temp_cetakpb_tolakan_idm ";
                $query .= "	   WHERE komi = '" . $KodeMember . "' ";
                $query .= "	     AND req_id = '" . $ip . "'		  ";
                $query .= "		  AND nodok = '" . $noPB . "' ";
                $query .= "		  AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		  AND plu = cpp_pluidm ";
                $query .= "   ) ";
                $query .= "   AND cpp_tolakan_sep > 0 ";
                $query .= "   AND cpp_stock <= 0 ";
                $query .= "   AND cpp_pluidm = idm_pluidm ";
                $query .= "   AND prd_prdcd = idm_pluigr ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND idm_kdidm = '" . $kodeDCIDM . "' ";
                }
            }

            DB::select($query);

            //! GET -> jum
            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From information_schema.columns ")
            // sb.AppendLine(" Where  ")

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_CETAKPB_TOLAKAN_IDM2'")
                ->count();

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

            if($data == 0){
                //!CREATE TABLE TEMP_CETAKPB_TOLAKAN_IDM2-PRODMAST-NXQ
                $query = '';
                $query .= "CREATE TABLE temp_cetakpb_tolakan_idm2 ";
                $query .= "AS ";
                $query .= "SELECT komi, ";
                $query .= "       tgl, ";
                $query .= "       nodok, ";
                $query .= "       tgldok, ";
                $query .= "       plu, ";
                $query .= "       pluigr, ";
                $query .= "       keta, ";
                $query .= "       prd_kodetag AS tag, ";
                $query .= "       descr, ";
                $query .= "       qtyo, ";
                $query .= "       kcab, ";
                $query .= "       kodeigr, ";
                $query .= "       req_id ";
                $query .= "FROM ";
                $query .= "( ";
                $query .= "Select '" . $KodeMember . "' as komi,  ";
                $query .= "       CURRENT_DATE as tgl,   ";
                $query .= "	    cpp_nopb as nodok,  ";
                $query .= "	    cpp_tglpb as tgldok,  ";
                $query .= "	    cpp_pluidm as plu,  ";

                if($chkIDMBacaProdcrm){
                    $query .= "	      kat_pluigr as PLUiGR,  ";
                }else{
                    $query .= "	      idm_pluigr as pluigr,  ";
                }

                $query .= "	      'PRODMAST IGR DISCONTINUE Tag:NXQ' as keta, ";
                $query .= "	      SUBSTR(prd_deskripsipanjang,1,60) as descr,  ";
                $query .= "	      cpp_qty as qtyo, ";
                $query .= "	      cpp_kodetoko as kcab, ";
                $query .= "	      '" . session('KODECABANG') . "' as kodeigr, ";
                $query .= "	      '" . $ip . "' as req_id, ";
                $query .= "         Min(prd_prdcd) AS plukecil  ";
                if($chkIDMBacaProdcrm){
                    $query .= "  FROM csv_pb_pot, tbmaster_prodmast,konversi_atk ";
                    $query .= " WHERE cpp_ip = '" . $ip . "' ";
                    $query .= "   AND cpp_kodetoko = '" . $kodeToko . "'  ";
                    $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                    $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                    $query .= "   AND NOT EXISTS  ";
                    $query .= "   (  ";
                    $query .= "   SELECT pluigr ";
                    $query .= "	    FROM temp_cetakpb_tolakan_idm  ";
                    $query .= "	   WHERE komi = '" . $KodeMember . "'  ";
                    $query .= "	     AND req_id = '" . $ip . "' ";
                    $query .= "		 AND nodok = '" . $noPB . "'  ";
                    $query .= "		 AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                    $query .= "		 AND plu = cpp_pluidm ";
                    $query .= "   )     ";
                    $query .= "   AND cpp_pluidm = kat_pluidm  ";
                    $query .= "   AND prd_prdcd like substr(kat_pluigr,1,6)||'%' ";
                    $query .= "   AND cpp_pluigr = kat_pluigr ";
                    $query .= "   AND SubStr(prd_prdcd,-1,1) <> '0'    ";
                    $query .= " GROUP BY cpp_nopb,  ";
                    $query .= "	        cpp_tglpb,  ";
                    $query .= "	        cpp_pluidm,  ";
                    $query .= "	        kat_pluigr, ";
                    $query .= "	        SUBSTR(prd_deskripsipanjang,1,60),  ";
                    $query .= "	        cpp_qty, ";
                    $query .= "	        cpp_kodetoko ";
                    $query .= ") X,tbmaster_prodmast ";
                    $query .= "WHERE prd_prdcd = PLUKECIL ";
                    $query .= "  AND prd_kodetag IN ('N','X','Q') ";
                }else{
                    $query .= "	 FROM csv_pb_pot, tbmaster_prodmast,tbtemp_pluidm   ";
                    $query .= " WHERE cpp_ip = '" . $ip . "'  ";
                    $query .= "   AND cpp_kodetoko = '" . $kodeToko . "'  ";
                    $query .= "   AND cpp_nopb = '" . $noPB . "' ";
                    $query .= "   AND cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                    $query .= "   AND NOT EXISTS  ";
                    $query .= "   (  ";
                    $query .= "   SELECT pluigr ";
                    $query .= "	    FROM temp_cetakpb_tolakan_idm  ";
                    $query .= "	   WHERE komi = '" . $KodeMember . "'  ";
                    $query .= "	     AND req_id = '" . $ip . "' ";
                    $query .= "		 AND nodok = '" . $noPB . "'  ";
                    $query .= "		 AND tgldok = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                    $query .= "		 AND plu = cpp_pluidm ";
                    $query .= "   ) ";
                    $query .= "   AND cpp_pluidm = idm_pluidm ";

                    if($kodeDCIDM <> ""){
                        $query .= "   AND idm_kdidm = '" . $kodeDCIDM . "' ";
                    }

                    $query .= "   AND prd_prdcd like SubStr(idm_pluigr,1,6)||'%'  ";
                    $query .= "   AND SubStr(prd_prdcd,-1,1) <> '0'    ";
                    $query .= " GROUP BY cpp_nopb,  ";
                    $query .= "	       cpp_tglpb,  ";
                    $query .= "	       cpp_pluidm,  ";
                    $query .= "	       idm_pluigr, ";
                    $query .= "	       SUBSTR(prd_deskripsipanjang,1,60),  ";
                    $query .= "	       cpp_qty, ";
                    $query .= "	       cpp_kodetoko ";
                    $query .= ") X,tbmaster_prodmast ";
                    $query .= "WHERE prd_prdcd = PLUKECIL ";
                    $query .= "  AND prd_kodetag IN ('N','X','Q') ";
                }

                DB::select($query);

            }else{
                //! DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM2
                DB::table('temp_cetakpb_tolakan_idm2')
                    ->where('req_id', $ip)
                    ->delete();
            }

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

            $query = '';
            $query .= "INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 ";
            $query .= "SELECT KOMI, ";
            $query .= "       TGL, ";
            $query .= "       NODOK, ";
            $query .= "       TGLDOK, ";
            $query .= "       PLU, ";
            $query .= "       PLUIGR, ";
            $query .= "       KETA, ";
            $query .= "       PRD_KODETAG AS TAG, ";
            $query .= "       DESCR, ";
            $query .= "       QTYO, ";
            $query .= "       KCAB, ";
            $query .= "       KODEIGR, ";
            $query .= "       REQ_ID ";
            $query .= "FROM ";
            $query .= "( ";
            $query .= "Select '" . $KodeMember . "' as KOMI,  ";
            $query .= "       CURRENT_DATE as TGL,   ";
            $query .= "	      CPP_NoPB as NODOK,  ";
            $query .= "	      CPP_TGLPB as TGLDOK,  ";
            $query .= "	      CPP_PLUIDM as PLU,  ";
            if($chkIDMBacaProdcrm){
                $query .= "	      KAT_PLUIGR as PLUIGR,  ";
            }else{
                $query .= "	      IDM_PLUIGR as PLUIGR,  ";
            }

            $query .= "	      'PRODMAST IGR DISCONTINUE Tag:NXQ' as KETA, ";
            $query .= "	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60) as DESCR,  ";
            $query .= "	      CPP_QTY as QTYO, ";
            $query .= "	      CPP_KodeToko as KCAB, ";
            $query .= "	      '" . session('KODECABANG') . "' as KODEIGR,  ";
            $query .= "	      '" . $ip . "' as REQ_ID, ";
            $query .= "        Min(PRD_PRDCD) AS PLUKECIL  ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM CSV_PB_POT, TBMASTER_PRODMAST,KONVERSI_ATK ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS  ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	     AND REQ_ID = '" . $ip . "' ";
                $query .= "		 AND NODOK = '" . $noPB . "'  ";
                $query .= "		 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		 AND PLU = CPP_PLUIDM  ";
                $query .= "   )     ";
                $query .= "   AND NOT EXISTS  ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM2  ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	     AND REQ_ID = '" . $ip . "' ";
                $query .= "		 AND NODOK = '" . $noPB . "'  ";
                $query .= "		 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		 AND PLU = CPP_PLUIDM  ";
                $query .= "   )     ";
                $query .= "   AND CPP_PLUIDM = KAT_PLUIDM  ";
                $query .= "   AND PRD_PRDCD like SubStr(KAT_PLUIGR,1,6)||'%'  ";
                $query .= "   AND CPP_PLUIGR = KAT_PLUIGR ";
                $query .= "   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ";
                $query .= " GROUP BY CPP_NOPB,  ";
                $query .= "	        CPP_TGLPB,  ";
                $query .= "	        CPP_PLUIDM,  ";
                $query .= "	        KAT_PLUIGR,	                  	       ";
                $query .= "	        SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ";
                $query .= "	        CPP_QTY,  ";
                $query .= "	        CPP_KodeToko ";
                $query .= ") X,tbMaster_Prodmast ";
                $query .= "WHERE PRD_PRDCD = PLUKECIL ";
                $query .= "  AND PRD_KodeTag IN ('N','X','Q') ";
            }else{
                $query .= "	 FROM CSV_PB_POT, TBMASTER_PRODMAST,TBTEMP_PLUIDM ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM  ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	   AND REQ_ID = '" . $ip . "' ";
                $query .= "		AND NODOK = '" . $noPB . "'  ";
                $query .= "		AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		AND PLU = CPP_PLUIDM ";
                $query .= "   )     ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM2  ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	   AND REQ_ID = '" . $ip . "' ";
                $query .= "		AND NODOK = '" . $noPB . "'  ";
                $query .= "		AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		AND PLU = CPP_PLUIDM ";
                $query .= "   )     ";
                $query .= "   AND CPP_PLUIDM = IDM_PLUIDM ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }

                $query .= "   AND PRD_PRDCD like SubStr(IDM_PLUIGR,1,6)||'%' ";
                $query .= "   AND SubStr(PRD_PRDCD,-1,1) <> '0' ";
                $query .= " GROUP BY CPP_NoPB,  ";
                $query .= "	       CPP_TglPB,  ";
                $query .= "	       CPP_PLUIDM,  ";
                $query .= "	       IDM_PLUIGR,	";
                $query .= "	       SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ";
                $query .= "	       CPP_QTY,  ";
                $query .= "	       CPP_KodeToko ";
                $query .= ") X,tbMaster_Prodmast ";
                $query .= "WHERE PRD_PRDCD = PLUKECIL ";
                $query .= "  AND PRD_KodeTag IN ('N','X','Q') ";
            }

            DB::select($query);

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

            $query = '';
            $query .= "INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 ";
            $query .= "SELECT KOMI, ";
            $query .= "       TGL, ";
            $query .= "       NODOK, ";
            $query .= "       TGLDOK, ";
            $query .= "       PLU, ";
            $query .= "       PLUIGR, ";
            $query .= "       KETA, ";
            $query .= "       PRD_KODETAG AS TAG, ";
            $query .= "       DESCR, ";
            $query .= "       QTYO, ";
            $query .= "       KCAB, ";
            $query .= "       KODEIGR, ";
            $query .= "       REQ_ID ";
            $query .= "FROM ";
            $query .= "( ";
            $query .= "Select '" . $KodeMember . "' as KOMI,  ";
            $query .= "       CURRENT_DATE as TGL,   ";
            $query .= "	      CPP_NoPB as NODOK,  ";
            $query .= "	      CPP_TGLPB as TGLDOK,  ";
            $query .= "	      CPP_PLUIDM as PLU,  ";

            if($chkIDMBacaProdcrm){
                $query .= "	      KAT_PLUIGR as PLUIGR,  ";
            }else{
                $query .= "	      IDM_PLUIGR as PLUIGR,  ";
            }

            $query .= "	      'PRODMAST IGR FLAG AKTIVASI:X' as KETA, ";
            $query .= "	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60) as DESCR,  ";
            $query .= "	      CPP_QTY as QTYO, ";
            $query .= "	      CPP_KodeToko as KCAB, ";
            $query .= "	      '" . session('KODECABANG') . "' as KODEIGR,  ";
            $query .= "	      '" . $ip . "' as REQ_ID, ";
            $query .= "        Min(PRD_PRDCD) AS PLUKECIL  ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM CSV_PB_POT, TBMASTER_PRODMAST,KONVERSI_ATK ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS  ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM  ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	     AND REQ_ID = '" . $ip . "' ";
                $query .= "		 AND NODOK = '" . $noPB . "'  ";
                $query .= "		 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		 AND PLU = CPP_PLUIDM  ";
                $query .= "   )     ";
                $query .= "   AND NOT EXISTS  ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM2  ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	     AND REQ_ID = '" . $ip . "' ";
                $query .= "		 AND NODOK = '" . $noPB . "'  ";
                $query .= "		 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		 AND PLU = CPP_PLUIDM  ";
                $query .= "   )     ";
                $query .= "   AND CPP_PLUIDM = KAT_PLUIDM  ";
                $query .= "   AND PRD_PRDCD like SubStr(KAT_PLUIGR,1,6)||'%'  ";
                $query .= "   AND CPP_PLUIGR = KAT_PLUIGR ";
                $query .= "   AND SubStr(PRD_PRDCD,-1,1) <> '0'    ";
                $query .= " GROUP BY CPP_NOPB,  ";
                $query .= "	        CPP_TGLPB,  ";
                $query .= "	        CPP_PLUIDM,  ";
                $query .= "	        KAT_PLUIGR,	                  	       ";
                $query .= "	        SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ";
                $query .= "	        CPP_QTY,  ";
                $query .= "	        CPP_KodeToko ";
                $query .= ") X,tbMaster_Prodmast ";
                $query .= ", TBMASTER_FLAGAKT ";
                $query .= "WHERE PRD_PRDCD = PLUKECIL ";
                $query .= "  AND prd_flag_aktivasi IN ('X') ";
                $query .= "  AND prd_flag_aktivasi = AKT_KODEFLAG ";
            }else{
                $query .= "	 FROM CSV_PB_POT, TBMASTER_PRODMAST,TBTEMP_PLUIDM ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM  ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	   AND REQ_ID = '" . $ip . "' ";
                $query .= "		AND NODOK = '" . $noPB . "'  ";
                $query .= "		AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		AND PLU = CPP_PLUIDM ";
                $query .= "   )     ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   (  ";
                $query .= "   SELECT PLUIGR ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM2  ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "	   AND REQ_ID = '" . $ip . "' ";
                $query .= "		AND NODOK = '" . $noPB . "'  ";
                $query .= "		AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "		AND PLU = CPP_PLUIDM ";
                $query .= "   )     ";
                $query .= "   AND CPP_PLUIDM = IDM_PLUIDM ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }

                $query .= "   AND PRD_PRDCD like SubStr(IDM_PLUIGR,1,6)||'%' ";
                $query .= "   AND SubStr(PRD_PRDCD,-1,1) <> '0' ";
                $query .= " GROUP BY CPP_NoPB,  ";
                $query .= "	       CPP_TglPB,  ";
                $query .= "	       CPP_PLUIDM,  ";
                $query .= "	       IDM_PLUIGR,	";
                $query .= "	       SUBSTR(PRD_DESKRIPSIPANJANG,1,60),  ";
                $query .= "	       CPP_QTY,  ";
                $query .= "	       CPP_KodeToko ";
                $query .= ") X,tbMaster_Prodmast ";
                $query .= ", TBMASTER_FLAGAKT ";
                $query .= "WHERE PRD_PRDCD = PLUKECIL ";
                $query .= "  AND prd_flag_aktivasi IN ('X') ";
                $query .= "  AND prd_flag_aktivasi = AKT_KODEFLAG ";
            }

            DB::select($query);

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

            DB::select("
            INSERT Into TEMP_CETAKPB_TOLAKAN_IDM(
                KOMI,
                TGL,
                NODOK,
                TGLDOK,
                PLU,
                PLUIGR,
                KETA,
                TAG,
                DESCR,
                QTYO,
                GROSS,
                KCAB,
                KODEIGR,
                REQ_ID
            )
            Select KOMI,
                TGL,
                NODOK,
                TGLDOK,
                PLU,
                PLUIGR,
                KETA,
                TAG,
                DESCR,
                QTYO,
                ST_AVGCOST * QTYO as GROSS,
                KCAB,
                KODEIGR,
                REQ_ID
            FROM TEMP_CETAKPB_TOLAKAN_IDM2 IDM2,tbMaster_Stock
            Where ST_PRDCD Like SUBSTR(PLUIGR,1,6)||'%'
                And ST_Lokasi = '01'
                And COALESCE(ST_RecordID,'0') <> '1'
                And REQ_ID = '" . $ip . "'
                AND KCAB = '" . $kodeToko . "'
                AND NODOK = '" . $noPB . "'
                AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')
                AND NOT EXISTS(
                    SELECT PLUIGR
                    FROM TEMP_CETAKPB_TOLAKAN_IDM IDM
                    WHERE KOMI = '" . $KodeMember . "'
                        AND REQ_ID = '" . $ip . "'
                        AND NODOK = '" . $noPB . "'
                        AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')
                        AND IDM.PLU = IDM2.PLU
                )
            ");

            //! '------------------------------------'
            //! '+ 03-07-2013 ISI TEMP_PB_IDMREADY2 +'
            //! '------------------------------------'

            //! DELETE FROM TEMP_PBIDM_READY2
            // sb.AppendLine("DELETE FROM TEMP_PBIDM_READY2 ")
            // sb.AppendLine(" WHERE REQ_ID = '" & IP & "' ")

            DB::table('temp_pbidm_ready2')
                ->where('req_id', $ip)
                ->delete();

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

            $query = '';
            $query .= "INSERT INTO TEMP_PBIDM_READY2 ";
            $query .= "( ";
            $query .= "      fdrcid, ";
            $query .= "      fdnouo, ";
            $query .= "      fdkode, ";
            $query .= "      fdqtyb, ";
            $query .= "      fdkcab, ";
            $query .= "      fdtgpb, ";
            $query .= "      fdksup, ";
            $query .= "      req_id, ";
            $query .= "      nama_file, ";
            $query .= "      prc_pluigr ";
            $query .= ") ";
            $query .= "Select NULL as FDRCID, ";
            $query .= "	    CPP_NOPB as FDNOUO, ";
            $query .= "	    CPP_PLUIDM as FDKODE, ";
            $query .= "	    CPP_QTY as FDQTYB, ";
            $query .= "	    CPP_KodeToko as  FDKCAB, ";
            $query .= "	    CPP_TglPB as FDTGPB, ";
            $query .= "	    NULL as  FDKSUP, ";
            $query .= "	    CPP_IP as REQ_ID, ";
            $query .= "	    CPP_FILENAME as  NAMA_FILE, ";
            if($chkIDMBacaProdcrm){
                $query .= "	      KAT_PLUIGR ";
                $query .= "   From csv_pb_pot A, KONVERSI_ATK ";
                $query .= "  Where CPP_IP = '" . $ip . "' ";
                $query .= "    AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "    AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "    AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "    AND NOT EXISTS ";
                $query .= "           (   ";
                $query .= "              SELECT PLUIGR    ";
                $query .= "                FROM TEMP_CETAKPB_TOLAKAN_IDM   ";
                $query .= "               WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "                 AND REQ_ID = '" . $ip . "' ";
                $query .= "                 AND NODOK = '" . $noPB . "'   ";
                $query .= "                 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "                 AND PLU = CPP_PLUIDM ";
                $query .= "           )  ";
                $query .= "    AND KAT_pluidm = CPP_PLUIDM ";
                $query .= "    AND CPP_PLUIGR = KAT_PLUIGR ";
                $query .= "    AND CPP_FLAG  IS NULL ";
            }else{
                $query .= "	       IDM_PLUIGR ";
                $query .= "   From csv_pb_pot A, TBTEMP_PLUIDM ";
                $query .= "  Where CPP_IP = '" . $ip . "' ";
                $query .= "    AND CPP_KodeTOKO = '" . $kodeToko . "' ";
                $query .= "    AND CPP_NOPB = '" . $noPB . "' ";
                $query .= "    AND CPP_TGLPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "    AND NOT EXISTS ";
                $query .= "           (   ";
                $query .= "              SELECT PLUIGR    ";
                $query .= "                FROM TEMP_CETAKPB_TOLAKAN_IDM   ";
                $query .= "               WHERE KOMI = '" . $KodeMember . "'  ";
                $query .= "                 AND REQ_ID = '" . $ip . "' ";
                $query .= "                 AND NODOK = '" . $noPB . "'   ";
                $query .= "                 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY')  ";
                $query .= "                 AND PLU = CPP_PLUIDM  ";
                $query .= "           )  ";
                $query .= "    AND IDM_pluidm = CPP_PLUIDM ";
                $query .= "    AND CPP_FLAG IS NULL ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }
            }

            DB::select($query);

            //GET -> jum
            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From information_schema.columns ")
            // sb.AppendLine(" Where upper(table_name) = 'TEMP_PBIDM_READY' ")

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_PBIDM_READY'")
                ->count();

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
        // }

            if($data == 0){
                //! CREATE TABLE TEMP_PBIDM_READY
                DB::select("
                    CREATE TABLE TEMP_PBIDM_READY
                    AS
                    Select E.*,ST_AvgCost as AVGCOST
                    From
                        (
                            Select D.*,
                                CASE WHEN FracKarton = 1 THEN 0 ELSE FDQTYB / FracKarton END as QTYB,
                                CASE WHEN FracKarton = 1 THEN FDQTYB ELSE MOD(FDQTYB,FracKarton) END as QTYK,
                                CASE WHEN
                                    CASE WHEN FracKarton = 1 THEN FDQTYB ELSE FDQTYB / FracKecil END < PRD_MinJual
                                THEN 'T'
                                ELSE '' END AS TolakMinJ
                            From
                            (
                                Select C.*,PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual
                                From
                                (
                                    Select B.*, CASE WHEN min(prd_prdcd) IS NULL THEN PluKarton ELSE min(prd_prdcd) END as PLUKecil--, PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual
                                    From
                                    (
                                        Select A.FDRCID, A.FDNOUO, A.FDKODE, MAX(A.FDQTYB) as FDQTYB, A.FDKCAB, A.FDTGPB, A.FDKSUP, A.REQ_ID, A.NAMA_FILE , prd_deskripsipanjang as DESK, prd_flagbkp1 as BKP, prd_prdcd as PluKarton,prd_unit as UnitKarton,prd_frac as FracKarton
                                        From temp_pbidm_ready2 A, tbmaster_prodmast
                                        Where REQ_ID = '" . $ip . "'
                                        AND FDKCAB = '" . $kodeToko . "'
                                        AND FDNOUO = '" . $noPB . "'
                                        AND FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                                        AND prd_prdcd = prc_pluigr
                                        GROUP By A.FDRCID,
                                                A.FDNOUO,
                                                A.FDKODE,
                                                A.FDTGPB,
                                                A.FDKCAB,
                                                A.FDKSUP,
                                                A.REQ_ID,
                                                A.NAMA_FILE,
                                                prd_deskripsipanjang,
                                                prd_flagbkp1,
                                                prd_prdcd,
                                                prd_unit,
                                                prd_frac
                                    ) B, tbMaster_Prodmast
                                    Where PRD_PRDCD <> SUBSTR(PLUKarton,1,6)||'0'
                                    And PRD_PRDCD Like SUBSTR(PLUKarton,1,6)||'%'
                                    AND COALESCE(prd_KodeTag,'A') NOT IN ('N','X','Q')
                                    Group By fdrcid,
                                        fdnouo,
                                        fdkode,
                                        fdqtyb,
                                        fdkcab,
                                        fdtgpb,
                                        fdksup,
                                        req_id,
                                        nama_file,
                                        Desk,
                                        PluKarton,
                                        UnitKarton,
                                        FracKarton,
                                        BKP
                                ) C, tbMaster_prodmast
                                Where PRD_PRDCD = PluKecil
                            )D
                        ) E, tbMaster_Stock
                    Where ST_PRDCD = PLUKARTON
                    And ST_Lokasi = '01'
                    And COALESCE(ST_RecordID,'0') <> '1'
                ");
            }else{
                //! DELETE FROM TEMP_PBIDM_READY
                // sb.AppendLine("DELETE FROM TEMP_PBIDM_READY ")
                // sb.AppendLine(" WHERE REQ_ID = '" & IP & "' ")

                DB::table('temp_pbidm_ready')
                    ->where('req_id', $ip)
                    ->delete();

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

                DB::select("
                INSERT INTO TEMP_PBIDM_READY
                (
                    fdrcid,
                    fdnouo,
                    fdkode,
                    fdqtyb,
                    fdkcab,
                    fdtgpb,
                    fdksup,
                    req_id,
                    nama_file,
                    desk,
                    bkp,
                    plukarton,
                    unitkarton,
                    frackarton,
                    plukecil,
                    unitkecil,
                    frackecil,
                    prd_minjual,
                    qtyb,
                    qtyk,
                    tolakminj,
                    avgcost
                )
                Select E.*,ST_AvgCost as AVGCOST
                From
                (
                    Select D.*,
                        CASE WHEN FracKarton = 1 THEN 0 ELSE FDQTYB / FracKarton END as QTYB,
                        CASE WHEN FracKarton = 1 THEN FDQTYB ELSE MOD(FDQTYB,FracKarton) END as QTYK,
                        CASE WHEN
                            CASE WHEN FracKarton = 1 THEN FDQTYB ELSE FDQTYB / FracKecil END < PRD_MinJual
                        THEN 'T'
                        ELSE '' END AS TolakMinJ
                    From
                    (
                    Select C.*,PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual
                    From
                    (
                        Select B.*, CASE WHEN min(prd_prdcd) IS NULL THEN PluKarton ELSE min(prd_prdcd) END as PLUKecil--, PRD_Unit as UnitKecil,PRD_Frac as FracKecil,PRD_MinJual
                        From
                        (
                            Select A.FDRCID, A.FDNOUO, A.FDKODE, MAX(A.FDQTYB) as FDQTYB, A.FDKCAB, A.FDTGPB, A.FDKSUP, A.REQ_ID, A.NAMA_FILE , prd_deskripsipanjang as DESK, prd_flagbkp1 as BKP, prd_prdcd as PluKarton,prd_unit as UnitKarton,prd_frac as FracKarton
                            From temp_pbidm_ready2 A, tbmaster_prodmast
                            Where REQ_ID = '" . $ip . "'
                            AND FDKCAB = '" . $kodeToko . "'
                            AND FDNOUO = '" . $noPB . "'
                            AND FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                            AND prd_prdcd = prc_pluigr
                            GROUP By A.FDRCID,
                            A.FDNOUO,
                            A.FDKODE,
                            A.FDTGPB,
                            A.FDKCAB,
                            A.FDKSUP,
                            A.REQ_ID,
                            A.NAMA_FILE,
                            prd_deskripsipanjang,
                            prd_flagbkp1,
                            prd_prdcd,
                            prd_unit,
                            prd_frac
                        ) B, tbMaster_Prodmast
                            Where PRD_PRDCD <> SUBSTR(PLUKarton,1,6)||'0'
                            And PRD_PRDCD Like SUBSTR(PLUKarton,1,6)||'%'
                            AND COALESCE(prd_KodeTag,'A') NOT IN ('N','X','Q')
                            Group By fdrcid,
                            fdnouo,
                            fdkode,
                            fdqtyb,
                            fdkcab,
                            fdtgpb,
                            fdksup,
                            req_id,
                            nama_file,
                            Desk,
                            PluKarton,
                            UnitKarton,
                            FracKarton,
                            BKP
                    ) C, tbMaster_prodmast
                    Where PRD_PRDCD = PluKecil
                    )D
                ) E, tbMaster_Stock
                Where ST_PRDCD = PLUKARTON
                And ST_Lokasi = '01'
                And COALESCE(ST_RecordID,'0') <> '1'
            ");
            }

            //! UPDATE TEMP_PBIDM_READY SUPAYA ITEM HANDHELD IN PIECES SEMUA
            // sb.AppendLine("UPDATE TEMP_PBIDM_READY ")
            // sb.AppendLine("   SET QTYB = 0, ")
            // sb.AppendLine("       QTYK = QTYK + QTYB * FRACKARTON ")
            // sb.AppendLine(" WHERE REQ_ID = '" & IP & "'  ")
            // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "' ")
            // sb.AppendLine("   AND FDNOUO = '" & noPB & "' ")
            // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND QTYB > 0 ")

            DB::select("
                UPDATE TEMP_PBIDM_READY
                SET QTYB = 0,
                    QTYK = QTYK + QTYB * FRACKARTON
                WHERE REQ_ID = '" . $ip . "'
                    AND FDKCAB = '" . $kodeToko . "'
                    AND FDNOUO = '" . $noPB . "'
                    AND FDTGPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                    AND QTYB > 0
            ");

            //! GET -> AdaKarton
            // sb.AppendLine("Select COALESCE(Count(1),0)  ")
            // sb.AppendLine("  From temp_pbidm_ready ")
            // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
            // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("   AND FDNOUO = '" & noPB & "'  ")
            // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND QTYB > 0 ")

            $data = DB::table('temp_pbidm_ready')
                ->where([
                    'req_id' => $ip,
                    'fdkcab' => $kodeToko,
                    'fdnouo' => $noPB,
                ])
                ->whereDate('fdtgpb', $tglPB)
                ->where('qtyb','>',0)
                ->count();

            // If jum > 0 Then
            //     AdaKartonan = True
            // End If

            $AdaKarton = $data > 0 ? True : False;

            //! GET -> AdaKecil
            // sb.AppendLine("Select COALESCE(Count(1),0)  ")
            // sb.AppendLine("  From temp_pbidm_ready ")
            // sb.AppendLine(" Where REQ_ID = '" & IP & "'  ")
            // sb.AppendLine("   AND FDKCAB = '" & KodeToko & "'  ")
            // sb.AppendLine("   AND FDNOUO = '" & noPB & "'  ")
            // sb.AppendLine("   AND FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")
            // sb.AppendLine("   AND QTYK > 0 ")

            $data = DB::table('temp_pbidm_ready')
                ->where([
                    'req_id' => $ip,
                    'fdkcab' => $kodeToko,
                    'fdnouo' => $noPB,
                ])
                ->whereDate('fdtgpb', $tglPB)
                ->where('qtyk','>',0)
                ->count();

            // If jum > 0 Then
            //     AdaKecil = True
            // End If

            $AdaKecil = $data > 0 ? True : False;

            // sb.AppendLine("Select COALESCE(Count(1),0) ")
            // sb.AppendLine("  From tbtr_counterpbomi ")
            // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
            // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

            $data = DB::table('tbtr_counterpbomi')
                ->where([
                    'cou_kodeomi' => $kodeToko,
                    'cou_kodeigr' => session('KODECABANG')
                ])
                ->count();

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

            if($data == 0){
                //! Insert Into tbtr_CounterPbOMI
                DB::insert([
                    'cou_kodeigr' => session('KODECABANG'),
                    'cou_kodeomi' => $kodeToko,
                    'cou_tgl' => Carbon::now(),
                    'cou_nodokumen' => '',
                    'cou_create_by' => session('userid'),
                    'cou_create_dt' => Carbon::now(),
                ]);

                if($AdaKecil) $CounterKecil = 1;
                $CounterKarton = ($AdaKarton && $AdaKecil) ? 2 : 1;
            }


            // sb.AppendLine("Select COALESCE(length(rtrim(cou_nodokumen)),0) ")
            // sb.AppendLine("  From tbtr_counterpbomi ")
            // sb.AppendLine(" Where COU_KodeOmi = '" & KodeToko & "' ")
            // sb.AppendLine("   And COU_KodeIGR = '" & KDIGR & "' ")

            $data = DB::table('tbtr_counterpbomi')
                ->where([
                    'cou_kodeomi' => $kodeToko,
                    'cou_kodeigr' => session('KODECABANG'),
                ])
                ->count();

            if($data >= 8){
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

                $query = '';
                $query .= "UPDATE TbTr_CounterPBOMI ";
                if($AdaKarton && $AdaKecil){
                    $query .= "   SET COU_NoDokumen = 'YY', ";
                }else{
                    $query .= "   SET COU_NoDokumen = 'Y', ";
                }
                $query .= "       COU_Modify_By = '" . session('userid') . "', ";
                $query .= "       COU_Modify_Dt = current_timestamp	    ";
                $query .= " Where COU_KodeOmi = '" . $kodeToko . "' ";
                $query .= "   And COU_KodeIGR = '" . session('KODECABANG') . "' ";

                DB::select($query);

                // If AdaKecil Then CounterKecil = 1
                // If AdaKartonan Then If AdaKecil Then CounterKarton = 2 Else CounterKarton = 1

                if($AdaKecil) $CounterKecil = 1;
                $CounterKarton = ($AdaKarton && $AdaKecil) ? 2 : 1;

            }else{

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

                $query .= "UPDATE TbTr_CounterPBOMI ";
                if($AdaKarton && $AdaKecil){
                    $query .= "   SET COU_NoDokumen = RTRIM(COU_NoDokumen)||'YY', ";
                }else{
                    $query .= "   SET COU_NoDokumen = RTRIM(COU_NoDokumen)||'Y', ";
                }
                $query .= "       COU_Modify_By = '" . session('userid') . "', ";
                $query .= "       COU_Modify_Dt = current_timestamp	    ";
                $query .= " Where COU_KodeOmi = '" . $kodeToko . "' ";
                $query .= "   And COU_KodeIGR = '" . session('KODECABANG') . "' ";

                // If AdaKecil Then CounterKecil = jum + 1
                // If AdaKartonan Then CounterKarton = CounterKecil + 1

                if($AdaKecil) $CounterKecil = (int)$data + 1;
                if($AdaKarton) $CounterKarton = (int)$CounterKecil + 1;

            }

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

            DB::select("
                INSERT INTO TBTR_TolakanPBOMI
                (
                    TLKO_KodeIGR,
                    TLKO_KodeOMI,
                    TLKO_TglPB,
                    TLKO_NoPB,
                    TLKO_PluIGR,
                    TLKO_PluOMI,
                    TLKO_PTAG,
                    TLKO_DESC,
                    TLKO_KetTolakan,
                    TLKO_QtyOrder,
                    TLKO_LastCost,
                    TLKO_Nilai,
                    TLKO_Create_By,
                    TLKO_Create_Dt
                )
                Select KODEIGR,
                    KCAB,
                    TGLDOK,
                    NODOK,
                    PLUIGR,
                    PLU,
                    TAG,
                    DESCR,
                    KETA,
                    QTYO,
                    ST_AVGCOST,
                    GROSS,
                    '" .session('userid'). "',
                    current_timestamp
                From TEMP_CETAKPB_TOLAKAN_IDM
                Join tbMaster_Stock
                    ON ST_PRDCD = SUBSTR(PLUIGR,1,6)||'0'
                    And ST_Lokasi = '01'
                Where REQ_ID = '" . $ip . "'
            ");

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

            if($chkIDMBacaProdcrm){
                if($kodeDCIDM <> ""){
                    $query .= "update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.IDM_KodeTag ";
                    $query .= "from (       ";
                    $query .= "      SELECT IDM_PLUIDM, ";
                    $query .= "      IDM_KodeTag ";
                    $query .= "      FROM tbMaster_Pluidm";
                    $query .= "      Where IDM_KodeIDM = '" . $kodeDCIDM . "' ";
                    $query .= "      And Exists ( ";
                    $query .= "          Select tlko_pluomi ";
                    $query .= "          From tbtr_TolakanPbOMI ";
                    $query .= "          Where tlko_PluOMI = IDM_PLUIDM ";
                    $query .= "   	     And tlko_KodeOMI = '" . $kodeToko . "' ";
                    $query .= "    		 And tlko_NoPB = '" . $noPB . "' ";
                    $query .= "   		 And tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                    $query .= "        ) ";
                    $query .= ") b where a.TLKO_PLUOMI = b.IDM_PLUIDM ";
                    $query .= "    and a.tlko_KodeOMI = '" . $kodeToko . "' ";
                    $query .= "    And a.tlko_NoPB = '" . $noPB . "' ";
                    $query .= "    And a.tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";

                }else{
                    $query .= "update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.PRC_KodeTag    ";
                    $query .= "from (";
                    $query .= "    SELECT PRC_PLUIDM, ";
                    $query .= "           PRC_KodeTag";
                    $query .= "     FROM tbMaster_Prodcrm";
                    $query .= "     Where Exists";
                    $query .= "     ( ";
                    $query .= "     Select tlko_pluomi";
                    $query .= "     From tbtr_TolakanPbOMI ";
                    $query .= "           Where tlko_PluOMI = PRC_PLUIDM ";
                    $query .= "           And tlko_KodeOMI = '" . $kodeToko . "' ";
                    $query .= "           And tlko_NoPB = '" . $noPB . "' ";
                    $query .= "   	      And tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                    $query .= "        ) ";
                    $query .= " ) b ";
                    $query .= "where a.TLKO_PLUOMI = b.PRC_PLUIDM ";
                    $query .= "      and a.tlko_KodeOMI = '" . $kodeToko . "' ";
                    $query .= "      And a.tlko_NoPB = '" . $noPB . "' ";
                    $query .= "      And a.tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                }
            }else{
                $query .= "update TBTR_TOLAKANPBOMI A SET TLKO_TAG_MD = b.IDM_Tag   ";
                $query .= "from (      ";
                $query .= "      SELECT IDM_PLUIDM, ";
                $query .= "             IDM_Tag ";
                $query .= "      FROM TBTEMP_PLUIDM ";
                $query .= "      Where Exists ";
                $query .= "       (  ";
                $query .= "           Select tlko_pluomi ";
                $query .= "           From tbtr_TolakanPbOMI ";
                $query .= "           Where tlko_PluOMI = IDM_PLUIDM ";
                $query .= "           And tlko_KodeOMI = '" . $kodeToko . "' ";
                $query .= " 		  And tlko_NoPB = '" . $noPB . "' ";
                $query .= "           And tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "        ) ";

                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }

                $query .= " ) b where a.TLKO_PLUOMI = b.IDM_PLUIDM ";
                $query .= "    and a.tlko_KodeOMI = '" . $kodeToko . "' ";
                $query .= "    And a.tlko_NoPB = '" . $noPB . "' ";
                $query .= "    And a.tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
            }

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

            DB::select("
            UPDATE TBTR_TOLAKANPBOMI A SET TLKO_TAG_IGR = b.PRD_KodeTag
            from (
                SELECT PRD_PRDCD, PRD_KodeTAG
                FROM TbMaster_Prodmast
                Where Exists
                (
                    Select tlko_pluomi
                    From tbtr_TolakanPbOMI
                    Where tlko_PluIGR = PRD_PRDCD
                        And tlko_KodeOMI = '" . $kodeToko . "'
                        And tlko_NoPB = '" . $noPB . "'
                        And tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                )
            ) b where a.TLKO_PLUOMI = b.PRD_PRDCD
                And a.tlko_KodeOMI = '" . $kodeToko . "'
                And a.tlko_NoPB = '" . $noPB . "'
                And a.tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            ");

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

            DB::select("
                UPDATE TBTR_TOLAKANPBOMI A
                SET TLKO_NILAI = b.RUPIAH,
                TLKO_MARGIN = b.MARGIN,
                TLKO_LPP = b.LPP
                from (
                    SELECT ST_PRDCD,
                        round(st_avgcost * (1 + COALESCE(MPI_MARGIN,3)/100)) as RUPIAH,
                        round(st_avgcost * (COALESCE(MPI_MARGIN,3)/100)) as MARGIN,
                        COALESCE(ST_SALDOAKHIR,0) as LPP
                    FROM TbMaster_Stock,
                        TbMaster_MarginPluIDM
                    Where Exists
                    (
                        Select tlko_pluomi
                        From tbtr_TolakanPbOMI
                        Where tlko_PluIGR = ST_PRDCD
                            And tlko_KodeOMI = '" . $kodeToko . "'
                            And tlko_NoPB = '" . $noPB . "'
                            And tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                    )
                    And ST_Lokasi = '01'
                    And ST_PRDCD = MPI_PluIGR
                ) b
                where a.TLKO_PLUIGR = b.ST_PRDCD
                    and a.tlko_KodeOMI = '" . $kodeToko . "'
                    And a.tlko_NoPB = '" . $noPB . "'
                    And a.tlko_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            ");

            //! '-----------------------------------'
            //! '+ SIAPKAN DATA JALUR TIDAK KETEMU +'
            //! '-----------------------------------'

            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From information_schema.columns ")
            // sb.AppendLine(" Where  ")

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_NOJALUR_IDM'")
                ->count();

            if($data == 0){

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

                DB::select("
                    CREATE TABLE TEMP_NOJALUR_IDM
                    AS
                    SELECT fdrcid,
                        fdnouo,
                        fdkode,
                        fdqtyb,
                        fdkcab,
                        fdtgpb,
                        fdksup,
                        req_id,
                        nama_file,
                        desk,
                        bkp,
                        plukarton,
                        unitkarton,
                        frackarton,
                        plukecil,
                        unitkecil,
                        frackecil,
                        prd_minjual,
                        qtyb,
                        qtyk,
                        tolakminj,
                        avgcost
                    From temp_pbidm_ready pbi
                    Where pbi.REQ_ID = '" . $ip . "'
                        AND pbi.FDKCAB = '" . $kodeToko . "'
                        And pbi.fdnouo = '" . $noPB . "'
                        And pbi.fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        And Not EXISTS
                        (
                            Select lks_koderak
                            From tbMaster_Lokasi
                            Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                                And LKS_PRDCD = pbi.PLUKarton
                                And LKS_TIPERAK NOT LIKE  'S%'
                        )
                        And COALESCE(pbi.TolakMinJ,'X') <> 'T'
                        And Not EXISTS
                        (
                            Select K.PLUKARTON
                            From TEMP_JALURKERTAS_IDM K
                            Where K.REQ_ID = '" . $ip . "'
                                AND K.FDKCAB = '" . $kodeToko . "'
                                And K.fdnouo = '" . $noPB . "'
                                And K.fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                                And K.PLUKARTON = pbi.PLUKARTON
                        )
                ");
            }else{
                //! Delete From TEMP_NOJALUR_IDM
                // sb.AppendLine("Delete From TEMP_NOJALUR_IDM ")
                // sb.AppendLine(" Where REQ_ID = '" & IP & "' ")
                // sb.AppendLine("   And FDNOUO = '" & noPB & "' ")
                // sb.AppendLine("   And FDTGPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

                DB::table('temp_nojalur_idm')
                    ->where([
                        'req_id' => $ip,
                        'fdnouo' => $noPB,
                    ])
                    ->whereDate('fdtgpb', $tglPB)
                    ->delete();

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

                DB::select("
                    INSERT INTO TEMP_NOJALUR_IDM
                    (
                        fdrcid,
                        fdnouo,
                        fdkode,
                        fdqtyb,
                        fdkcab,
                        fdtgpb,
                        fdksup,
                        req_id,
                        nama_file,
                        desk,
                        bkp,
                        plukarton,
                        unitkarton,
                        frackarton,
                        plukecil,
                        unitkecil,
                        frackecil,
                        prd_minjual,
                        qtyb,
                        qtyk,
                        tolakminj,
                        avgcost
                    )
                    SELECT fdrcid,
                        fdnouo,
                        fdkode,
                        fdqtyb,
                        fdkcab,
                        fdtgpb,
                        fdksup,
                        req_id,
                        nama_file,
                        desk,
                        bkp,
                        plukarton,
                        unitkarton,
                        frackarton,
                        plukecil,
                        unitkecil,
                        frackecil,
                        prd_minjual,
                        qtyb,
                        qtyk,
                        tolakminj,
                        avgcost
                    From temp_pbidm_ready pbi
                    Where pbi.REQ_ID = '" . $ip . "'
                        AND pbi.FDKCAB = '" . $kodeToko . "'
                        And pbi.fdnouo = '" . $noPB . "'
                        And pbi.fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        And Not EXISTS
                        (
                            Select lks_koderak
                            From tbMaster_Lokasi
                            Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                                And LKS_PRDCD = pbi.PLUKarton
                                And LKS_TIPERAK NOT LIKE  'S%'
                        )
                        And COALESCE(pbi.TolakMinJ,'X') <> 'T'
                        And Not EXISTS
                        (
                            Select K.PLUKARTON
                            From TEMP_JALURKERTAS_IDM K
                            Where K.REQ_ID = '" . $ip . "'
                                AND K.FDKCAB = '" . $kodeToko . "'
                                And K.fdnouo = '" . $noPB . "'
                                And K.fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                                And K.PLUKARTON = pbi.PLUKARTON
                        )
                ");
            }

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

            DB::select("
                INSERT INTO TEMP_NOJALUR_IDM
                (
                    fdrcid,
                    fdnouo,
                    fdkode,
                    fdqtyb,
                    fdkcab,
                    fdtgpb,
                    fdksup,
                    req_id,
                    nama_file,
                    desk,
                    bkp,
                    plukarton,
                    unitkarton,
                    frackarton,
                    plukecil,
                    unitkecil,
                    frackecil,
                    prd_minjual,
                    qtyb,
                    qtyk,
                    tolakminj,
                    avgcost
                )
                SELECT fdrcid,
                    fdnouo,
                    fdkode,
                    fdqtyb,
                    fdkcab,
                    fdtgpb,
                    fdksup,
                    req_id,
                    nama_file,
                    desk,
                    bkp,
                    plukarton,
                    unitkarton,
                    frackarton,
                    plukecil,
                    unitkecil,
                    frackecil,
                    prd_minjual,
                    qtyb,
                    qtyk,
                    tolakminj,
                    avgcost
                From temp_pbidm_ready
                Where REQ_ID = '" . $ip . "'
                    AND FDKCAB = '" . $kodeToko . "'
                    And fdnouo = '" . $noPB . "'
                    And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    And EXISTS
                    (
                        Select lks_koderak
                        From tbMaster_Lokasi
                        Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                            And LKS_PRDCD = PLUKarton
                            And LKS_TIPERAK NOT LIKE  'S%'
                    )
                    And NOT EXISTS
                    (
                        Select grr_koderak
                        From tbMaster_Lokasi,tbMaster_GroupRak
                        Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                            And LKS_PRDCD = PLUKarton
                            And GRR_Koderak = LKS_KodeRak
                            And GRR_Subrak  = LKS_KodeSubrak
                            And LKS_TIPERAK NOT LIKE  'S%'
                    )
                    And COALESCE(TolakMinJ,'X') <> 'T'
                    And QTYK > 0
            ");

            //! INSERT INTO TEMP_NOJALUR_IDM 3
            DB::select("
                INSERT INTO TEMP_NOJALUR_IDM
                (
                    fdrcid,
                    fdnouo,
                    fdkode,
                    fdqtyb,
                    fdkcab,
                    fdtgpb,
                    fdksup,
                    req_id,
                    nama_file,
                    desk,
                    bkp,
                    plukarton,
                    unitkarton,
                    frackarton,
                    plukecil,
                    unitkecil,
                    frackecil,
                    prd_minjual,
                    qtyb,
                    qtyk,
                    tolakminj,
                    avgcost
                )
                SELECT fdrcid,
                    fdnouo,
                    fdkode,
                    fdqtyb,
                    fdkcab,
                    fdtgpb,
                    fdksup,
                    req_id,
                    nama_file,
                    desk,
                    bkp,
                    plukarton,
                    unitkarton,
                    frackarton,
                    plukecil,
                    unitkecil,
                    frackecil,
                    prd_minjual,
                    qtyb,
                    qtyk,
                    tolakminj,
                    avgcost
                From temp_pbidm_ready
                Where REQ_ID = '" . $ip . "'
                    AND FDKCAB = '" . $kodeToko . "'
                    And fdnouo = '" . $noPB . "'
                    And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    And EXISTS
                    (
                        Select grr_koderak
                        From tbMaster_Lokasi,tbMaster_GroupRak
                        Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                            And LKS_PRDCD = PLUKarton
                            And GRR_Koderak = LKS_KodeRak
                            And GRR_Subrak  = LKS_KodeSubrak
                            And COALESCE(LKS_Noid,'X') Like '%B'
                            And COALESCE(GRR_FlagCetakan,'X') <> 'Y'
                            And LKS_TIPERAK NOT LIKE  'S%'
                    )
                    And NOT EXISTS
                    (
                        Select grr_koderak
                        From tbMaster_Lokasi,tbMaster_GroupRak
                        Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                            And LKS_PRDCD = PLUKarton
                            And GRR_Koderak = LKS_KodeRak
                            And GRR_Subrak  = LKS_KodeSubrak
                            And COALESCE(LKS_Noid,'X') Like '%P'
                            And COALESCE(GRR_FlagCetakan,'X') <> 'Y'
                            And LKS_TIPERAK NOT LIKE  'S%'
                    )
                    And NOT EXISTS
                    (
                        Select grr_koderak
                        From tbMaster_Lokasi,tbMaster_GroupRak
                        Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                            And LKS_PRDCD = PLUKarton
                            And GRR_Koderak = LKS_KodeRak
                            And GRR_Subrak  = LKS_KodeSubrak
                            And COALESCE(GRR_FlagCetakan,'X') = 'Y'
                            And LKS_TIPERAK NOT LIKE  'S%'
                    )
                    And COALESCE(TolakMinJ,'X') <> 'T'
                    And QTYK > 0
            ");

            //! INSERT INTO TEMP_NOJALUR_IDM 4
            DB::select("
            INSERT INTO TEMP_NOJALUR_IDM
            (
                fdrcid,
                fdnouo,
                fdkode,
                fdqtyb,
                fdkcab,
                fdtgpb,
                fdksup,
                req_id,
                nama_file,
                desk,
                bkp,
                plukarton,
                unitkarton,
                frackarton,
                plukecil,
                unitkecil,
                frackecil,
                prd_minjual,
                qtyb,
                qtyk,
                tolakminj,
                avgcost
            )
            SELECT fdrcid,
                fdnouo,
                fdkode,
                fdqtyb,
                fdkcab,
                fdtgpb,
                fdksup,
                req_id,
                nama_file,
                desk,
                bkp,
                plukarton,
                unitkarton,
                frackarton,
                plukecil,
                unitkecil,
                frackecil,
                prd_minjual,
                qtyb,
                qtyk,
                tolakminj,
                avgcost
            From temp_pbidm_ready
            Where REQ_ID = '" . $ip . "'
                AND FDKCAB = '" . $kodeToko . "'
                And fdnouo = '" . $noPB . "'
                And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                And EXISTS
                (
                    Select grr_koderak
                    From tbMaster_Lokasi,tbMaster_GroupRak
                    Where LKS_KodeIGR = '" . session('KODECABANG') . "'
                        And LKS_PRDCD = PLUKarton
                        And GRR_Koderak = LKS_KodeRak
                        And GRR_Subrak  = LKS_KodeSubrak
                        And LKS_NOID IS NULL
                        And LKS_KodeRak Like 'D%'
                        And LKS_TIPERAK NOT LIKE  'S%'
                )
                AND NOT EXISTS
                (
                    Select LKS_NOID
                    From tbMaster_Lokasi
                    Where LKS_PRDCD = PLUKarton
                        And LKS_NOID Like '%P'
                        And LKS_TIPERAK NOT LIKE  'S%'
                )
                And COALESCE(TolakMinJ,'X') <> 'T'
                And QTYK > 0
            ");

            //! INSERT INTO TEMP_NOJALUR_IDM 5
            DB::select("
                INSERT INTO TEMP_NOJALUR_IDM
                Select 'B',
                    FDNOUO,
                    FDKODE,
                    FDQTYB,
                    FDKCAB,
                    FDTGPB,
                    FDKSUP,
                    REQ_ID,
                    NAMA_FILE,
                    DESK,
                    BKP,
                    PLUKARTON,
                    UNITKARTON,
                    FRACKARTON,
                    PLUKECIL,
                    UNITKECIL,
                    FRACKECIL,
                    PRD_MINJUAL,
                    QTYB,
                    QTYK,
                    TOLAKMINJ,
                    AVGCOST
                From temp_pbidm_ready
                Where REQ_ID = '" . $ip . "'
                    AND FDKCAB = '" . $kodeToko . "'
                    And fdnouo = '" . $noPB . "'
                    And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    And NOT EXISTS
                    (
                        Select BRC_Barcode
                        From tbMaster_Barcode
                        Where BRC_PRDCD = PLUKECIL
                    )
            ");

            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From information_schema.columns ")
            // sb.AppendLine(" Where upper(table_name) = 'TBMASTER_MARGINPLUIDM' ")

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TBMASTER_MARGINPLUIDM'")
                ->count();

            //! INSERT KE MASDPB BULKY
            if($data > 0){
                DB::select("
                    Insert Into tbMaster_PBOmi
                    (
                        pbo_kodeigr,
                        pbo_recordid,
                        pbo_nourut,
                        pbo_batch,
                        pbo_tglpb,
                        pbo_nopb,
                        pbo_kodesbu,
                        pbo_kodemember,
                        pbo_kodeomi,
                        pbo_kodedivisi,
                        pbo_kodedepartemen,
                        pbo_kodekategoribrg,
                        pbo_pluomi,
                        pbo_pluigr,
                        pbo_hrgsatuan,
                        pbo_qtyorder,
                        pbo_qtyrealisasi,
                        pbo_nilaiorder,
                        pbo_ppnorder,
                        pbo_distributionfee,
                        pbo_create_by,
                        pbo_create_dt,
                        pbo_TglStruk
                    )
                    Select '" . session('KODECABANG') . "',
                        NULL,
                        row_number() over(),
                        '" . $CounterKarton . "',
                        fdtgpb,
                        fdnouo,
                        '" . $KodeSBU . "',
                        '" . $KodeMember . "',
                        fdkcab,
                        prd_kodedivisi,
                        prd_kodedepartement,
                        prd_kodekategoribarang,
                        fdkode,
                        plukecil,
                        round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + (COALESCE(MPI_MARGIN,3)/100) ),0),
                        QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END,
                        QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END,
                        QtyB * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0),
                        QtyB * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END,
                        0,
                        '" . session('userid') . "',
                        current_timestamp,
                        CURRENT_DATE
                    FROM temp_pbidm_ready
                    JOIN tbmaster_prodmast on prd_prdcd = PLUKarton
                    JOIN tbMaster_MarginPluIDM on MPI_PLUIGR = PLUKARTON
                    WHERE req_id = '" . $ip . "'
                        and fdnouo = '" . $noPB . "'
                        and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        and qtyb > 0
                        and COALESCE(TolakMinJ,'X') <> 'T'
                ");
            }else{
                DB::select("
                Insert Into tbMaster_PBOmi
                (
                    pbo_kodeigr,
                    pbo_recordid,
                    pbo_nourut,
                    pbo_batch,
                    pbo_tglpb,
                    pbo_nopb,
                    pbo_kodesbu,
                    pbo_kodemember,
                    pbo_kodeomi,
                    pbo_kodedivisi,
                    pbo_kodedepartemen,
                    pbo_kodekategoribrg,
                    pbo_pluomi,
                    pbo_pluigr,
                    pbo_hrgsatuan,
                    pbo_qtyorder,
                    pbo_qtyrealisasi,
                    pbo_nilaiorder,
                    pbo_ppnorder,
                    pbo_distributionfee,
                    pbo_create_by,
                    pbo_create_dt,
                    pbo_TglStruk
                )
                Select '" . session('KODECABANG') . "',
                    NULL,
                    Row_number() over(),
                    '" . $CounterKarton . "',
                    fdtgpb,
                    fdnouo,
                    '" . $KodeSBU . "',
                    '" . $KodeMember . "',
                    fdkcab,
                    prd_kodedivisi,
                    prd_kodedepartement,
                    prd_kodekategoribarang,
                    fdkode,
                    plukecil,
                    round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + " . $PersenMargin . "),0),
                    QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END,
                    QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END,
                    QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + " . $PersenMargin . "),0),
                    QtyB * CASE WHEN UnitKarton = 'KG' THEN 1 ELSE FracKarton END * round(avgcost::numeric * (1 + " . $PersenMargin . "),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END,
                    0,
                    '" . session('userid') . "',
                    current_timestamp,
                    CURRENT_DATE
                From temp_pbidm_ready,tbmaster_prodmast
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and qtyb > 0
                    and prd_prdcd = PLUKarton
                    and COALESCE(TolakMinJ,'X') <> 'T'
                ");
            }

            //! GET -> PBO_NoUrut
            // sb.AppendLine("Select COALESCE(Max(pbo_nourut),1) ")
            // sb.AppendLine("  From tbMaster_PbOMI ")
            // sb.AppendLine(" Where PBO_KodeIGR = '" & KDIGR & "' ")
            // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "' ")
            // sb.AppendLine("   And PBO_NoPB = '" & noPB & "' ")
            // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY')")

            $PBO_NoUrut = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB,
                ])
                ->whereDate('pbo_tglpb', $tglPB)
                ->selectRaw("COALESCE(Max(pbo_nourut),1) as count")
                ->first()->count;

            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From information_schema.columns ")
            // sb.AppendLine(" Where upper(table_name) = 'TBMASTER_MARGINPLUIDM' ")

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TBMASTER_MARGINPLUIDM'")
                ->count();

            //! INSERT KE MASDPB PIECES
            if($data > 0){
                DB::select("
                    Insert Into tbMaster_PBOmi
                    (
                        pbo_kodeigr,
                        pbo_recordid,
                        pbo_nourut,
                        pbo_batch,
                        pbo_tglpb,
                        pbo_nopb,
                        pbo_kodesbu,
                        pbo_kodemember,
                        pbo_kodeomi,
                        pbo_kodedivisi,
                        pbo_kodedepartemen,
                        pbo_kodekategoribrg,
                        pbo_pluomi,
                        pbo_pluigr,
                        pbo_hrgsatuan,
                        pbo_qtyorder,
                        pbo_qtyrealisasi,
                        pbo_nilaiorder,
                        pbo_ppnorder,
                        pbo_distributionfee,
                        pbo_create_by,
                        pbo_create_dt,
                        pbo_TglStruk
                    )
                    Select '" . session('KODECABANG') . "',
                        NULL,
                        row_number() over() + " . $PBO_NoUrut . ",
                        '" . $CounterKecil . "',
                        fdtgpb,
                        fdnouo,
                        '" . $KodeSBU . "',
                        '" . $KodeMember . "',
                        fdkcab,
                        prd_kodedivisi,
                        prd_kodedepartement,
                        prd_kodekategoribarang,
                        fdkode,
                        plukecil,
                        round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0),
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END,
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END,
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0),
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + (COALESCE(MPI_MARGIN,3) / 100)),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END,
                        0,
                        '" . session('userid') . "',
                        current_timestamp,
                        CURRENT_DATE
                    FROM temp_pbidm_ready
                    JOIN tbmaster_prodmast on prd_prdcd = PLUKarton
                    JOIN tbMaster_MarginPluIDM on MPI_PLUIGR = PLUKARTON
                    WHERE req_id = '" . $ip . "'
                        and fdnouo = '" . $noPB . "'
                        and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        and qtyK > 0
                        and COALESCE(TolakMinJ,'X') <> 'T'
                ");
            }else{
                DB::select("
                    Insert Into tbMaster_PBOmi
                    (
                        pbo_kodeigr,
                        pbo_recordid,
                        pbo_nourut,
                        pbo_batch,
                        pbo_tglpb,
                        pbo_nopb,
                        pbo_kodesbu,
                        pbo_kodemember,
                        pbo_kodeomi,
                        pbo_kodedivisi,
                        pbo_kodedepartemen,
                        pbo_kodekategoribrg,
                        pbo_pluomi,
                        pbo_pluigr,
                        pbo_hrgsatuan,
                        pbo_qtyorder,
                        pbo_qtyrealisasi,
                        pbo_nilaiorder,
                        pbo_ppnorder,
                        pbo_distributionfee,
                        pbo_create_by,
                        pbo_create_dt,
                        pbo_TglStruk
                    )
                    Select '" . session('KODECABANG') . "',
                        NULL,
                        row_number() over() + " . $PBO_NoUrut . ",
                        '" . $CounterKecil . "',
                        fdtgpb,
                        fdnouo,
                        '" . $KodeSBU . "',
                        '" . $KodeMember . "',
                        fdkcab,
                        prd_kodedivisi,
                        prd_kodedepartement,
                        prd_kodekategoribarang,
                        fdkode,
                        plukecil,
                        round(avgcost::numeric / CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END * (1 + ". $PersenMargin ."),0),
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END,
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END,
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + ". $PersenMargin ."),0),
                        QtyK * CASE WHEN UnitKecil = 'KG' THEN 1 ELSE FracKecil END * round(avgcost::numeric * (1 + ". $PersenMargin ."),0) * (COALESCE(PRD_PPN,0) / 100) * CASE WHEN COALESCE(PRD_FlagBKP1,'X') = 'Y' THEN 1 ELSE 0 END,
                        0,
                        '" . session('userid') . "',
                        current_timestamp,
                        CURRENT_DATE
                    From temp_pbidm_ready,tbmaster_prodmast
                    Where req_id = '" . $ip . "'
                        and fdnouo = '" . $noPB . "'
                        and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        and qtyK > 0
                        and prd_prdcd = PLUKarton
                        and COALESCE(TolakMinJ,'X') <> 'T'
                ");
            }

            //! '-------------------------------'
            //! '+ UPDATE RECID TBMASTER_PBOMI +'
            //! '-------------------------------'

            //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR KARTON
            DB::select("
                Update tbMaster_PBOMI
                Set pbo_recordID = '3'
                Where EXISTS
                (
                    Select PluKecil
                    From TEMP_KARTON_NONDPD_IDM
                    Where REQ_ID = '" . $ip . "'
                        And FDKCAB = '" . $kodeToko . "'
                        And fdnouo = '" . $noPB . "'
                        And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%'
                )
                And PBO_KodeIGR = '" . session('KODECABANG') . "'
                And PBO_KodeOMI = '" . $kodeToko . "'
                And PBO_NoPB = '" . $noPB . "'
                And PBO_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
                And PBO_Batch = '" . $CounterKarton . "'
            ");

            //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR PIECES
            DB::select("
                Update tbMaster_PBOMI
                Set pbo_recordID = '3'
                Where EXISTS
                (
                    Select PluKecil
                    From TEMP_NOJALUR_IDM
                    Where REQ_ID = '" . $ip . "'
                        And FDKCAB = '" . $kodeToko . "'
                        And fdnouo = '" . $noPB . "'
                        And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%'
                    And Not EXISTS
                    (
                        Select grr_grouprak
                        from tbmaster_grouprak
                        join tbmaster_lokasi lks2 on grr_koderak = lks2.lks_koderak
                            and grr_subrak = lks2.lks_kodesubrak
                            and LKS_KodeRak Like 'D%'
                            And LKS_TIPERAK NOT LIKE 'S%'
                            and lks_prdcd = plukarton
                    )
                )
                And PBO_KodeIGR = '" . session('KODECABANG') . "'
                And PBO_KodeOMI = '" . $kodeToko . "'
                And PBO_NoPB = '" . $noPB . "'
                And PBO_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            ");

            //! UPDATE RECID = '3' TBMASTER_PBOMI JALUR PIECES
            DB::select("
                Update tbMaster_PBOMI
                Set pbo_recordID = '3'
                Where EXISTS
                (
                    Select PluKecil
                    From TEMP_JALURKERTAS_IDM
                    Where REQ_ID = '" . $ip . "'
                        And FDKCAB = '" . $kodeToko . "'
                        And fdnouo = '" . $noPB . "'
                        And fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                        And PLUKARTON Like substr(PBO_PluIGR,1,6)||'%'
                )
                And PBO_KodeIGR = '" . session('KODECABANG') . "'
                And PBO_KodeOMI = '" . $kodeToko . "'
                And PBO_NoPB = '" . $noPB . "'
                And PBO_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY')
            ");

            //! GET -> jumItmCSV
            // sb.AppendLine("Select COALESCE(COUNT(1),0)  ")
            // sb.AppendLine("  From csv_pb_pot ")
            // sb.AppendLine(" Where CPP_IP = '" & IP & "' ")
            // sb.AppendLine("   And CPP_KodeToko = '" & KodeToko & "' ")
            // sb.AppendLine("   And CPP_NoPB = '" & noPB & "' ")
            // sb.AppendLine("   And CPP_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

            $jumItmCSV = DB::table('csv_pb_pot')
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_kodetoko' => $kodeToko,
                    'cpp_nopb' => $noPB,
                ])
                ->whereDate('cpp_tglpb', $tglPB)
                ->count();

            //! GET -> jumTolakan
            // sb.AppendLine("Select COALESCE(Count(1),0)  ")
            // sb.AppendLine("  From temp_cetakpb_tolakan_idm ")
            // sb.AppendLine(" Where REQ_ID = '" & IP & "'   ")
            // sb.AppendLine("   And KCAB = '" & KodeToko & "'    ")
            // sb.AppendLine("   And nodok = '" & noPB & "'   ")
            // sb.AppendLine("   And tgldok = to_date('" & tglPB & "','DD-MM-YYYY') ")

            $jumTolakan = DB::table('temp_cetakpb_tolakan_idm')
                ->where([
                    'req_id' => $ip,
                    'kcab' => $kodeToko,
                    'nodok' => $noPB,
                ])
                ->whereDate('tgldok', $tglPB)
                ->count();

            // If jumItmCSV - jumTolakan <= 0 Then MsgBox("Semua Item Ditolak !!, Silahkan Cek Di TBTR_TOLAKANPBOMI" & vbNewLine & "TOKO : " & KodeToko & ",NOPB : " & noPB & " TGLPB : " & tglPB) : Exit Sub
            if((int)$jumItmCSV - (int)$jumTolakan <= 0){

                $message = "Semua Item Ditolak !!, Silahkan Cek Di TBTR_TOLAKANPBOMI | TOKO : $kodeToko, NOPB : $noPB TGLPB : $tglPB";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            //! CEK ADA YANG MASUK PBOMI GA??
            // sb.AppendLine("Select COALESCE(count(pbo_pluigr),0) ")
            // sb.AppendLine("  From tbMaster_PBOMI ")
            // sb.AppendLine(" Where PBO_KodeIGR = '" & KDIGR & "'   ")
            // sb.AppendLine("   And PBO_KodeOMI = '" & KodeToko & "'    ")
            // sb.AppendLine("   And PBO_NoPB = '" & noPB & "'   ")
            // sb.AppendLine("   And PBO_TglPB = to_date('" & tglPB & "','DD-MM-YYYY') ")

            $data = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB
                ])
                ->whereDate('pbo_tglpb', $tglPB)
                ->count();

            //!ANEH ANEH ANEH HARUS CHECK

            //! INSERT INTO tbtr_tolakanpbomi - TOTAL QTY ORDER 0
            DB::select("
                INSERT INTO tbtr_tolakanpbomi
                (
                    TLKO_KodeIGR,
                    TLKO_KodeOMI,
                    TLKO_TglPB,
                    TLKO_NOPB,
                    TLKO_PLUIGR,
                    TLKO_PLUOMI,
                    TLKO_DESC,
                    TLKO_KETTOLAKAN,
                    TLKO_QtyOrder,
                    TLKO_LastCost,
                    TLKO_Nilai,
                    TLKO_Create_By,
                    TLKO_Create_Dt
                )
                SELECT '" . session('KODECABANG') . "',
                    FDKCAB,
                    FDTGPB,
                    FDNOUO,
                    PLUKECIL,
                    FDKODE,
                    DESK,
                    'TOTAL QTY ORDER 0',
                    0,
                    0,
                    0,
                    '" . session('userid') . "',
                    current_timestamp
                FROM temp_pbidm_ready
                Where req_id = '" . $ip . "'
                    and fdnouo = '" . $noPB . "'
                    and fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')
                    and fdqtyb = 0
            ");

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
                    'cpp_nopb' => $noPB,
                    'cpp_kodetoko' => $kodeToko,
                ])
                ->whereNull('cpp_flag')
                ->whereDate('cpp_tglpb', $tglPB)
                ->update([
                    'cpp_flag' => '1'
                ]);

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

            $rphOrder = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB,
                ])
                ->whereDate('pbo_tglpb', $tglPB)
                ->selectRaw("sum(COALESCE(pbo_nilaiorder,0)) as sum")
                ->first()->sum;

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

            DB::table('tbtr_header_pot')
                ->insert([
                    'hdp_kodeigr' => session('KODECABANG'),
                    'hdp_flag' => '2',
                    'hdp_tgltransaksi' => Carbon::now(),
                    'hdp_kodetoko' => $kodeToko,
                    'hdp_nopb' =>  $noPB,
                    'hdp_tglpb' => Carbon::parse($tglPB)->format('Y-m-d'),
                    'hdp_itempb' => $jumItmCSV,
                    'hdp_itemvalid' => (int)$jumItmCSV - (int)$jumTolakan,
                    'hdp_rphvalid' => $rphOrder,
                    'hdp_filepb' => $fullPathFile,
                    'hdp_create_by' => session('userid'),
                    'hdp_create_dt' => Carbon::now(),
                ]);

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

            DB::select("
                INSERT INTO DCP_DATA_POT
                    (
                    DDP_KodeSBU,
                    DDP_KodeToko,
                    DDP_NoPB,
                    DDP_TglPB,
                    DDP_PRDCD,
                    DDP_PLUIDM,
                    DDP_Deskripsi,
                    DDP_Unit,
                    DDP_Frac,
                    DDP_FlagBKP1,
                    DDP_FlagBKP2,
                    DDP_QtyOrder,
                    DDP_TglUpload,
                    DDP_IP
                )
                Select '$KodeSBU',
                    PBO_KodeOMI,
                        PBO_NoPB,
                        PBO_TglPB,
                        PBO_PluIGR,
                        PBO_PluOMI,
                        SUBSTR(PRD_DeskripsiPendek,1,20),
                        PRD_Unit,
                        PRD_Frac,
                        PRD_FlagBKP1,
                        PRD_FlagBKP2,
                        PBO_QtyOrder,
                        CURRENT_DATE,
                        '$ip'
                From tbMaster_PbOMI,
                    tbMaster_Prodmast
                Where PRD_PRDCD = PBO_PLUIGR
                And PBO_KodeOMI = '$kodeToko'
                And PBO_NoPB = '$noPB'
                And PBo_TglPB = TO_DATE('$tglPB','DD-MM-YYYY')
            ");

            // DB::commit();

            dd('success');

        } catch (HttpResponseException $e) {
            // Handle the custom response exception
            throw new HttpResponseException($e->getResponse());

        }catch(\Exception $e){

            DB::rollBack();

            $message = "Oops terjadi kesalahan ( $e )";
            throw new HttpResponseException(ApiFormatter::error(400, $message));
        }

        // CetakALL_1(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_2(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_3(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_4(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_5(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
        // CetakALL_6(PersenMargin, CounterKarton, CounterKecil)  CALL FUNCTION
    }
}
