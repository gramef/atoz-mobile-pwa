<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        return view('companies.index', [
            'companies' => Company::select('name', 'id')->filter($request->only('name'))
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(CompanyRequest $request)
    {
        Company::create($request->validated());

        return redirect()->route('companies.index')->with('success', 'Company created');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', [
            'company' => $company,
        ]);
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return back()->with('success', 'Company updated');
    }

    public function destroy(Company $company)
    {
        $company->forceDelete();

        return redirect()->route('companies.index')->with('success', 'Company deleted');
    }
}
