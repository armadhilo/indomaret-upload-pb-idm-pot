@extends('master')
@section('title')
    <h1 class="pagetitle">Upload POT</h1>
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

    .selected-row td {
        background-color: #0076ffa1 !important;
        color: white;
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
                                <span style="padding: 4px 10px; background: #7F1910; color: white; font-weight: 700; border-radius: 3px; height: 32px">* F3 - LOAD PBA</span>
                            </div>
                            <div class="form-group header-input">
                                <label for="csv-input">PATH FILE PBPOT* .CSV</label>
                                <input type="file" id="csv-input" class="form-control" style="height: 47px;">
                            </div>
                        </div>
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
                                <tr>
                                    <td colspan="6" class="text-center text-secondary">Data Masih Kosong</td>
                                </tr>
                            </tbody>
                        </table>

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
                                <tr>
                                    <td colspan="6" class="text-center text-secondary">Data Masih Kosong</td>
                                </tr>
                            </tbody>
                        </table>
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
        $(document).ready(function(){
            // $('#modal_login').modal("show");
        });

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
                        $('#tb_pba tbody').empty();
                        response.data.forEach(item => {
                            let newRow = '<tr>' +
                                '<td>' + item.no_pb + '</td>' +
                                '<td>' + item.tgl_pb + '</td>' +
                                '<td>' + item.toko + '</td>' +
                                '<td>' + item.item + '</td>' +
                                '<td>' + formatRupiah(item.rupiah) + '</td>' +
                                '<td>' + item.nama_file + '</td>' +
                            '</tr>';

                            $('#tb_pba tbody').append(newRow);
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: "error"
                        });    
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire({
                        text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
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
        });

        function login_pb(){
            $("#modal_loading").modal("show");
            $.ajax({
                url:  "/upload-pot/check-login",
                type: "POST",
                data: { username: $('#username'), password: $('#password') },
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
                        text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                }
            })
        }
    </script>
    @endpush
@endsection

