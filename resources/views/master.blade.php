<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@php echo str_replace("_", " ", env('APP_NAME')); @endphp</title>

    <link rel="stylesheet" href="fonts/all.min.css">
    <link rel="stylesheet" href="css/sb-admin-2.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/fixedColumns.bootstrap4.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="css/print.min.css">
    <link rel="stylesheet" href="css/pace-theme-default.min.css">
    <link rel="stylesheet" href="css/style-additional.css">

    @yield('css')

    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->

    <!-- <script src="../resources/js/jquery.min.js"></script> -->
    <!-- <script src="../resources/js/jquery-3.5.1.js"></script> -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <!-- <script src="../resources/js/datepicker.min.js"></script> -->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/dataTables.select.min.js"></script>
    <script src="js/datatables-demo.js"></script>
    <script src="js/dataTables.fixedColumns.min.js"></script>
    <script src="js/dataTables.fixedHeader.min.js"></script>
    <script src="js/dataTables.fixedHeader.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/chart-area-demo.js"></script>
    <script src="js/chart-pie-demo.js"></script>
    <script src="js/sweatalert.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="js/buttons.flash.min.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/pace.min.js"></script>
    <script src="js/print.min.js"></script>
    <script src="js/jquery.number.min.js"></script>


    {{-- <script src="../resources/js/jquery.number.min.js.map"></script> --}}
    <script>
        $.ajaxSetup({
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // 'Authorization': '{{session()->get('token_jwt')}}',
            }
        });
        setInterval(refreshToken, 3900000); // 65min

        function refreshToken() {
            $.get('get-csrf').done(function(data) {
                if (data != document.querySelector('meta[name="csrf-token"]').content) {
                    console.log("token expired");
                    window.location.href = "/promosi/public/login";
                } else {
                    console.log("token valid");
                }
            });
        }

        function formatRupiah(amount) {
            var rupiah = 'Rp. ' + amount.toFixed(0).replace(/(\d)(?=(\d{3})+$)/g, '$1,');
            return rupiah;
        }
    </script>
    <!-- <script>
        $(document).ready(function() {
            $("#sidebarToggle").click(function() {
                $("#accordionSidebar").toggleClass("toggled");
            });
        });
    </script> -->
    <style>
        body{
            padding: 0!important;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                @include('header')

                @yield('content')

                <!-- Modal Load-->
                <div class="modal fade" role="dialog" id="modal_loading" data-keyboard="false" data-backdrop="static" style="z-index: 2000">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body pt-0" style="background-color: #F5F7F9; border-radius: 6px;">
                            <div class="text-center">
                                <img style="border-radius: 4px; height: 140px;" src="{{ asset('img/loader_1.gif') }}" alt="Loading">
                                <h6 style="position: absolute; bottom: 10%; left: 37%;" class="pb-2">Mohon Tunggu..</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatDate(inputDateString) {
            // Parse the original date string
            var parts = inputDateString.split('-');
            var formattedDate = new Date(parts[2], parts[1] - 1, parts[0]);

            // Format the date as "Y-m-d"
            var formattedDateString = $.datepicker.formatDate('yy-mm-dd', formattedDate);

            return formattedDateString;
        }

    </script>

    @stack('page-script')
</body>

</html>
