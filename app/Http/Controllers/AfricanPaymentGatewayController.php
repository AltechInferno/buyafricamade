<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AfricanPaymentGatewayController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:african_pg_configuration'])->only('configuration');
        $this->middleware(['permission:african_pg_credentials_configuration'])->only('credentials_index');
    }

    public function configuration()
    {
        return view('african_pg.configurations.activation');
    }

    public function credentials_index()
    {
        return view('african_pg.configurations.index');
    }
}
