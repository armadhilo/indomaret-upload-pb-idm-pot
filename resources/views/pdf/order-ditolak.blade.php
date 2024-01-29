<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ORDER DITOLAK</title>
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
                <div style="float: left">
                    <p><b>{{ $namaCabang }}</b></p>
                </div>
            </div>

            <div class="body">
                <div style="margin: 15px 30px 35px 30px">
                    <div style="float: left">
                        <p>Toko : {{$namaToko}} ({{$kodeToko}})</p>
                        <p>No. Order : 99999 (Dummy)</p>
                    </div>
                    <div style="float: right">
                        <p><b>LISTING ITEM ORDER PB YG DITOLAK</b></p>
                        <p>Tanggal : {{$tglPb}}</p>
                    </div>
                </div>
                <hr>
                <table border="1" style="border-collapse: collapse; margin-top:10px" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 4%">No</th>
                            <th>IDM</th>
                            <th>IGR</th>
                            <th>DESKRIPSI BARANG</th>
                            <th>UNIT</th>
                            <th>QTY</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $item->pluidm }}</td>
                                <td class="text-center">{{ $item->pluigr }}</td>
                                <td>{{ $item->desk }}</td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-center">{{ (int)$item->qty }}</td>
                                <td class="text-center">{{ $item->keterangan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
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
