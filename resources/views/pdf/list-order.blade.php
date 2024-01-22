<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LIST ORDER</title>
    <style>
        body{
            font-family: sans-serif;
        }
        table{
            width: 100%;
        }
        table th{
            font-size: 11px;
            background: #e9e7e7;
        }
        table td{
            font-size: 9px;
        }
        p{
            font-size: .7rem;
            font-weight: 400;
            margin: 0;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        }
        .italic {
            font-style: italic;
        }

        .inline-block-content > *{
            display: inline-block;
        }

        .title > *{
            text-align: center;
        }

        .body{
            margin-top: 20px;
        }

        .text-center{
            text-align: center;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div style="width: 100%">
            <div class="header">
                <div style="float: left;">
                    <p style="font-size: .8rem;"><b>{{ $namaCabang }}</b></p>
                    <p>Toko : {{$namaToko}} ({{$kodeToko}})</p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H.i.s') }}</p>
                    <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                </div>
            </div>

            <div class="body">
                <p style="text-align: center"><b>LISTING TRANSFER ORDER</b></p>
                <div style="margin: 12px 0;">

                    <p style="float: left">No. Order : 99999 (Dummy)</p>
                    <p style="float: right; text-align: right;">Tgl : {{$tglPb}}</p>
                </div>

                <table border="1" style="border-collapse: collapse; margin-top:20px" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 4%">No</th>
                            <th>PLU</th>
                            <th style="width: 30%">Deskripsi Barang</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Frc</th>
                            <th>In PCS</th>
                            <th>Harga</th>
                            <th>Nilai</th>
                            <th>Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $sum_nilai = 0;
                            $sum_ppn = 0;
                            $sum_total = 0;
                        @endphp

                        @foreach ($data as $item)

                            @php
                                $sum_nilai += $item->nilai;
                                $sum_ppn += $item->ppn;
                                $sum_total += $item->total;
                            @endphp

                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $item->plu }}</td>
                                <td>{{ $item->desk }}</td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-center">{{ (int)$item->frc }}</td>
                                <td class="text-center">{{ (int)$item->inpcs }}</td>
                                <td class="text-center">{{ number_format($item->nilai,0,',','.'); }}</td>
                                <td class="text-center">{{ number_format($item->ppn,0,',','.'); }}</td>
                                <td class="text-center">{{ number_format($item->total,0,',','.'); }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" style="text-align: right"><b>TOTAL :</b></td>
                            <td style="text-align: center">{{ number_format($sum_nilai,0,',','.'); }}</td>
                            <td style="text-align: center">{{ number_format($sum_ppn,0,',','.'); }}</td>
                            <td style="text-align: center">{{ number_format($sum_total,0,',','.'); }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
