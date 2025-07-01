{{-- https://github.com/eveseat/web/tree/master/src/resources/views/layouts/grids --}}
@extends('web::layouts.grids.12')

@section('title', 'Appraisal')
@section('page_header', 'Appraisal')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('full')
<div class="row text-md">
    <div class="col-md-5">
        <div class="row" style="align-items: center;">
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-balance-scale-left"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Default Exchange Rate</span>
                        <span class="info-box-number">{{ $priceModifier->modifier * 100 }}% {{ $settings['base_price'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-map-pin"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Allowed Systems</span>
                        <span class="info-box-number">
                        @foreach($allowedSystems as $system)
                            <span class="badge bg-secondary">{{ $system['name'] }}</span>
                        @endforeach
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div x-data="appraisalComponent()" class="row text-md">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                Get Appraisal
            </div>
            <form @submit.prevent="submitForm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Your trash</label>
                                <textarea x-model="trash" class="form-control" rows="7" placeholder="Plex 50"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="loading" style="display: none;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span x-show="!loading">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <template x-if="price">
        <div class="col-md-1" style="display: flex; flex-direction: column; justify-content: center">
            <svg fill="#bdbdbd" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120px" height="120px" viewBox="0 0 532.16 532.16" xml:space="preserve" stroke="#bdbdbd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M355.349,266.078L230.464,461.539c-13.648,21.364-7.393,49.743,13.966,63.391c7.656,4.89,16.212,7.228,24.67,7.228 c15.141,0,29.963-7.491,38.721-21.193l140.675-220.173c9.626-15.067,9.626-34.358,0-49.425L307.821,21.192 C294.173-0.172,265.789-6.421,244.43,7.227c-21.365,13.647-27.614,42.032-13.966,63.391L355.349,266.078z"></path> <path d="M122.305,532.157c15.141,0,29.964-7.491,38.721-21.193l140.674-220.173c9.627-15.067,9.627-34.358,0-49.425 L161.026,21.192C147.373-0.172,118.995-6.421,97.636,7.227C76.271,20.874,70.022,49.259,83.67,70.618l124.885,195.46 L83.67,461.539c-13.648,21.364-7.393,49.743,13.966,63.391C105.292,529.825,113.848,532.157,122.305,532.157z"></path> </g> </g> </g></svg>
        </div>
    </template>
    <template x-if="price">
        <div class="col-md-5" style="display: flex; flex-direction: column; justify-content: center">

            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Contract Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Breakdown</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                            <div class="row">
                                <div class="col">
                                    <p class="text-md">{!! $settings['instruction'] !!}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col form-group">
                                    <label>I will receive</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" :value="price" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col form-group">
                                    <label>Description</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" :value="`Buyback: ${code}`" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                            <table class="table table-striped mt-4">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Modifier</th>
                                    <th class="text-right">Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template x-for="item in items" :key="item.typeId">
                                    <tr>
                                        <td>
                                            <img :src="'https://images.evetech.net/types/' + item.typeId + '/icon?size=32'">
                                            <span class="ml-2" x-text="item.name"></span>
                                        </td>
                                        <td class="text-right" x-text="item.quantity"></td>
                                        <td class="text-right" x-text="item.modifier.modifier"></td>
                                        <td class="text-right" x-text="Math.floor(item.modifiedPrice).toLocaleString('de-DE')"></td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a :href="`https://janice.e-351.com/a/${code}`" target="_blank" class="text-decoration-none" style="display: flex; align-items: center;">
                        <svg style="margin-right: 4px;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-box-arrow-up-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M7.364 3.5a.5.5 0 0 1 .5-.5H14.5A1.5 1.5 0 0 1 16 4.5v10a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 3 14.5V7.864a.5.5 0 1 1 1 0V14.5a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H7.864a.5.5 0 0 1-.5-.5"></path>
                            <path fill-rule="evenodd" d="M0 .5A.5.5 0 0 1 .5 0h5a.5.5 0 0 1 0 1H1.707l8.147 8.146a.5.5 0 0 1-.708.708L1 1.707V5.5a.5.5 0 0 1-1 0z"></path>
                        </svg>
                        Check on Janice
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
@stop

@push('javascript')
    <script src="{{ asset('web/js/alpine.min.js') }}" defer></script>
    <script>
        function appraisalComponent() {
            return {
                trash: '',
                loading: false,
                price: null,
                code: null,
                items: null,
                async submitForm() {
                    this.loading = true;
                    try {
                        const response = await fetch('/buyback/appraisal', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ trash: this.trash }),
                        });

                        if (!response.ok) {
                            throw new Error('Server error');
                        }

                        const result = await response.json();

                        this.price = Math.floor(result.price).toLocaleString('de-DE');
                        this.code = result.code || 'xxx';
                        this.items = result.items;
                    } catch (error) {
                        this.result = 'Error: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endpush
