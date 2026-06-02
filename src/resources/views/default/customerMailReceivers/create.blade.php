@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('customerMailReceivers.add') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customerMailReceivers.add') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('customerMailReceivers.store')}}">
            @csrf

            <input type="hidden" name="customer_id" value="{{$customer->id}}">

            <!-- Email -->
            <div class="field">
                <label for="email" class="label">{{ __('attributes.email') }}</label>
                <div class="control">
                    <input id="email" name="email" type="email"
                           class="input"
                           value="{{old('email')}}"
                           required>
                </div>
            </div>

            <!-- Gender -->
            <div class="field">
                <label for="gender" class="label">{{ __('attributes.gender') }}</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select id="gender" name="gender">
                            <option></option>
                            @foreach(\App\Models\CustomerMailReceiver::GENDER as $gender)
                                <option value="{{$gender}}" @if($gender === old('gender')) selected @endif>{{$gender}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- First name -->
            <div class="field">
                <label for="first_name" class="label">{{ __('attributes.first_name') }}</label>
                <div class="control">
                    <input id="first_name" name="first_name" type="text"
                           class="input"
                           value="{{old('first_name')}}"
                           placeholder="{{ __('translate.optional') }}">
                </div>
            </div>

            <!-- Last name -->
            <div class="field">
                <label for="last_name" class="label">{{ __('attributes.last_name') }}</label>
                <div class="control">
                    <input id="last_name" name="last_name" type="text"
                           class="input"
                           value="{{old('last_name')}}"
                           placeholder="{{ __('translate.optional') }}">
                </div>
            </div>


            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('customers.mailReceivers', ['customer' => $customer])}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>
@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'; $customer=$customer?>
