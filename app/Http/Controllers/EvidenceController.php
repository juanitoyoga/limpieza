<?php


namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Models\Evidence;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function store(StoreEvidenceRequest $request)
    {
        $file = $request->file('file');

        $path = $file->store('evidence/' . date('Y/m/d'), 'public');

        $evidence = Evidence::create([
            'file_path'     => $path,
            'latitude'      => $request->input('latitude'),
            'longitude'     => $request->input('longitude'),
            'timestamp_utc' => $request->input('timestampUtc'),
            'device_id'     => $request->input('deviceId'),
            'evidence_hash' => $request->input('evidenceHash'),
            'signature'     => $request->input('signature'),
            'vecino_id'     => $request->vecinoId,
        ]);

        return response()->json([
            'success'  => true,
            'id'       => $evidence->id,
            'filePath' => $path,
        ], 201);
    }
}
