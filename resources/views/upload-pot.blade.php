@extends('master')
@section('title')
    <h1 class="pagetitle">Upload PB POT</h1>
@endsection

@section('css')
<style>
    .detail{
        margin-bottom: 40px;
        margin-left: 30px;
    }

    .detail-action > *{
        width: unset;
        display: inline-block;
    }

    .btn-warning{
        box-shadow: 0 2px 6px #ffc473;
        background: #ffa426;
        color: white;
        font-size: 15px;
    }

    .btn-warning:hover{
        color: white;
    }

    .table tbody tr.deactive td{
        background-color: #ffb6c19e;
    }

    .btn-success{
        box-shadow: 0 2px 6px #81d694;
        background-color: #47c363;
        border-color: #47c363;
        color: white;
        font-size: 13px;
    }

    .table tbody tr.deactive td{
        background-color: #ffb6c19e;
    }

    .select-r td {
        background-color: #566cfb !important;
        color: white!important;
    }

    .header-input{
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-input label{
        white-space: nowrap;
        display: block;
        width: 210px;
        background: #bb7f12;
        color: white;
        padding: 7px 10px;
        text-align: center;
        border-radius: 5px;
    }

    .header-input input{
        width: 40%;
    }

    #tb_pba tbody tr{
        cursor: pointer;
    }

    #loading_datatable{
        opacity: .85!important;
    }
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>
    <div class="container-fluid blur-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="header">
                            <div class="form-group header-input">
                                <label for="dbf-input">PATH FILE PBA* .DBF</label>
                                <input type="file" id="dbf-input" class="form-control" multiple webkitdirectory directory style="height: 47px;">
                                <span style="padding: 4px 10px; background: #7F1910; color: white; font-weight: 700; border-radius: 3px; height: 32px">* F3 - TARIK DATA PB A</span>
                                <span style="padding: 4px 10px; background: #7F1910; color: white; font-weight: 700; border-radius: 3px; height: 32px">* F8 - UPLOAD PB A</span>
                            </div>
                            <div class="form-group header-input">
                                <label for="csv-input">PATH FILE PBPOT* .CSV</label>
                                <input type="file" id="csv-input" class="form-control" style="height: 47px;">
                            </div>
                        </div>
                        <div class="body">
                            <div class="position-relative">
                                <table class="table table-striped table-hover datatable-dark-primary table-center" id="tb_pba" style="margin-top: 20px">
                                    <thead>
                                        <tr>
                                            <th>No. PB</th>
                                            <th>TGL PB</th>
                                            <th>TOKO</th>
                                            <th>ITEM</th>
                                            <th>RUPIAH</th>
                                            <th>NAMA FILE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <button class="btn btn-lg btn-primary d-none" id="loading_datatable" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </button>

                            </div>

                            <div class="position-relative mt-5">
                                <table class="table table-striped table-hover table-center datatable-dark-primary" id="tb_plu" style="margin-top: 20px">
                                    <thead>
                                        <tr>
                                            <th>PLU</th>
                                            <th>DESKRIPSI</th>
                                            <th>QTY</th>
                                            <th>STOCK EKONOMIS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <button class="btn btn-lg btn-primary d-none" id="loading_datatable_detail" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" type="button" disabled>
                                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                </button>
                            </div>
                        </div>

                        <div class="footer">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal_login" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
           <div class="modal-content">
                <div class="modal-header br">
                    <h5 class="modal-title">LOGIN UPLOAD PB IDM</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Username</label>
                        <input type="text" class="form-control" name="username" id="username">
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group float-right">
                        <button class="btn btn-secondary mr-2" onclick="window.location.href='/home'" style="width: 95px; height: 40px">Cancel</button>
                        <button class="btn btn-success" onclick="login_pb()" style="width: 95px; height: 40px">Login</button>
                    </div>
                </div>
           </div>
        </div>
    </div>

    @push('page-script')

    <script>
        let tb_pba;
        let tb_plu;
        let selectedRowData;
        function initializeDatatables(){
            tb_pba = $('#tb_pba').DataTable({
                language: {
                    emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data PBA</div>",
                },
                columns: [
                    { data: 'nopb'},
                    { data: 'tglpb'},
                    { data: 'toko'},
                    { data: 'item'},
                    { data: 'rupiah'},
                    { data: 'nama_file'},
                ],
                columnDefs: [
                    { className: 'text-center-vh', targets: '_all' },
                ],
                data: [],
                rowCallback: function(row, data){
                    $(row).dblclick(function() {
                        $('#tb_pba tbody tr').removeClass('select-r');
                        $(this).addClass("select-r");
                        selectedRowData = data;
                        showDatatableDetail(data.toko);
                    });
                },
            });

            tb_plu = $('#tb_plu').DataTable({
                language: {
                    emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Detail PB</div>",
                },
                columns: [
                    { data: 'plu'},
                    { data: 'desk'},
                    { data: 'qty'},
                    { data: 'stock'},
                ],
                columnDefs: [
                    { className: 'text-center-vh', targets: '_all' },
                ],
                data: [],
                rowCallback: function(row, data){
                },
            });

        }
        $(document).ready(function(){
            $('#modal_login').modal("show");
            $('body').addClass('modal-blur');
            initializeDatatables();
        });

        function uploadPBA(){
            if(selectedRowData === undefined || selectedRowData === null || selectedRowData === ''){
                Swal.fire({
                    title: 'Peringatan..!',
                    text: 'Harap Pilih PBA Terlebih Dahulu',
                    icon: 'warning',
                });
                return;
            }
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah anda yakin ingin Upload PBA?',
                icon: 'warning',
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    let noPB = selectedRowData.nopb;
                    let KodeToko = selectedRowData.toko;
                    let tglPB = selectedRowData.tglpb;
                    let namaFile = selectedRowData.nama_file;
                    $("#modal_loading").modal("show");
                    $.ajax({
                        url: "/upload-pot/prosesPBIDM?noPB=" + encodeURIComponent(noPB) + "&KodeToko=" + encodeURIComponent(KodeToko) + "&tglPB=" + encodeURIComponent(tglPB) + "&namaFile=" + encodeURIComponent(namaFile),
                        type: "GET",
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            setTimeout(function () { $("#modal_loading").modal("hide"); }, 500);
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success"
                            }).then(function(){
                                window.open('/upload-pot/download-excel/' + response.data, '_blank');
                            });

                        }, error: function(jqXHR, textStatus, errorThrown) {
                            setTimeout(function () { $("#modal_loading").modal("hide"); }, 500);
                            Swal.fire({
                                text: jqXHR.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }

        function showDatatableDetail(toko){
            tb_plu.clear().draw();
            $('.datatable-no-data').css('color', '#F2F2F2');
            $('#loading_datatable_detail').removeClass('d-none');
            $.ajax({
                url: `/upload-pot/datatables-detail/${toko}`,
                type: "GET",
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#loading_datatable_detail').addClass('d-none');
                    $('.datatable-no-data').css('color', '#ababab');
                    tb_plu.rows.add(response.data).draw();
                }, error: function(jqXHR, textStatus, errorThrown) {
                    setTimeout(function () { $('#loading_datatable_detail').addClass('d-none'); }, 500);
                    $('.datatable-no-data').css('color', '#ababab');
                    Swal.fire({
                        text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                }
            });
        }

        function showDatatablesHead(){
            tb_pba.clear().draw();
            tb_plu.clear().draw();
            $('.datatable-no-data').css('color', '#F2F2F2');
            $('#loading_datatable').removeClass('d-none');
            $('#loading_datatable_detail').removeClass('d-none');
            $.ajax({
                url: "/upload-pot/datatables-head",
                type: "GET",
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#loading_datatable').addClass('d-none');
                    $('.datatable-no-data').css('color', '#ababab');
                    tb_pba.rows.add(response.data).draw();
                    $('#tb_pba tbody tr:first').dblclick();
                }, error: function(jqXHR, textStatus, errorThrown) {
                    setTimeout(function () { $('#loading_datatable').addClass('d-none'); }, 500);
                    $('#loading_datatable_detail').addClass('d-none');
                    $('.datatable-no-data').css('color', '#ababab');
                    Swal.fire({
                        text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                }
            });
        }


        function uploadDBF(){
            let input = $('#dbf-input');
            if(input.val() === null || input.val() === ''){
                Swal.fire({
                    text: "Mohon Isi File Path DBF Terlebih Dahulu",
                    icon: "error"
                });
                return;
            }
            let files = input[0].files;
            let formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            $("#modal_loading").modal("show");
            $.ajax({
                url: "/upload-pot/readDbf",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    if(response.code === 200){
                        Swal.fire({
                            text: response.message,
                            icon: "success"
                        }).then(function(){
                            showDatatablesHead();
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire({
                        text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                            ? jqXHR.responseJSON.message
                            : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                }
            });
        };

        $(document).keydown(function(e) {
            // Check if the pressed key is F3 (key code 114)
            if (e.which === 114 || e.keyCode === 114) {
            // Call your function here
                e.preventDefault();
                uploadDBF();
            }

            // Check if the pressed key is F8 (key code 114)
            else if (e.which === 119 || e.keyCode === 119) {
                e.preventDefault();
                uploadPBA();
            }

        });

        function login_pb(){
            console.log('test');
            $("#modal_loading").modal("show");
            $.ajax({
                url:  "/upload-pot/check-login",
                type: "POST",
                data: { user: $('#username').val(), password: $('#password').val() },
                success: function(response){
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    if(response.code === 200){
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success"
                        });
                        $("#modal_login").modal('hide');
                        $(".blur-container").removeClass("blur-container");
                    } else {
                        Swal.fire({
                            title: "error",
                            text: "Username atau Password Salah",
                            icon: "error"
                        });
                    }
                },error: function (jqXHR, textStatus, errorThrown){
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire({
                        text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                            ? jqXHR.responseJSON.message
                            : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                }
            })
        }
    </script>
    @endpush
@endsection

