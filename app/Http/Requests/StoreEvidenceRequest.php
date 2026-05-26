<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'          => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:51200',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'timestampUtc'  => 'required|integer',
            'deviceId'      => 'required|string|max:100',
            'evidenceHash'  => 'required|string|max:128',
            'signature'     => 'required|string|max:128',
        ];
    }
}
