@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('settings.test_email_settings') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('settings.test_email_settings') }}</h1>
            <h2>{{ __('settings.email_settings.help') }}</h2>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('settings.testEmailSettings')}}">
        @csrf

            <!-- Mail Mailers Smtp Host -->
            <div class="field">
                <label for="mail_mailers_smtp_host" class="label">mail.mailers.smtp.host</label>
                <div class="control">
                    <input id="mail_mailers_smtp_host" name="mail_mailers_smtp_host" type="text"
                           class="input"
                           value="{{old('mail_mailers_smtp_host') ?? optional($settings->where('key', '=', 'mail.mailers.smtp.host')->first())->value ?? config('mail.mailers.smtp.host')}}"
                           required>
                </div>
            </div>

            <!-- Mail Mailers Smtp Port -->
            <div class="field">
                <label for="mail_mailers_smtp_port" class="label">mail.mailers.smtp.port</label>
                <div class="control">
                    <input id="mail_mailers_smtp_port" name="mail_mailers_smtp_port" type="number"
                           class="input"
                           value="{{old('mail_mailers_smtp_port') ?? optional($settings->where('key', '=', 'mail.mailers.smtp.port')->first())->value ?? config('mail.mailers.smtp.port')}}"
                           required>
                </div>
            </div>

            <!-- Mail Mailers Smtp Username -->
            <div class="field">
                <label for="mail_mailers_smtp_username" class="label">mail.mailers.smtp.username</label>
                <div class="control">
                    <input id="mail_mailers_smtp_username" name="mail_mailers_smtp_username" type="text"
                           class="input"
                           value="{{old('mail_mailers_smtp_username') ?? optional($settings->where('key', '=', 'mail.mailers.smtp.username')->first())->value ?? config('mail.mailers.smtp.username')}}"
                           required>
                </div>
            </div>

            <!-- Mail Mailers Smtp Password -->
            <div class="field">
                <label for="mail_mailers_smtp_password" class="label">mail.mailers.smtp.password</label>
                <div class="control">
                    <input id="mail_mailers_smtp_password" name="mail_mailers_smtp_password" type="password"
                           class="input"
                           value="{{old('mail_mailers_smtp_password') ?? optional($settings->where('key', '=', 'mail.mailers.smtp.password')->first())->value ?? config('mail.mailers.smtp.password')}}"
                           required>
                </div>
            </div>

            <!-- Mail Mailers Smtp Encryption -->
            <div class="field">
                <label for="mail_mailers_smtp_encryption" class="label">mail.mailers.smtp.encryption</label>
                <div class="control">
                    <input id="mail_mailers_smtp_encryption" name="mail_mailers_smtp_encryption" type="text"
                           class="input"
                           value="{{old('mail_mailers_smtp_encryption') ?? optional($settings->where('key', '=', 'mail.mailers.smtp.encryption')->first())->value ?? config('mail.mailers.smtp.encryption')}}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('settings.index')}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('settings.test') }}</button>
                </div>
            </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>
