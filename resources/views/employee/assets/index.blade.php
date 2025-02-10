@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center bg-white shadow rounded p-3">
        <div class="col">
            <h3 class="page-title">Assets</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">My company assets</li>
            </ul>
        </div>
        <div class="col-auto text-right ml-auto">
            <a href="" class="btn btn-info custom-btn">
                <i class="fa fa-plus"></i> Make a request
            </a>
            <a href="" class="btn btn-success custom-btn">
                <i class="fa fa-exclamation-circle"></i> Report Fault
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mt-4">
        <h4>Assigned Assets:</h4>
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0 datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asset Name</th>
                        <th>Model</th>
                        <th>Serial no</th>
                        <th>Issued on</th>
                        <th>Condtion</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->model }}</td>
                            <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->issuance_date }}</td>
                            <td>{{ $asset->condition }}</td>
                            <td>
                                <a href="" class="text-info mx-2"> <i class="la la-eye"></i></a>
                                <a href="" class="text-info mx-2"> <i class="la la-eye"></i></a>
                                <a href="" class="text-info mx-2"> <i class="la la-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No assigned assets</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
