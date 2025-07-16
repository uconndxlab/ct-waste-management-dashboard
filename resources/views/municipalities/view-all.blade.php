@extends('layouts.app')

@section('title', 'Municipalities')

@section('content')
    <h1 class="text-primary">All Municipalities</h1>

    <form action="{{ route('municipalities.all') }}" method="GET" class="mb-4 mt-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search Municipality" value="{{ request('search') }}">
 
            @php
                $filters = [
                    'region_type' => $regionType,
                    'geographical_region' => $geographicalRegion,
                    'county' => $county,
                    'letter' => $selectedLetter,
                ];
            @endphp

            @foreach($filters as $key => $value)
                @if($value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>

    <div class="mb-3">
        <strong>Search by Letter: </strong>
        <a href="{{ route('municipalities.all', array_merge(request()->except('letter'), ['letter' => null])) }}" 
        class="btn btn-sm {{ !$selectedLetter ? 'btn-primary' : 'btn-outline-primary' }}">
            All
        </a>
        @foreach($letters as $letter)
            <a href="{{ route('municipalities.all', array_merge(request()->except('letter'), ['letter' => $letter])) }}" 
            class="btn btn-sm {{ $selectedLetter == $letter ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $letter }}
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-start mb-3">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseContent" aria-expanded="false" aria-controls="filterCollapseContent">
            Filter Options <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
        </button>

        <form id="compare-form" action="{{ route('municipalities.compare') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" id="compare-button" class="btn btn-primary ms-3" disabled>Compare</button>
        </form>
    </div>

    <div class="collapse mb-3" id="filterCollapseContent">
        <div class="card card-body">
            <div class="d-flex flex-row">
                <div class="d-flex align-items-center gap-4 w-100">
                    <strong>Filter by Index: </strong>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $regionType ? $regionType : 'Region Type' }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('region_type'), ['region_type' => null])) }}">All</a></li>
                            @foreach ($regionTypes as $type)
                                <li><a class="dropdown-item" href="{{ route( 'municipalities.all', array_merge(request()->except('region_type'), ['region_type' => $type])) }}"> {{ $type }}</a></li>  
                            @endforeach
                        </ul>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $geographicalRegion ? $geographicalRegion : 'Geographical Region' }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('geographical_region'), ['geographical_region' => null])) }}"> All </a></li>
                            @foreach ($geographicalRegions as $region)
                                <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('geographical_region'), ['geographical_region' => $region])) }}"> {{ $region }} </a></li>
                            @endforeach

                        </ul>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             {{ $county ? $county : 'County'  }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('county'), ['county' => null])) }}"> All </a></li>
                            @foreach ($counties as $c)
                                 <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('county'), ['county' => $c])) }}"> {{ $c }} </a></li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($regionType || $geographicalRegion || $county) 
                        <a href="{{ route('municipalities.all', array_intersect_key(request()->all(), array_flip(['letter', 'search']))) }}" class="btn btn-outline-danger btn-sm">Clear Filters</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="compare-form" action="{{ route('municipalities.compare') }}" method="POST">
        @csrf

        <div class="list-group">
            @foreach($municipalities as $municipality)
                <div class="list-group-item list-group-item-action municipality-item" data-name="{{ $municipality->name }}">
                    {{ $municipality->name }}
                </div>
            @endforeach
        </div>
    </form>

    <script>
        const items = document.querySelectorAll('.municipality-item');
        const compareButton = document.getElementById('compare-button');
        const compareForm   = document.getElementById('compare-form');
        let selected = [];

        function rebuildHiddenInputs() {
            // remove old dynamic inputs
            compareForm.querySelectorAll('input[name="municipalities[]"]').forEach(i => i.remove());
            // add one hidden input per selected municipality
            selected.forEach(name => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'municipalities[]';
                inp.value = name;
                compareForm.appendChild(inp);
            });
        }

        items.forEach(item => {
            item.addEventListener('click', () => {
                const name = item.dataset.name;
                if (selected.includes(name)) {
                    selected = selected.filter(n => n !== name);
                    item.classList.remove('active');
                } else if (selected.length < 2) {
                    selected.push(name);
                    item.classList.add('active');
                }

                rebuildHiddenInputs();
                compareButton.disabled = selected.length !== 2;
            });
        });
    </script>

    <style>
        .municipality-item {
            cursor: pointer;
        }
        .municipality-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>

    @if(request('search'))
        @if($municipalities->isEmpty())
        <p class="text-muted">No Results Found</p>
        @endif
    <br/>
    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
      </svg> View All Municipalities</a>
    @endif

@endsection
