<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CompanyController extends Controller
{
    public function show(Company $company)
    {
        return Response::json(
            $company->only([
                'id',
                'name',
                'email',
                'vat_number',
                'company_number',
                'address_line_1',
                'address_line_2',
                'county',
                'postcode'
            ])
        );
    }
}