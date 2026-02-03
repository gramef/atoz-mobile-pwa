<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;

class ArchivedCompanyController extends Controller
{
    public function index()
    {
        return view('companies.index', [
            'companies' => Company::select('name', 'id')->onlyTrashed()->paginate(10),
        ]);
    }

    public function update(Company $company)
    {
        $companyUsers = User::whereHas('client.organisation', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        foreach ($companyUsers as $companyUser) {
            if ($companyUser->client->hasActiveJobs()) {
                return redirect()->route('companies.index')->withErrors(["Client {$companyUser->getFullName()} has active jobs"]);
            }
            
            tap($companyUser)
                ->update(['enabled' => 0])
                ->client
                ->update(['archived' => 1]);
        }

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company archived');
    }

    public function destroy(Company $company)
    {
        $companyUsers = User::whereHas('client.organisation', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        foreach ($companyUsers as $companyUser) {
            tap($companyUser)
                ->update(['enabled' => 1])
                ->client
                ->update(['archived' => 0]);
        }

        $company->restore();

        return redirect()->route('companies.index')->with('success', 'Company restored');
    }
}
