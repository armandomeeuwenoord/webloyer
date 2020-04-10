<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repositories\Setting\SettingInterface;
use App\Services\Form\Setting\MailSettingForm;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('acl');
    }

    public function getEmail(SettingInterface $settingRepository)
    {
        $settings = $settingRepository->byType('mail');

        return view('settings.email')
            ->with('settings', $settings);
    }

    public function postEmail(Request $request, MailSettingForm $mailSettingForm)
    {
        $input = $request->all();

        if ($mailSettingForm->update($input)) {
            return redirect()->route('settings.email');
        } else {
            return redirect()->route('settings.email')
                ->withInput()
                ->withErrors($mailSettingForm->errors());
        }
    }
}
