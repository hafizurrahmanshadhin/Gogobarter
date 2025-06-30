@extends('backend.app')

@section('title', 'Subscription Plans')

@push('styles')
    <link href="{{ asset('backend/css/subscription-plans-card.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
    <div class="page-content py-4">
        <div class="container-fluid">
            {{-- Cards Grid --}}
            <div class="row g-4" style="margin-top: 70px;">
                @foreach ($plans as $plan)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="plan-card position-relative">
                            {{-- Status Badge (Always Visible) --}}
                            <div class="status-badge {{ $plan->status === 'active' ? 'active' : 'inactive' }}">
                                {{ ucfirst($plan->status) }}
                            </div>

                            {{-- Popular Badge (Always in Same Position) --}}
                            <div class="recommended-badge-container">
                                @if ($plan->is_recommended)
                                    <div class="recommended-badge">
                                        <i class="ri-award-fill me-1"></i> Popular
                                    </div>
                                @endif
                            </div>

                            {{-- Plan Header --}}
                            <div class="plan-header">
                                {{-- Plan Name and Title --}}
                                <h4 class="plan-title">{{ $plan->name }}</h4>

                                {{-- Price Section with Fixed Height --}}
                                <div class="pricing-container">
                                    <div class="plan-price">
                                        <span class="plan-currency">{{ $plan->currency }}</span>
                                        {{ number_format($plan->price, 2) }}
                                        <span class="plan-interval">/ {{ ucfirst($plan->billing_interval) }}</span>
                                    </div>

                                    {{-- Description --}}
                                    @if ($plan->description)
                                        <p class="plan-description">{{ $plan->description }}</p>
                                    @else
                                        <p class="plan-description empty">&nbsp;</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Features List with Fixed Height --}}
                            <div class="feature-list-container">
                                <div class="feature-list">
                                    <h5>Features included:</h5>
                                    @foreach ($plan->features as $feature)
                                        <div class="feature-item">
                                            <i class="ri-checkbox-circle-fill feature-icon"></i>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Footer --}}
                            <div class="plan-footer">
                                {{-- Action Buttons Row --}}
                                <div class="row g-2 mb-2">
                                    {{-- Status Toggle Button --}}
                                    <div class="col-6">
                                        <form action="{{ route('subscription-plan.toggle-status', $plan) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-toggle-status {{ $plan->status === 'active' ? 'active' : 'inactive' }} w-100">
                                                {{ $plan->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Popular Toggle Button --}}
                                    <div class="col-6">
                                        <form action="{{ route('subscription-plan.toggle-recommended', $plan) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-toggle-recommended {{ $plan->is_recommended ? 'recommended' : 'not-recommended' }} w-100"
                                                {{ $plan->status !== 'active' ? 'disabled' : '' }}>
                                                {{ $plan->is_recommended ? 'Unpopular' : 'Popular' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Edit Button --}}
                                <a href="javascript:void(0)" class="btn btn-edit" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $plan->id }}">
                                    <i class="ri-pencil-line me-1"></i> Edit Plan
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Modal for editing this plan --}}
                    <div class="modal fade" id="editModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('subscription-plan.edit', $plan->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit {{ $plan->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            {{-- Plan Type --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="plan_type{{ $plan->id }}">Plan
                                                    Type</label>
                                                <select name="plan_type" id="plan_type{{ $plan->id }}"
                                                    class="form-select plan-type-select" data-plan-id="{{ $plan->id }}"
                                                    required>
                                                    <option value="free" @selected($plan->price == 0)>Free Plan</option>
                                                    <option value="paid" @selected($plan->price > 0)>Paid Plan</option>
                                                </select>
                                            </div>

                                            {{-- Name --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="name{{ $plan->id }}">Plan Name</label>
                                                <input type="text" name="name" id="name{{ $plan->id }}"
                                                    class="form-control" value="{{ $plan->name }}" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Billing Interval --}}
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="billing_interval{{ $plan->id }}">Billing
                                                    Interval</label>
                                                <select name="billing_interval" id="billing_interval{{ $plan->id }}"
                                                    class="form-select" required>
                                                    <option value="month" @selected($plan->billing_interval === 'month')>Month</option>
                                                    <option value="year" @selected($plan->billing_interval === 'year')>Year</option>
                                                </select>
                                            </div>

                                            {{-- Price --}}
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="price{{ $plan->id }}">Price</label>
                                                <input type="number" step="0.01" name="price"
                                                    id="price{{ $plan->id }}" class="form-control plan-price-input"
                                                    value="{{ $plan->price }}" min="0" required>
                                                <input type="hidden" name="price" id="hidden_price{{ $plan->id }}"
                                                    value="0">
                                            </div>

                                            {{-- Currency --}}
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"
                                                    for="currency{{ $plan->id }}">Currency</label>
                                                <input type="text" name="currency" id="currency{{ $plan->id }}"
                                                    class="form-control" value="{{ $plan->currency }}" required>
                                            </div>
                                        </div>

                                        {{-- Description --}}
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="description{{ $plan->id }}">Description</label>
                                            <textarea name="description" id="description{{ $plan->id }}" class="form-control" rows="3">{{ $plan->description }}</textarea>
                                        </div>

                                        {{-- Features (comma-separated) --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="features{{ $plan->id }}">Features
                                                (comma-separated)</label>
                                            <input type="text" name="features[]" class="form-control"
                                                id="features{{ $plan->id }}"
                                                value="{{ implode(',', $plan->features ?? []) }}">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update Plan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.plan-type-select').on('change', function() {
                var planId = $(this).data('plan-id');
                var type = $(this).val();
                var priceInput = $('#price' + planId);
                var hiddenPriceInput = $('#hidden_price' + planId);

                if (type === 'free') {
                    priceInput.val(0.00);
                    priceInput.prop('disabled', true);
                    hiddenPriceInput.prop('disabled', false);
                } else {
                    priceInput.prop('disabled', false);
                    hiddenPriceInput.prop('disabled', true);
                    if (parseFloat(priceInput.val()) <= 0) {
                        priceInput.val('');
                    }
                }
            });

            // On modal show, set the correct state for price input
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.plan-type-select').trigger('change');
            });
        });
    </script>
@endpush
