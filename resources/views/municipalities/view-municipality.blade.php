@extends('layouts.app')

@section('title', $name . ' Overview')

@section('content')
    <h1 class="text-primary fw-bolder">{{ $name }} Overview</h1>
    @if(!empty($townInfo->contact_1) || !empty($townInfo->title_1) || !empty($townInfo->phone_1) || !empty($townInfo->email_1) || !empty($townInfo->department) || !empty($townInfo->contact_2) || !empty($townInfo->title_2) || !empty($townInfo->phone_2) || !empty($townInfo->email_2) || !empty($townInfo->notes))
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <p style="margin-bottom: 0;"> {{ session('success') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row w-100 align-items-stretch flex-column flex-lg-row gx-4">

        <div class="col-7">
            <!-- Expenditure Reports -->
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="subtitle-blue">Expenditure Reports</h3>
                <h4>
                    <a href="{{ route('municipalities.report.create', ['name' => $name]) }}" class="badge bg-success mb-2" style="text-decoration: none;">New </a>
                </h4>
            </div>
            @if($reports->isEmpty())
                <p class="text-muted">No reports available.</p>
            @else
                <div class="list-group accordian mb-4" id="reportAccordian">
                    @foreach($reports as $report)
                        <div class="accordian-item mb-2">
                            <a 
                                href="{{ route('municipalities.report', ['id' => $report->id]) }}" 
                                class="list-group-item rounded accordian-header" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#report{{ $report->id }}"
                            >
                                {{ $report->year !== '' ? 'Report for ' . $report->year : 'Report #' . $report->id . ': Year Not Specified' }}
                            </a>
                        </div>
                        <div id="report{{ $report->id }}" class="accordian-collapse collapse" >
                            <table class="table table-striped mb-4">
                                <tbody>
                                    <tr><th>Bulky Waste</th><td>{{ $municipality->bulky_waste !== '' ?  $municipality->bulky_waste : 'No data' }}</td></tr>
                                    <tr><th>Recycling</th><td>{{ $municipality->recycling !== '' ?  $municipality->recycling : 'No data' }}</td></tr>
                                    <tr><th>Tipping Fees</th><td>{{ $municipality->tipping_fees !== '' ?  $municipality->tipping_fees : 'No data' }}</td></tr>
                                    <tr><th>Admin Costs</th><td>{{ $municipality->admin_costs !== '' ?  $municipality->admin_costs : 'No data' }}</td></tr>
                                    <tr><th>Hazardous Waste</th><td>{{ $municipality->hazardous_waste !== '' ?  $municipality->hazardous_waste : 'No data' }}</td></tr>
                                    <tr><th>Contractual Services</th><td>{{ $municipality->contractual_services !== '' ?  $municipality->contractual_services : 'No data' }}</td></tr>
                                    <tr><th>Landfill Costs</th><td>{{ $municipality->landfill_costs !== '' ?  $municipality->landfill_costs : 'No data' }}</td></tr>
                                    <tr><th>Total Sanitation Refuse</th><td>{{ $municipality->total_sanitation_refuse !== '' ?  $municipality->total_sanitation_refuse : 'No data' }}</td></tr>
                                    <tr><th>Only Public Works</th><td>{{ $municipality->only_public_works !== '' ?  $municipality->only_public_works : 'No data' }}</td></tr>
                                    <tr><th>Transfer Station Wages</th><td>{{ $municipality->transfer_station_wages !== '' ?  $municipality->transfer_station_wages : 'No data' }}</td></tr>
                                    <tr><th>Hauling Fees</th><td>{{ $municipality->hauling_fees !== '' ?  $municipality->hauling_fees : 'No data' }}</td></tr>
                                    <tr><th>Curbside Pickup Fees</th><td>{{ $municipality->curbside_pickup_fees !== '' ?  $municipality->curbside_pickup_fees : 'No data' }}</td></tr>
                                    <tr><th>Waste Collection</th><td>{{ $municipality->waste_collection !== '' ?  $municipality->waste_collection : 'No data' }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endif

                <!-- Financial Info -->
                <h3 class="subtitle-blue">Financial Information</h3>
                @if($financials->isEmpty()) 
                    <p class="text-muted">No Financial Information Available.</p>
                @else
                    <div class="list-group accordian mb-4" id="financialAccordian">
                        @foreach($financials as $financial)
                            <div class="accordian-item mb-2">
                                <a
                                    href="{{ route('municipalities.financials', ['municipality' => $name]) }}" 
                                    class="list-group-item rounded accordian-header"
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#financial{{ $financial->id }}"
                                >
                                    {{ $financial->time_period !== '' ? 'Financial Information for ' . $financial->time_period : 'Financial Info: Time Period Not Specified' }}
                                </a>
                            </div>
                            <div id="financial{{ $financial->id }}" class="accordian-collapse collapse" >
                                <table class="table table-striped">
                                    <tbody>
                                        <tr><th>Time Period</th><td>{{ $financialData->time_period == "" ? 'No data' : $financialData->time_period }}</td></tr>
                                        <tr><th>Town Population (2022)</th><td>{{ $financialData->population == "" ? 'No data' : number_format($financialData->population) }}</td></tr>
                                        <tr><th>Town Size (Square Miles, 2010)</th><td>{{ $financialData->size == "" ? 'No data' : $financialData->size }}</td></tr>
                                        <tr><th>Link to Town Budget</th><td><a href="{{ $financialData->link == "" ? '#' : $financialData->link }}" target="_blank">View Town Budget</a></td></tr>
                                        <tr><th>Notes</th><td>{{ $financialData->notes == "" ? 'No additional notes' : $financialData->notes }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            @endforeach
                    </div>
                @endif
        </div>
        <div class="col-5">
            <div class="row">
                <h4 class="d-flex flex-start bg-transparent border-0 w-100 subtitle-blue">
                    Town Indexes & Information
                </h4>
                <div class="card mb-4 card-body">
                    @if(isset($townClassification))
                        <div class="flex-row w-100">
                            <div>
                                <p class="mb-1"><strong>Region Type:</strong> {{ $townClassification->region_type }}</p>
                                <p class="mb-1"><strong>Geographical Region:</strong> {{ $townClassification->geographical_region }}</p>
                                <p class="mb-1"><strong>County:</strong> {{ $townClassification->county }}</p>
                            </div>  
                            
                        </div>
                    @else
                        <p class="text-muted">No classification information available.</p>
                    @endif
                </div>
                <h4 class="d-flex flex-start w-100 subtitle-blue">
                    Town Contact Information
                </h4>
                <div class="card mb-4">
                    <div class="card-body ">
                        <div class="row">
                            @if(!empty($townInfo->department))
                                <div class="col-12">
                                    <p><strong>Department:</strong> {{ $townInfo->department }}</p>
                                </div>
                            @endif
            
                            @if(!empty($townInfo->contact_1) || !empty($townInfo->title_1) || !empty($townInfo->phone_1) || !empty($townInfo->email_1))
                            <div class="col-md-6">
                                <h5>Primary Contact</h5>
                                @if(!empty($townInfo->contact_1)) 
                                    <p><strong>Name:</strong> {{ $townInfo->contact_1 }}</p>
                                @endif
                                @if(!empty($townInfo->title_1)) 
                                    <p><strong>Title:</strong> {{ $townInfo->title_1 }}</p>
                                @endif
                                @if(!empty($townInfo->phone_1)) 
                                    <p><strong>Phone:</strong> {{ $townInfo->phone_1 }}</p>
                                @endif
                                @if(!empty($townInfo->email_1)) 
                                    <p><strong>Email:</strong> <a href="mailto:{{ $townInfo->email_1 }}">{{ $townInfo->email_1 }}</a></p>
                                @endif
                            </div>
                            @endif
            
                            @if(!empty($townInfo->contact_2) || !empty($townInfo->title_2) || !empty($townInfo->phone_2) || !empty($townInfo->email_2))
                            <div class="col-md-6">
                                <h5>Secondary Contact</h5>
                                @if(!empty($townInfo->contact_2)) 
                                    <p><strong>Name:</strong> {{ $townInfo->contact_2 }}</p>
                                @endif
                                @if(!empty($townInfo->title_2)) 
                                    <p><strong>Title:</strong> {{ $townInfo->title_2 }}</p>
                                @endif
                                @if(!empty($townInfo->phone_2)) 
                                    <p><strong>Phone:</strong> {{ $townInfo->phone_2 }}</p>
                                @endif
                                @if(!empty($townInfo->email_2)) 
                                    <p><strong>Email:</strong> <a href="mailto:{{ $townInfo->email_2 }}">{{ $townInfo->email_2 }}</a></p>
                                @endif
                            </div>
                            @endif
                        </div>
            
                        @if(!empty($townInfo->notes) || !empty($townInfo->other_useful_notes))
                            <h5>Additional Notes</h5>
                            @if(!empty($townInfo->notes)) 
                                <p>{{ $townInfo->notes }}</p>
                            @endif
                            @if(!empty($townInfo->other_useful_notes)) 
                                <p>{{ $townInfo->other_useful_notes }}</p>
                            @endif
                        @endif
                        <h5><a href="{{ route('municipalities.edit', ['name' => $name]) }}" class="badge bg-success" style="text-decoration: none;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>  Edit</a></h5>
                        
                        @else
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    Town Contact Information
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">No contact information available.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
      </svg>  Back to All Municipalities</a>

    <br/><br/>

@endsection
