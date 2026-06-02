<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Requests\SettingRequest;
use App\Http\Requests\TestEmailSettingsRequest;
use App\Models\Setting;
use App\Models\Tenant\Tenant;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Tenant $tenant)
    {
        return view('default.settings.index', [
            'tenant' => $tenant,
            'settings' => Setting::where([
                'tenant_id' => $tenant->id,
            ])->paginate(20),
        ]);
    }

    public function testEmailSettingsForm(Tenant $tenant)
    {
        return view('default.settings.testEmailSettings', [
            'settings' => Setting::where('tenant_id', '=', $tenant->id)->get(),
            'tenant' => $tenant,
        ]);
    }

    public function testEmailSettings(TestEmailSettingsRequest $testEmailSettingsRequest, Tenant $tenant)
    {
        $input = $testEmailSettingsRequest->validated();

        try {
            // 'ssl' -> implicit TLS on port 465; 'tls' (STARTTLS) and empty -> let
            // Symfony Mailer autodetect from the port.
            $tls = $input['mail_mailers_smtp_encryption'] === 'ssl' ? true : null;

            $transport = new EsmtpTransport(
                $input['mail_mailers_smtp_host'],
                (int) $input['mail_mailers_smtp_port'],
                $tls,
            );
            $transport->setUsername($input['mail_mailers_smtp_username']);
            $transport->setPassword($input['mail_mailers_smtp_password']);

            $transport->start();

            return redirect(route('settings.testEmailSettingsForm', ['tenant' => $tenant]))->with(
                'success',
                __('settings.email_settings.success'),
            );
        } catch (\Exception $e) {
            return redirect($tenant->route('settings.testEmailSettingsForm'))->with(
                'error',
                $e->getMessage(),
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Tenant $tenant)
    {
        return view('default.settings.create', ['tenant' => $tenant]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SettingRequest $request, Tenant $tenant)
    {
        $input = $request->validated();

        $exists = Setting::where([
            'tenant_id' => $tenant->id,
            'key' => $input['key'],
        ])->exists();

        if ($exists) {
            return redirect(route('settings.index', ['tenant' => $tenant]))->with(
                'error',
                'Einstellung existiert bereits',
            );
        }

        Setting::create([
            'tenant_id' => $tenant->id,
            'key' => $input['key'],
            'value' => Crypt::encryptString($input['value']),
            'type' => $input['type'],
        ]);

        return redirect(route('settings.index', ['tenant' => $tenant]))->with(
            'success',
            'Einstellung erfolgreich gespeichert',
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant, Setting $setting)
    {
        return view('default.settings.edit', [
            'setting' => $setting,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request, Tenant $tenant, Setting $setting)
    {
        $input = $request->validated();

        $setting->update([
            'key' => $input['key'],
            'value' => Crypt::encryptString($input['value']),
            'type' => $input['type'],
        ]);

        return redirect(route('settings.index', ['tenant' => $tenant]))->with(
            'success',
            'Einstellung erfolgreich aktualisiert',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant, Setting $setting)
    {
        $setting->delete();

        return redirect(route('settings.index', ['tenant' => $tenant]))->with(
            'delete',
            'Einstellungen erfolgreich gelöscht',
        );
    }
}
