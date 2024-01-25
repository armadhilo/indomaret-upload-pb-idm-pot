@extends('master')
@section('title')
    <h1 class="pagetitle">KONVERSI PLU</h1>
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

    .detail-input{
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .detail-input label{
        white-space: nowrap;
        display: block;
        width: 110px;
        background: rgb(53,70,179);
        color: white;
        padding: 3px;
        text-align: center;
    }

    .detail-info{
        top: -14px;
        font-size: 1.1rem;
        background: white;
        font-weight: 600;
        padding: 0 5px;
        color: #012970;
    }

    @media(max-width: 1582px){
        #display_help{
            padding: 4px 9px;
            font-size: .8rem;
        }

        #plu_igr, #plu_idm{
            width: 40%!important;
        }

        #deskripsi{
            width: 69%!important;
        }

        label{
            font-size: 13px!important;
        }

        .table td{
            font-size: .9rem
        }
    }
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <table class="table table-striped table-hover datatable-dark-primary w-100" id="tb" style="margin: 20px">
                                    <thead>
                                        <tr>
                                            <th>PLU IDM</th>
                                            <th>PLU IGR</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="detail">
                                    <div class="row position-relative" style="padding: 20px 20px; border: 1px solid lightgray; margin: 20px 20px">
                                        <p style="position: absolute" class="detail-info">Detail</p>
                                        <form id="form" class="w-100">
                                            <div class="col-12">
                                                <div class="form-group detail-input">
                                                    <label for="">PLU IDM</label>
                                                    <input type="text" class="form-control" style="width: 50%" id="plu_idm" name="kat_pluidm">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group detail-input">
                                                    <label for="">PLU IGR</label>
                                                    <input type="text" class="form-control" style="width: 50%" id="plu_igr" name="kat_pluigr">
                                                    <span id="display_help" style="padding: 4px 10px; background: #E74A3B; color: white; font-weight: 700; border-radius: 3px">* F1 - HELP</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group detail-input">
                                                    <label for="">DESKRIPSI</label>
                                                    <input type="text" class="form-control" style="width: 85%" id="deskripsi" name="description" placeholder="E-PRINT PITA PRINTER REFILL 8758 BOX 12m">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group detail-input">
                                                    <label for="">AKTIF</label>
                                                    <div style="width: 25%">
                                                        <input type="checkbox" class="form-control" id="aktif" name="flag_aktif" style="width: 30px; height: 30px; cursor: pointer">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-lg btn-success float-right mt-2" style="width: 20%;">SAVE</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
           <div class="modal-content">
                <div class="modal-header br">
                    <h5 class="modal-title">Help Konversi ATK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable-dark-primary w-100" id="tb_igr" style="margin: 20px">
                            <thead>
                                <tr>
                                    <th>PLU IGR</th>
                                    <th>Deskripsi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
           </div>
        </div>
    </div>

    @push('page-script')
    <script>
        let tb;
        $(document).ready(function() {
            tb = $('#tb').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/home/datatables',
                    type: 'GET'
                },
                columnDefs: [
                    { className: 'text-center', targets: [0,1,3] },
                ],
                columns: [
                    // { data: 'DT_RowIndex',searchable: false,orderable: false },
                    { data: 'kat_pluidm' },
                    { data: 'kat_pluigr' },
                    { data: 'kat_deskripsi' },
                    { data: 'kat_flagaktif' },
                ],
                order: [],
                rowCallback: function (row, data) {
                    if(data.kat_flagaktif == 1){
                        $('td:eq(3)', row).html(`<span class="badge badge-pill badge-success">aktif</span>`);
                    } else {
                        $('td:eq(3)', row).html(`<span class="badge badge-pill badge-danger">non-aktif</span>`);
                    }
                }
            });

            let tb_igr = $('#tb_igr').DataTable({
                processing: true,
                ajax: {
                    url: '/home/igr-datatables',
                    type: 'GET'
                },
                columnDefs: [
                    { className: 'text-center', targets: [0,2] },
                ],
                columns: [
                    { data: 'pluigr' },
                    { data: 'desk' },
                    { data: null },
                ],
                rowCallback: function (row, data) {
                    $('td:eq(2)', row).html(`<button class="btn btn-info btn-sm mr-1" onclick="pilihIgr('${data.pluigr}', '${data.desk}')">Pilih IGR</button>`);
                }
            });
        });

        function pilihIgr(pluigr, desk){
            $('#modal').modal("hide");
            $('#plu_igr').val(pluigr);
            $('#deskripsi').val(desk);
        }

        $('#form').submit(function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah anda yakin ?',
                icon: 'warning',
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    $("#modal_loading").modal('show');
                    $.ajax({
                        url:  "/home/action/save",
                        type: "POST",
                        data: $('#form').serialize(),
                        success: function(response){
                            setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                            if(response.code === 200){
                                Swal.fire({
                                    title: "Success",
                                    text: response.message,
                                    icon: "success"
                                });
                                tb.ajax.reload();
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
            })
        });

        $(document).keydown(function(e){
            if(e.which === 112) {
                e.preventDefault();
                $("#modal").modal("show");
            }
        })
    </script>
    @endpush
@endsection

