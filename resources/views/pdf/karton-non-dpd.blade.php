<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KARTON NON-DPD</title>
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
                    <p style="font-size: .8rem;"><b>INDOGROSIR SEMARANG POST</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H.i.s') }}</p>
                    <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                </div>
                <hr style="margin-top: 40px">
            </div>

            <div class="body">
                <p style="text-align: center"><b>ITEM JALUR CETAKAN KE KERTAS</b></p>
                <div style="margin: 0 30px 35px 30px">
                    <div style="float: left">
                        <p>Toko : {{$namaToko}} ({{$kodeToko}})</p>
                        <p>No. Order : 99999 (Dummy)</p>
                    </div>
                    <div style="float: right">
                        <p>Tanggal : {{$tglPb}}</p>
                        <p>Batch : 6 (Dummy)</p>
                    </div>
                </div>
                <table border="1" style="border-collapse: collapse; margin-top:20px" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 4%">No</th>
                            <th>PLU</th>
                            <th>ItmRak</th>
                            <th>Nama Barang</th>
                            <th>Tag</th>
                            <th>Order</th>
                            <th>Unit</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $item->plu }}</td>
                                <td class="text-center">{{ $item->tiperak }}</td>
                                <td>{{ $item->desk }}</td>
                                <td class="text-center">{{ $item->tag }}</td>
                                <td class="text-center">{{ $item->order }}</td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-center">{{ $item->stok }}</td>
                            </tr>
                            {{-- <tr>
                                <td class="text-center">1</td>
                                <td colspan="2"><b>GROUP : 0</b></td>
                                <td class="text-center"></td>
                                <td class="text-center"><b>RAK :</b></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <p style="margin-top: 5px"># {{ count($data) }}.00 item.</p>
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
