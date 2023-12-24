<?php

namespace App\Http\Controllers;

use App\Helper\DatabaseConnection;
use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use File;
use Illuminate\Queue\Connectors\DatabaseConnector;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
    public function index(){

        //Apakah anda yakin ingin mengupdate harga jual ke seluruh kasir?
        //Apakah anda yakin ingin mengupdate harga jual ke SPI?
        //Apakah anda yakin ingin mengupdate harga jual ke KlikIGR?
        return view('');
    }
}
