@extends('layouts.app')

@section('page-header')
    <div class="row align-items-center bg-white rounded shadow p-3">
        <div class="col">
            <h3 class="page-title">Company Profile</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li class="breadcrumb-item active">Company Profile</li>
            </ul>
        </div>
        <div class="col-auto float-right ml-auto">
            <a href="/company/profile/png" class="btn btn-warning mx-2">
                <i class="fa fa-download"></i> Download Image
            </a>
            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#apply_leave">
                <i class="fa fa-plus"></i> Download Image
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">About Boxleo Courier</h5>
                        <p> Boxleo is a game-changer in the market by giving clients exactly what they want at affordable
                            pricing and with lightning-fast delivery.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Our Vision and Mission</h5>
                        <p>
                            <strong>Vision:</strong> To be the preferred and trusted courier service partner, delivering
                            excellence in logistics solutions.
                        </p>
                        <p>
                            <strong>Mission:</strong> To provide seamless and secure delivery services, exceeding customer
                            expectations through innovation and a dedicated team.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Core Values</h5>
                        <ul>
                            <li>  Reliability</li>
                            <li>  Customer Satisfaction</li>
                            <li>  Integrity</li>
                            <li>  Innovation</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Our Services</h5>
                        <p>
                            Boxleo Courier offers a range of services including:
                        </p>
                        <ul>
                            <li>Cash On Delivery</li>
                            <li>Express Courier</li>
                            <li>Order Fulfillment</li>
                            <li>Warehousing Services</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-md-6 ">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Contact Us</h5>
                        <p>
                            For inquiries and support, contact us at:
                        </p>
                        <ul>
                            <li><i class="fa fa-envelope"></i> Email: info@boxleocourier.com</li>
                            <li><i class="fa fa-phone"></i> Phone: : +254 797 750 324 </li>
                            <li><i class="fa fa-map-marker"></i> Address:Nairobi - Mombasa Road,Akshrap Godowns</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
