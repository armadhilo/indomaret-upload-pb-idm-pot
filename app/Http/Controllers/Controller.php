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

    //! LIST ORDER PB
    public function CetakAll_1($kodeToko,$ip,$noPB,$tglPB,$PersenMargin){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

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

    //! REKAP ORDER PB
    public function CetakAll_2($kodeToko,$ip,$noPB,$tglPB, $PersenMargin){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

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

    //! KARTON NON DPD
    public function CetakAll_3($kodeToko,$ip,$noPB,$tglPB){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

        //! INSERT INTO PBIDM_KARTONNONDPD
        //? = "KARTON NON DPD"
        DB::select("
            INSERT INTO PBIDM_KARTONNONDPD
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

        return $data;
    }

    //! ITEM ORDER DITOLAK
    public function CetakAll_4($kodeToko,$ip,$noPB,$tglPB){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

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

    //! POSISI RAK JALUR TIDAK KETEMU
    public function CetakAll_5($kodeToko,$ip,$noPB,$tglPB){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

        //! INSERT INTO PBIDM_RAKJALUR_TIDAKKETEMU
        //? f = "RAK JALUR TIDAK KETEMU";
        DB::select("
            INSERT INTO PBIDM_RAKJALUR_TIDAKKETEMU
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
            From TEMP_NOJALUR_IDM NJI
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

    //! JALUR CETAK KERTAS
    public function CetakAll_6($kodeToko,$ip,$noPB,$tglPB){

        $data['kodeToko'] = $kodeToko;
        $data['tglPb'] = $tglPB;

        //! GET HEADER CETAKAN (NAMA CABANG)
        // sb.AppendLine("Select PRS_NamaCabang ")
        // sb.AppendLine("  From tbMaster_perusahaan ")

        $data['namaCabang'] = DB::table('tbmaster_perusahaan')
            ->select('prs_namacabang')
            ->first()->prs_namacabang;

        //! GET HEADER CETAKAN (NAMA TOKO)
        // sb.AppendLine("Select TKO_NamaOMI ")
        // sb.AppendLine("  From tbMaster_TokoIGR ")
        // sb.AppendLine(" Where TKO_KodeIGR = '" & KDIGR & "' ")
        // sb.AppendLine("   And TKO_KodeOMI = '" & KodeToko & "' ")
        // sb.AppendLine("   And coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE ")

        $data['namaToko'] = DB::table('tbmaster_tokoigr')
            ->select('tko_namaomi')
            ->where([
                'tko_kodeigr' => session('KODECABANG'),
                'tko_kodeomi' => $kodeToko,
            ])
            ->whereRaw("coalesce(TKO_TGLTUTUP,CURRENT_DATE+1) > CURRENT_DATE")
            ->first()->tko_namaomi;

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

    public function prosesPBIDM($noPB,$kodeToko,$tglPB,$fullPathFile){
        DB::beginTransaction();
        try{
            // $noPB = 'TZ4Z133';
            // $kodeToko = 'TZ4Z';
            // $tglPB = '10-10-2023';

            // $namaFile = 'PBATZ4Z.DBF';
            // $fullPathFile = 'full-path/PBATZ4Z.DBF';

            //DEFAULT VARIABLE
            $ip = $this->getIP();
            $chkIDMBacaProdcrm = true;
            $CounterKarton = 0;
            $CounterKecil = 0;
            $AdaKarton = False;
            $AdaKecil = False;
            $jumItmCSV = 0;
            $jumTolakan = 0;
            $PersenMargin = 0;
            $rphOrder = 0;
            $PBO_NoUrut = 0;

            //! DEL TEMP_CETAKPB_TOLAKAN_IDM
            DB::table('temp_cetakpb_tolakan_idm')
                ->where('req_id', $ip)
                ->delete();

            $data = DB::table('tbmaster_tokoigr')
                ->select('tko_kodecustomer')
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first();

            if(empty($data)){
                $message = "Kode Toko $kodeToko Tidak Terdaftar Di TbMaster_TokoIGR";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            $KodeMember = $data->tko_kodecustomer;

            //! GET -> KodeSBU
            $KodeSBU = DB::table('tbmaster_tokoigr')
                ->select('tko_kodesbu')
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first()->tko_kodesbu;

            //! GET -> PersenMargin
            $PersenMargin = DB::table('tbmaster_tokoigr')
                ->selectRaw("coalesce(tko_persenmargin::numeric,3) / 100 as tko_persenmargin")
                ->where([
                    'tko_kodeigr' => session('KODECABANG'),
                    'tko_kodeomi' => $kodeToko
                ])
                ->whereRaw("COALESCE(tko_tgltutup,CURRENT_DATE+1) > CURRENT_DATE")
                ->first()->tko_persenmargin;

            $check = DB::table('tbtr_header_pot')
                    ->where([
                        'hdp_kodeigr' => session('KODECABANG'),
                        'hdp_kodetoko' => $kodeToko,
                        'hdp_nopb' => $noPB,
                    ])
                    ->where(DB::raw("to_char(HDP_TGLPB,'YYYY')"), Carbon::parse($tglPB)->format('Y'))
                    ->count();

            //! dummy
            if($check > 0){
                $message = "PB Dengan No = $noPB, KodeTOKO = $kodeToko Sudah Pernah Diproses !";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            //! GET -> kodeDCIDM
            $kodeDCIDM = $this->getKodeDC($kodeToko);

            //! PROGRESS => 20

            //! ISI PLU TIDAK TERDAFTAR DI PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM
            $query = "";
            $query .= "INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ";
            $query .= "( ";
            $query .= "   KOMI, ";
            $query .= "   TGL, ";
            $query .= "   NODOK, ";
            $query .= "   TGLDOK, ";
            $query .= "   PLU, ";
            $query .= "   PLUIGR, ";
            $query .= "   KETA, ";
            $query .= "   TAG, ";
            $query .= "   DESCR, ";
            $query .= "   QTYO, ";
            $query .= "   GROSS, ";
            $query .= "   KCAB, ";
            $query .= "   KODEIGR, ";
            $query .= "   REQ_ID ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      cpp_nopb, ";
            $query .= "	      cpp_tglpb, ";
            $query .= "	      cpp_pluidm, ";
            $query .= "	      null, ";
            if($chkIDMBacaProdcrm){
                $query .= "	      'PLU TIDAK TERDAFTAR DI TBMASTER_PRODCRM', ";
            }else{
                $query .= "	      'PLU TIDAK TERDAFTAR DI TBTEMP_PLUIDM', ";
            }
            $query .= "	      null, ";
            $query .= "	      null, ";
            $query .= "	      cpp_qty, ";
            $query .= "	      null, ";
            $query .= "	      cpp_KodeToko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            $query .= "  From csv_pb_pot ";
            $query .= " Where not exists ";
            if($chkIDMBacaProdcrm){
                $query .= " ( ";
                $query .= "    Select KAT_PluIGR ";
                $query .= "      From KONVERSI_ATK ";
                $query .= "     WHERE KAT_PLUIDM = CPP_PLUIDM ";
                $query .= "       AND EXISTS ( ";
                $query .= "         SELECT st_prdcd ";
                $query .= "         FROM tbmaster_stock ";
                $query .= "         WHERE st_prdcd = kat_pluigr ";
                $query .= "         AND st_lokasi = '01' ";
                $query .= "       ) ";
                $query .= " ) ";
            }else{
                $query .= " ( ";
                $query .= "   SELECT IDM_PLUIDM  ";
                $query .= "     FROM TBTEMP_PLUIDM ";
                $query .= "    WHERE IDM_PLUIDM = cpp_pluidm ";
                if($kodeDCIDM <> ""){
                    $query .= "      AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }else{
                    $query .= " ) ";
                }
            }
            $query .= "   AND CPP_IP = '" . $ip . "'";
            $query .= "   AND CPP_KodeToko = '" . $kodeToko . "'";
            $query .= "   AND CPP_NoPB = '" . $noPB . "'";
            $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
            DB::insert($query);

            //! PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR
            $query = "";
            $query .= "INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ";
            $query .= "( ";
            $query .= "   KOMI, ";
            $query .= "   TGL, ";
            $query .= "   NODOK, ";
            $query .= "   TGLDOK, ";
            $query .= "   PLU, ";
            $query .= "   PLUIGR, ";
            $query .= "   KETA, ";
            $query .= "   TAG, ";
            $query .= "   DESCR, ";
            $query .= "   QTYO, ";
            $query .= "   GROSS, ";
            $query .= "   KCAB, ";
            $query .= "   KODEIGR, ";
            $query .= "   REQ_ID ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      CPP_NoPB, ";
            $query .= "	      CPP_TglPB, ";
            $query .= "	      CPP_PLUIDM, ";
            $query .= "	      null, ";
            $query .= "	      'PLU IDM TIDAK MEMPUNYAI PLU INDOGROSIR', ";
            $query .= "	      null, ";
            $query .= "	      null, ";
            $query .= "	      CPP_Qty, ";
            $query .= "	      null, ";
            $query .= "	      CPP_KodeToko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            $query .= "  From csv_pb_pot ";
            $query .= " Where exists ";
            $query .= " ( ";
            if($chkIDMBacaProdcrm){
                $query .= "    Select KAT_PluIGR ";
                $query .= "      From KONVERSI_ATK ";
                $query .= "     WHERE KAT_PLUIDM = CPP_PLUIDM  ";
                $query .= "       AND KAT_PLUIGR IS NULL ";
            }else{
                $query .= "   SELECT IDM_PLUIDM  ";
                $query .= "     FROM TBTEMP_PLUIDM ";
                $query .= "    WHERE IDM_PLUIDM = cpp_pluidm ";
                $query .= "      AND IDM_PLUIGR IS NULL ";
                if($kodeDCIDM <> ""){
                    $query .= "      AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }
            }
            $query .= " ) ";
            $query .= "   AND CPP_IP = '" . $ip . "'";
            $query .= "   AND CPP_KodeToko = '" . $kodeToko . "'";
            $query .= "   AND CPP_NoPB = '" . $noPB . "'";
            $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
            DB::insert($query);

            //! PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST
            //! PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST
            $query = "";
            $query .= "INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ";
            $query .= "( ";
            $query .= "   KOMI, ";
            $query .= "   TGL, ";
            $query .= "   NODOK, ";
            $query .= "   TGLDOK, ";
            $query .= "   PLU, ";
            $query .= "   PLUIGR, ";
            $query .= "   KETA, ";
            $query .= "   TAG, ";
            $query .= "   DESCR, ";
            $query .= "   QTYO, ";
            $query .= "   GROSS, ";
            $query .= "   KCAB, ";
            $query .= "   KODEIGR, ";
            $query .= "   REQ_ID ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      CPP_NoPB, ";
            $query .= "	      CPP_TglPB, ";
            $query .= "	      CPP_PLUIDM, ";
            if($chkIDMBacaProdcrm){
                $query .= "	      KAT_PLUIGR, ";
                $query .= "	      'PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST', ";
            }else{
                $query .= "	      IDM_PLUIGR, ";
                $query .= "	      'PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST', ";
            }
            $query .= "	      null, ";
            $query .= "	      null, ";
            $query .= "	      CPP_QTY, ";
            $query .= "	      null, ";
            $query .= "	      CPP_KodeToko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            if($chkIDMBacaProdcrm){
                //! PLU IGR PADA PRODCRM TIDAK ADA DI PRODMAST
                $query .= "	 FROM csv_pb_pot, KONVERSI_ATK ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT PLUIGR  ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "' ";
                $query .= "	   AND REQ_ID = '" . $ip . "'		  ";
                $query .= "	   AND NODOK = '" . $noPB . "' ";
                $query .= "	   AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "	   AND PLU = CPP_PLUIDM ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT PRD_PRDCD  ";
                $query .= "        FROM tbMaster_ProdMast ";
                $query .= "       Where PRD_PRDCD = KAT_PLUIGR ";
                $query .= "         And PRD_KodeIGR = '" . session('KODECABANG') . "'  ";
                $query .= "   )    ";
                $query .= "   AND CPP_PLUIDM = KAT_PLUIDM ";
                $query .= "   AND CPP_PLUIGR = KAT_PLUIGR ";
                DB::insert($query);

            }else{

                //! PLU IGR PADA TBTEMP_PLUIDM TIDAK ADA DI PRODMAST
                $query .= "	 FROM csv_pb_pot,TBTEMP_PLUIDM  ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT PLUIGR  ";
                $query .= "	  FROM TEMP_CETAKPB_TOLAKAN_IDM ";
                $query .= "	 WHERE KOMI = '" . $KodeMember . "' ";
                $query .= "	   AND REQ_ID = '" . $ip . "'		  ";
                $query .= "		 AND NODOK = '" . $noPB . "' ";
                $query .= "		 AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		 AND PLU = CPP_PLUIDM ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT PRD_PRDCD  ";
                $query .= "        FROM tbMaster_ProdMast ";
                $query .= "       Where PRD_PRDCD = IDM_PLUIGR ";
                $query .= "         And PRD_KodeIGR = '" . session('KODECABANG') . "'  ";
                $query .= "   )    ";
                $query .= "   AND CPP_PLUIDM = IDM_PLUIDM ";
                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }
                DB::insert($query);
            }

            //! AVG.COST <= 0 - 1
            $query = "";
            $query .= "INSERT Into TEMP_CETAKPB_TOLAKAN_IDM ";
            $query .= "( ";
            $query .= "   KOMI, ";
            $query .= "   TGL, ";
            $query .= "   NODOK, ";
            $query .= "   TGLDOK, ";
            $query .= "   PLU, ";
            $query .= "   PLUIGR, ";
            $query .= "   KETA, ";
            $query .= "   TAG, ";
            $query .= "   DESCR, ";
            $query .= "   QTYO, ";
            $query .= "   GROSS, ";
            $query .= "   KCAB, ";
            $query .= "   KODEIGR, ";
            $query .= "   REQ_ID ";
            $query .= ") ";
            $query .= "Select '" . $KodeMember . "', ";
            $query .= "       CURRENT_DATE,  ";
            $query .= "	      CPP_NoPB, ";
            $query .= "	      CPP_TglPB, ";
            $query .= "	      CPP_PLUIDM, ";
            if($chkIDMBacaProdcrm){
                $query .= "	      KAT_PLUIGR, ";
            }else{
                $query .= "	      IDM_PLUIGR, ";
            }
            $query .= "	      'AVG.COST IS NULL', ";
            $query .= "	      PRD_KodeTag, ";
            $query .= "	      SUBSTR(PRD_DESKRIPSIPANJANG,1,60), ";
            $query .= "	      CPP_QTY, ";
            $query .= "	      null, ";
            $query .= "	      CPP_KodeToko, ";
            $query .= "	      '" . session('KODECABANG') . "', ";
            $query .= "	      '" . $ip . "' ";
            if($chkIDMBacaProdcrm){
                $query .= "	 FROM csv_pb_pot, TBMASTER_PRODMAST,KONVERSI_ATK ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT PLUIGR  ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "' ";
                $query .= "	     AND REQ_ID = '" . $ip . "'		  ";
                $query .= "		  AND NODOK = '" . $noPB . "' ";
                $query .= "		  AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		  AND PLU = CPP_PLUIDM ";
                $query .= "   )    ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT ST_AvgCost  ";
                $query .= "        FROM tbMaster_Stock  ";
                $query .= "       Where ST_PRDCD Like SUBSTR(KAT_PLUIGR,1,6)||'%' ";
                $query .= "         And ST_Lokasi = '01'  ";
                $query .= "         And ST_KodeIGR = '" . session('KODECABANG') . "'  ";
                $query .= "         And ST_AvgCost IS NOT NULL ";
                $query .= "   )    ";
                $query .= "   AND CPP_PLUIDM = KAT_PLUIDM ";
                $query .= "   AND PRD_PRDCD = KAT_PLUIGR ";
                $query .= "   AND CPP_PLUIGR = KAT_PLUIGR ";
            }else{
                $query .= "	 FROM csv_pb_pot, TBMASTER_PRODMAST,TBTEMP_PLUIDM  ";
                $query .= " WHERE CPP_IP = '" . $ip . "' ";
                $query .= "   AND CPP_KodeToko = '" . $kodeToko . "' ";
                $query .= "   AND CPP_NoPB = '" . $noPB . "' ";
                $query .= "   AND CPP_TglPB = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "   SELECT PLUIGR  ";
                $query .= "	    FROM TEMP_CETAKPB_TOLAKAN_IDM ";
                $query .= "	   WHERE KOMI = '" . $KodeMember . "' ";
                $query .= "	     AND REQ_ID = '" . $ip . "'		  ";
                $query .= "		  AND NODOK = '" . $noPB . "' ";
                $query .= "		  AND TGLDOK = to_date('" . $tglPB . "','DD-MM-YYYY') ";
                $query .= "		  AND PLU = CPP_PLUIDM ";
                $query .= "   ) ";
                $query .= "   AND NOT EXISTS ";
                $query .= "   ( ";
                $query .= "      SELECT ST_AvgCost  ";
                $query .= "        FROM tbMaster_Stock  ";
                $query .= "       Where ST_PRDCD Like SUBSTR(IDM_PLUIGR,1,6)||'%' ";
                $query .= "         And ST_Lokasi = '01'  ";
                $query .= "         And ST_KodeIGR = '" . session('KODECABANG') . "'  ";
                $query .= "         And ST_AvgCost IS NOT NULL ";
                $query .= "   )    ";
                $query .= "   AND CPP_PLUIDM = IDM_PLUIDM ";
                $query .= "   AND PRD_PRDCD = IDM_PLUIGR ";
                if($kodeDCIDM <> ""){
                    $query .= "   AND IDM_KDIDM = '" . $kodeDCIDM . "' ";
                }
            }
            DB::insert($query);

            //! AVG.COST <= 0 - 2
            $query = "";
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
            DB::insert($query);

            //! STOCK EKONOMIS POT TIDAK MENCUKUPI
            $query = "";
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
            DB::insert($query);

            //! PROGRESS => 25

            //! CHECK TEMP_CETAKPB_TOLAKAN_IDM2
            $check = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_CETAKPB_TOLAKAN_IDM2'")
                ->count();

            if($check == 0){
                //!CREATE TABLE TEMP_CETAKPB_TOLAKAN_IDM2-PRODMAST-NXQ
                $query = "";
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

                DB::insert($query);

            }else{
                //! DELETE FROM TEMP_CETAKPB_TOLAKAN_IDM2
                DB::table('temp_cetakpb_tolakan_idm2')
                    ->where('req_id', $ip)
                    ->delete();
            }

            //! INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 - 1-PRODMAST-NXQ
            $query = "";
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
            DB::insert($query);

            //! INSERT INTO TEMP_CETAKPB_TOLAKAN_IDM2 - 1-FLAGAKTIVASI-X
            $query = "";
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
            DB::insert($query);

            //! INSERT Into TEMP_CETAKPB_TOLAKAN_IDM
            DB::insert("
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
            DB::table('temp_pbidm_ready2')
                ->where('req_id', $ip)
                ->delete();

            //! INSERT INTO TEMP_PBIDM_READY2
            $query = "";
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
            DB::insert($query);

            //! PROGRESS => 30

            $data = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_PBIDM_READY'")
                ->count();

            if($data == 0){
                //! CREATE TABLE TEMP_PBIDM_READY
                DB::insert("
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
                DB::table('temp_pbidm_ready')
                    ->where('req_id', $ip)
                    ->delete();

                //! INSERT INTO TEMP_PBIDM_READY
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
            DB::update("
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
            $data = DB::table('temp_pbidm_ready')
                ->where([
                    'req_id' => $ip,
                    'fdkcab' => $kodeToko,
                    'fdnouo' => $noPB,
                ])
                ->whereRaw(" fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->where('qtyb','>',0)
                ->count();

            $AdaKarton = $data > 0 ? True : False;

            //! GET -> AdaKecil
            $data = DB::table('temp_pbidm_ready')
                ->where([
                    'req_id' => $ip,
                    'fdkcab' => $kodeToko,
                    'fdnouo' => $noPB,
                ])
                ->whereRaw(" fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->where('qtyk','>',0)
                ->count();

            $AdaKecil = $data > 0 ? True : False;

            $count = DB::table('tbtr_counterpbomi')
                ->where([
                    'cou_kodeomi' => $kodeToko,
                    'cou_kodeigr' => session('KODECABANG')
                ])
                ->count();

            if($count == 0){
                //! Insert Into tbtr_CounterPbOMI
                DB::table('tbtr_counterpbomi')->insert([
                    'cou_kodeigr' => session('KODECABANG'),
                    'cou_kodeomi' => $kodeToko,
                    'cou_tgl' => Carbon::now(),
                    'cou_nodokumen' => '',
                    'cou_create_by' => session('userid'),
                    'cou_create_dt' => Carbon::now(),
                ]);

                if($AdaKecil){
                    $CounterKecil = 1;
                }

                $CounterKarton = ($AdaKarton && $AdaKecil) ? 2 : 1;
            }

            $count = DB::table('tbtr_counterpbomi')
                ->where([
                    'cou_kodeomi' => $kodeToko,
                    'cou_kodeigr' => session('KODECABANG'),
                ])
                ->count();

            if($count >= 8){
                //! SET COU_NoDokumen = ''
                $query = "";
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
                DB::update($query);

                // If AdaKecil Then CounterKecil = 1
                // If AdaKartonan Then If AdaKecil Then CounterKarton = 2 Else CounterKarton = 1

                if($AdaKecil) $CounterKecil = 1;
                $CounterKarton = ($AdaKarton && $AdaKecil) ? 2 : 1;

            }else{

                //! SET COU_NoDokumen = RTRIM(COU_NoDokumen) + Y/YY
                $query = "";
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
                DB::update($query);

                // If AdaKecil Then CounterKecil = jum + 1
                // If AdaKartonan Then CounterKarton = CounterKecil + 1

                if($AdaKecil) $CounterKecil = (int)$data + 1;
                if($AdaKarton) $CounterKarton = (int)$CounterKecil + 1;

            }

            //! INSERT INTO TBTR_TOLAKANPBOMI
            DB::insert("
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
            $query = "";
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
            DB::update($query);

            //! MERGE INTO TBTR_TOLAKANPBOMI-PRD_KodeTag
            DB::update("
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
            DB::update("
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

            //! PROGRESS => 75

            $count = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TEMP_NOJALUR_IDM'")
                ->count();

            if($count == 0){

                //! Create Table TEMP_NOJALUR_IDM 1
                DB::insert("
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
                DB::table('temp_nojalur_idm')
                    ->where([
                        'req_id' => $ip,
                        'fdnouo' => $noPB,
                    ])
                    ->whereRaw("fdtgpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                    ->delete();

                //! INSERT INTO TEMP_NOJALUR_IDM 1
                DB::insert("
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
            DB::insert("
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
            DB::insert("
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
            DB::insert("
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
            DB::insert("
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

            $count = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TBMASTER_MARGINPLUIDM'")
                ->count();

            //! INSERT KE MASDPB BULKY
            if($count > 0){
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
                DB::insert("
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
            $PBO_NoUrut = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB,
                ])
                ->whereRaw("pbo_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->selectRaw("COALESCE(Max(pbo_nourut),1) as count")
                ->first()->count;

            $check = DB::table('information_schema.columns')
                ->whereRaw("upper(table_name) = 'TBMASTER_MARGINPLUIDM'")
                ->count();

            //! INSERT KE MASDPB PIECES
            if($check > 0){
                DB::insert("
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
                DB::insert("
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
            DB::update("
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
            DB::update("
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
            DB::update("
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
            $jumItmCSV = DB::table('csv_pb_pot')
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_kodetoko' => $kodeToko,
                    'cpp_nopb' => $noPB,
                ])
                ->whereRaw("cpp_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->count();

            //! GET -> jumTolakan
            $jumTolakan = DB::table('temp_cetakpb_tolakan_idm')
                ->where([
                    'req_id' => $ip,
                    'kcab' => $kodeToko,
                    'nodok' => $noPB,
                ])
                ->whereRaw("tgldok = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->count();

            //! dummy
            // $jumTolakan = 0;

            if((int)$jumItmCSV - (int)$jumTolakan <= 0){

                $message = "Semua Item Ditolak !!, Silahkan Cek Di TBTR_TOLAKANPBOMI | TOKO : $kodeToko, NOPB : $noPB TGLPB : $tglPB";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            //! CEK ADA YANG MASUK PBOMI GA??
            $count = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB
                ])
                ->whereRaw("pbo_tglpb = to_date('". $tglPB . "','DD-MM-YYYY')")
                ->count();

            //! dummy
            if($count == 0){
                $message = "Total Permintaan Semua Item Jumlahnya NOL (Silahkan Cek Di TBTR_TOLAKAN_PBOMI) | TOKO : $kodeToko, NOPB : $noPB TGLPB : $tglPB";
                throw new HttpResponseException(ApiFormatter::error(400, $message));
            }

            //! INSERT INTO tbtr_tolakanpbomi - TOTAL QTY ORDER 0
            DB::insert("
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
            DB::table('csv_pb_pot')
                ->where([
                    'cpp_ip' => $ip,
                    'cpp_nopb' => $noPB,
                    'cpp_kodetoko' => $kodeToko,
                ])
                ->whereNull('cpp_flag')
                ->whereRaw("cpp_tglpb = TO_DATE('" . $tglPB . "','DD-MM-YYYY')")
                ->update([
                    'cpp_flag' => '1'
                ]);

            //! GET -> rphOrder
            $rphOrder = DB::table('tbmaster_pbomi')
                ->where([
                    'pbo_kodeigr' => session('KODECABANG'),
                    'pbo_kodeomi' => $kodeToko,
                    'pbo_nopb' => $noPB,
                ])
                ->whereRaw("pbo_tglpb = to_date('" . $tglPB . "','DD-MM-YYYY')")
                ->selectRaw("sum(COALESCE(pbo_nilaiorder,0)) as sum")
                ->first()->sum;

            //! Insert Into TBTR_HEADER_POT
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
            DB::insert("
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

            DB::commit();

            return [
                'cetak_all_1' => $this->CetakALL_1($kodeToko, $ip, $noPB, $tglPB, $PersenMargin),
                'cetak_all_2' => $this->CetakALL_2($kodeToko, $ip, $noPB, $tglPB, $PersenMargin),
                'cetak_all_3' => $this->CetakALL_3($kodeToko, $ip, $noPB, $tglPB),
                'cetak_all_4' => $this->CetakALL_4($kodeToko, $ip, $noPB, $tglPB),
                'cetak_all_5' => $this->CetakALL_5($kodeToko, $ip, $noPB, $tglPB),
                'cetak_all_6' => $this->CetakALL_6($kodeToko, $ip, $noPB, $tglPB),
            ];

        } catch (HttpResponseException $e) {
            // Handle the custom response exception
            throw new HttpResponseException($e->getResponse());

        }catch(\Exception $e){

            DB::rollBack();

            $message = "Oops terjadi kesalahan ( $e )";
            throw new HttpResponseException(ApiFormatter::error(400, $message));
        }
    }
}
