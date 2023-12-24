@extends('master')
@section('title')
    <h4>CEK JUAL</h4>
@endsection

@section('css')
<style>
    .header{
        margin-bottom: 40px;
    }

    .header-action > *{
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
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="card shadow mb-4">
        <div class="card-body">

            </div>
        </div>
    </div>

    @push('page-script')
    <script>
        $(document).ready(function() {

        });
    </script>
    @endpush
@endsection
