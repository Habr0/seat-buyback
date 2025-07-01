@php use Habr0\Buyback\Enums\BasePriceEnum; @endphp
{{-- https://github.com/eveseat/web/tree/master/src/resources/views/layouts/grids --}}
@extends('web::layouts.grids.4-8')

@section('title', 'Admin')
@section('page_header', 'Admin')

@push('head')
    <style>
        .text-sm .select2-container--default .select2-selection--multiple,
        .text-sm .select2-container--default .select2-selection--single {
            min-height: calc(2.25rem + 2px);
        }

        .text-sm .select2-container--default .select2-selection--single .select2-selection__rendered {
            margin-top: -.2rem;
        }

        .text-sm .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('left')
    <div class="card">
        <div class="card-header">
            Settings
        </div>
        <form method="post" action="/buyback/admin/save">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group mb-4">
                            <label>
                                Base Price
                                <span style="font-weight: 400; display: block;">Market order type to base estimation on</span>
                            </label>
                            <select name="basePrice" class="form-control custom-select">
                                <option {{ $settings['base_price'] === BasePriceEnum::BUY ? 'selected' : '' }} value="{{ BasePriceEnum::BUY->value }}">
                                    Buy
                                </option>
                                <option {{ $settings['base_price'] === BasePriceEnum::SPLIT ? 'selected' : '' }} value="{{ BasePriceEnum::SPLIT->value }}">
                                    Split
                                </option>
                                <option {{ $settings['base_price'] === BasePriceEnum::SELL ? 'selected' : '' }} value="{{ BasePriceEnum::SELL->value }}">
                                    Sell
                                </option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label>
                                Default Price Modifier
                                <span style="font-weight: 400; display: block;">Decimal number. For 95% Jita, type: 0.95</span>
                            </label>
                            <input name="defaultPriceModifier" type="number" step="0.01" class="form-control"
                                   value="{{ $defaultPriceModifier->modifier }}">
                        </div>

                        <div class="form-group mb-4">
                            <label>
                                Allowed Systems
                                <span style="font-weight: 400; display: block;">Systems in which you want to accept contracts</span>
                            </label>
                            <span class="text-md">
                                 <select name="allowedSystems[]" class="form-control select2" id="allowed_systems" multiple="multiple"
                                         style="width: 100%;">
                                     @foreach($allowedSystems as $system)
                                         <option value="{{ $system['system_id'] }}" selected>{{ $system['name'] }}</option>
                                     @endforeach
                                 </select>
                            </span>
                            <span x-html="allowedSystems"></span>
                        </div>

                        <div class="form-group">
                            <label>
                                Instruction
                                <span style="font-weight: 400; display: block;">Shown to user to guide contract creation</span>
                            </label>
                            <textarea name="instruction" class="form-control"
                                      rows="3">{{ $settings['instruction'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                <span style="display: none;" class="spinner-border spinner-border-sm" role="status"
                      aria-hidden="true"></span>
                    <span>Save</span>
                </button>
                @if(session('saved'))
                    <span id="settings-saved" class="text-success ml-4">{{ session('saved') }}!</span>
                @endif
            </div>
        </form>
    </div>
@stop

@section('right')
    <div class="card" x-data="marketGroupsComponentData()" x-init="marketGroupsComponentInit">
        <div class="card-header">
            Market Groups
        </div>
        <div class="card-body">
            <form @submit.prevent="storeMarketGroupModifier">
                <div class="row">
                    <div class="col form-group">
                        <label>
                            Market Group
                        </label>
                        <span class="text-md">
                        <select x-ref="select" name="marketGroup" class="form-control custom-select" style="width: 100%;">
                        <option></option>
                        @foreach($marketGroupsTree as $id => $marketGroup)
                            <option value="{{ $id }}">{{ $marketGroup }}</option>
                        @endforeach
                        </select>
                    </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-3 form-group">
                        <label>
                            Price Modifier
                        </label>
                        <input name="priceModifier" type="number" step="0.01" class="form-control" x-model="priceModifier">
                    </div>
                    <div class="col form-group" style="display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start;">
                        <span x-show="loading" style="display: none; margin-bottom: .7rem;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <button x-show="!loading" type="submit" class="btn btn-success">Add</button>
                    </div>
                </div>
            </form>

            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Market Group</th>
                        <th>Modifier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr x-show="!marketGroupModifiers.length" style="display: none;">
                        <td colspan="2">No Modifiers</td>
                    </tr>
                    <template x-for="modifier in marketGroupModifiers" :key="modifier.group_id">
                        <tr>
                            <td>
                                <span x-text="marketGroupsTree[modifier.group_id]"></span>
                            </td>
                            <td>
                                <form @submit.prevent="updateMarketGroupModifier(modifier.group_id, modifier.modifier)">
                                    <span class="input-group">
                                        <span class="input-group input-group-sm">
                                            <input type="hidden" name="groupId" :value="modifier.group_id">
                                            <input class="form-control" name="priceModifier" type="number" step="0.01" x-model="modifier.modifier">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-success btn-flat">Save</button>
                                                <button type="button" @click="deleteMarketGroupModifier(modifier.group_id, modifier.modifier)" class="btn btn-danger btn-flat"><i class="fas fa-trash-alt"></i></button>
                                            </span>
                                        </span>
                                    </span>
                                </form>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('javascript')
    <script src="{{ asset('web/js/alpine.min.js') }}" defer></script>
    <script>
        function marketGroupsComponentInit() {
            this.select2 = $(this.$refs.select).select2({
                minimumInputLength: 3
            });
            this.select2.on("select2:select", (event) => {
                this.newMarketGroupId = event.target.value;
            });
        }

        function marketGroupsComponentData() {
            return {
                newMarketGroupId: 0,
                priceModifier: 0.95,
                marketGroupModifiers: {!! json_encode($marketGroupModifiers) !!},
                loading: false,
                marketGroupsTree: {!! json_encode($marketGroupsTree) !!},
                async getModifier() {
                    try {
                        const response = await fetch('/buyback/admin/price-modifier', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Server error');
                        }

                        this.marketGroupModifiers = await response.json();
                    } catch (error) {
                        this.result = 'Error: ' + error.message;
                    }
                },
                async updateMarketGroupModifier(groupId, modifier) {
                    try {
                        const response = await fetch('/buyback/admin/price-modifier', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                marketGroup: groupId,
                                priceModifier: modifier,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Server error');
                        }

                        await this.getModifier()
                    } catch (error) {
                        this.result = 'Error: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                },
                async deleteMarketGroupModifier(groupId) {
                    try {
                        const response = await fetch('/buyback/admin/price-modifier', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                marketGroup: groupId,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Server error');
                        }

                        await this.getModifier()
                    } catch (error) {
                        this.result = 'Error: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                },
                async storeMarketGroupModifier() {
                    this.loading = true;
                    try {
                        const response = await fetch('/buyback/admin/price-modifier', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                marketGroup: this.newMarketGroupId,
                                priceModifier: this.priceModifier
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Server error');
                        }

                        await this.getModifier()
                    } catch (error) {
                        this.result = 'Error: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        // Hide Save Notification
        $(function() {
            $('#settings-saved').delay(2000).hide(300);
        })

        // System Multi Select
        $('select#allowed_systems').select2({
            minimumInputLength: 3,
            ajax: {
                url: '/buyback/system-search',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                    }
                },
                processResults: function (data) {
                    return {
                        results: data.map(system => {
                            return {
                                id: system.system_id,
                                text: system.name
                            }
                        })
                    };
                }
            }
        });
    </script>
@endpush
