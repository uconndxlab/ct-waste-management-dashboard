@extends('layouts.app')

@section('title', $name . ' Overview')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const accordionHeaders = document.querySelectorAll('[data-bs-toggle="collapse"]');

        accordionHeaders.forEach(header => {
            const targetId = header.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            const icon = header.querySelector('.toggle-icon');

            targetElement.addEventListener('show.bs.collapse', function () {
                icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
            });

            targetElement.addEventListener('hide.bs.collapse', function () {
                icon.classList.replace('bi-chevron-up', 'bi-chevron-down');
            });
        });
    });
</script>
@endpush

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
                                class="list-group-item rounded accordian-header d-flex justify-content-between" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#report{{ $report->id }}"
                                aria-expanded="false"
                                aria-controls="report{{ $report->id }}"
                            >
                                <span>{{ $report->year !== '' ? 'Report for ' . $report->year : 'Report #' . $report->id . ': Year Not Specified' }}</span>
                                <i class="bi bi-chevron-down toggle-icon ml-auto"></i>
                            </a> 
                        </div>
                        <div id="report{{ $report->id }}" class="accordian-collapse collapse" >
                            <table class="table table-striped mb-4">
                                <tbody>
                                    <tr>
                                        <th>
                                            Bulky Waste 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Costs associated with the collection and disposal of large items that are too large to be accepted by the regular waste collection."></i>
                                        </th>
                                        <td>{{ $municipality->bulky_waste !== '' ? $municipality->bulky_waste : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Recycling 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Fees incurred for the collection, processing, and reuse of recyclable materials (e.g., paper, plastic)."></i>
                                        </th>
                                        <td>{{ $municipality->recycling !== '' ? $municipality->recycling : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Tipping Fees 
                                        <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Charges paid by waste haulers to dispose of waste at a landfill or waste processing facility."></i>
                                        </th>
                                        <td>{{ $municipality->tipping_fees !== '' ? $municipality->tipping_fees : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Admin Costs 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Administrative expenses related to managing waste services, including salaries of staff, office supplies, and overhead."></i>
                                        </th>
                                        <td>{{ $municipality->admin_costs !== '' ? $municipality->admin_costs : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Hazardous Waste 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Costs for the handling, transport, and disposal of waste materials that pose environmental or health risks."></i>
                                        </th>
                                        <td>{{ $municipality->hazardous_waste !== '' ? $municipality->hazardous_waste : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Contractual Services 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Payments made to private vendors for outsourced waste services such as collection, hauling, processing, or disposal."></i>
                                        </th>
                                        <td>{{ $municipality->contractual_services !== '' ? $municipality->contractual_services : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Landfill Costs 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Expenses related to the operation, maintenance, and regulatory compliance of landfills."></i>
                                        </th>
                                        <td>{{ $municipality->landfill_costs !== '' ? $municipality->landfill_costs : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Total Sanitation Refuse 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="The total volume or cost of refuse (trash, recyclables, organics, etc.) collected and managed by the sanitation department."></i>
                                        </th>
                                        <td>{{ $municipality->total_sanitation_refuse !== '' ? $municipality->total_sanitation_refuse : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Only Public Works 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Waste management costs for the town's public works department. (In some municipalities, costs are not broken down into more detailed categories.)"></i>
                                        </th>
                                        <td>{{ $municipality->only_public_works !== '' ? $municipality->only_public_works : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Transfer Station Wages 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Costs to manage transfer station where waste is temporarily held before being transported to final disposal facilities."></i>
                                        </th>
                                        <td>{{ $municipality->transfer_station_wages !== '' ? $municipality->transfer_station_wages : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Hauling Fees 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Charges for transporting waste from one location to another, such as from curbside to transfer station or landfill."></i>
                                        </th>
                                        <td>{{ $municipality->hauling_fees !== '' ? $municipality->hauling_fees : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Curbside Pickup Fees 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Costs associated with collecting waste and recyclables directly from residents' curbsides.."></i>
                                        </th>
                                        <td>{{ $municipality->curbside_pickup_fees !== '' ? $municipality->curbside_pickup_fees : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Waste Collection 
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Total costs for collecting waste from residential, commercial, or industrial locations."></i>
                                        </th>
                                        <td>{{ $municipality->waste_collection !== '' ? $municipality->waste_collection : 'No data' }}</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <!-- Edit Button -->
                                                <a href="{{ route('municipalities.report.edit', ['id' => $report->id]) }}" class="btn rounded-0 btn-primary">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <!-- Delete Button -->
                                                <form action="{{ route('municipalities.report.delete', ['name' => $name, 'reportId' => $report->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-0">
                                                        <i class="bi bi-trash"></i> Delete Report
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
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
                                    class="list-group-item rounded accordian-header d-flex justify-content-between"
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#financial{{ $financial->id }}"
                                >
                                    {{ $financial->time_period !== '' ? 'Financial Information for ' . $financial->time_period : 'Financial Info: Time Period Not Specified' }}
                                    <i class="bi bi-chevron-down toggle-icon ml-auto"></i>
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
                    <div class="card-body p-3 rounded-lg shadow-sm bg-light">
                        <div class="row">
                            @if(!empty($townInfo->contact_1) || !empty($townInfo->title_1) || !empty($townInfo->phone_1) || !empty($townInfo->email_1))
                                <div class="col-12 mb-3">
                                    <p class="fw-bold mb-1">
                                        {{ $townInfo->contact_1 }} 
                                        @if(!empty($townInfo->title_1)) 
                                            / <span class="text-primary">{{ $townInfo->title_1 }}</span>
                                        @endif
                                    </p>
                                    @if(!empty($townInfo->department))
                                        <p class="text-muted mb-2">{{ $townInfo->department }}</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-telephone text-primary"></i>
                                        <p class="mb-0">{{ $townInfo->phone_1 }}</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope text-primary"></i>
                                        <p class="mb-0"><a href="mailto:{{ $townInfo->email_1 }}" class="text-dark">{{ $townInfo->email_1 }}</a></p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($townInfo->contact_2) || !empty($townInfo->title_2) || !empty($townInfo->phone_2) || !empty($townInfo->email_2))
                                <div class="col-12 mb-3">
                                    <p class="fw-bold mb-1">
                                        {{ $townInfo->contact_2 }} 
                                        @if(!empty($townInfo->title_2)) 
                                            / <span class="text-primary">{{ $townInfo->title_2 }}</span>
                                        @endif
                                    </p>
                                    @if(!empty($townInfo->department))
                                        <p class="text-muted mb-2">{{ $townInfo->department }} Department</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-telephone text-primary"></i>
                                        <p class="mb-0">{{ $townInfo->phone_2 }}</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope text-primary"></i>
                                        <p class="mb-0"><a href="mailto:{{ $townInfo->email_2 }}" class="text-dark">{{ $townInfo->email_2 }}</a></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
      </svg>  Back to All Municipalities</a>

    <br/><br/>
@endif

@endsection
