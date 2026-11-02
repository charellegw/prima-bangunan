<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSupplier;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceSupplierRequest;
use App\Http\Requests\UpdateInvoiceSupplierRequest;

class InvoiceSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceSupplierRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceSupplier $invoiceSupplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceSupplier $invoiceSupplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceSupplierRequest $request, InvoiceSupplier $invoiceSupplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceSupplier $invoiceSupplier)
    {
        //
    }
}
