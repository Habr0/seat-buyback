{{-- https://github.com/eveseat/web/tree/master/src/resources/views/layouts/grids --}}
@extends('web::layouts.grids.12')

@section('title', 'Contracts')
@section('page_header', 'Contracts')

@push('head')

@endpush

@section('full')
    <div class="card">
        <div class="card-header">
            Contracts
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <table class="table table-striped" id="buyback-contracts">
                        <thead>
                        <tr>
                            <th scope="col">Issuer</th>
                            <th scope="col">Location</th>
                            <th scope="col">Code</th>
                            <th scope="col">Price</th>
                            <th scope="col">Condition</th>
                            <th scope="col">Created</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>{{ $contract['issuer']['name'] }}</td>
                                <td>{{ $contract['start_location']['name'] }}</td>
                                <td>
                                    <a
                                            href="https://janice.e-351.com/a/{{ str_replace('Buyback: ', '', $contract['title']) }}"
                                            target="_blank"
                                    >
                                        <svg style="margin-right: 4px;" xmlns="http://www.w3.org/2000/svg" width="14"
                                             height="14" fill="currentColor" class="bi bi-box-arrow-up-left"
                                             viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M7.364 3.5a.5.5 0 0 1 .5-.5H14.5A1.5 1.5 0 0 1 16 4.5v10a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 3 14.5V7.864a.5.5 0 1 1 1 0V14.5a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H7.864a.5.5 0 0 1-.5-.5"></path>
                                            <path fill-rule="evenodd"
                                                  d="M0 .5A.5.5 0 0 1 .5 0h5a.5.5 0 0 1 0 1H1.707l8.147 8.146a.5.5 0 0 1-.708.708L1 1.707V5.5a.5.5 0 0 1-1 0z"></path>
                                        </svg>
                                        {{ str_replace('Buyback: ', '', $contract['title']) }}
                                    </a>
                                </td>
                                <td>{{ number_format($contract['price'], 0, ',', '.') }} ISK</td>
                                <td>95% buy</td>
                                <td>{{ $contract['date_issued'] }}</td>
                                <td>
                                    @if($contract['status'] === 'outstanding')
                                        <span class="badge bg-primary">{{ $contract['status'] }}</span>
                                    @elseif($contract['status'] === 'finished')
                                        <span class="badge bg-success">{{ $contract['status'] }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $contract['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script>
        let table = new DataTable('#buyback-contracts')
    </script>
@endpush
