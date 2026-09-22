<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePinRequest;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ResidentProfileController extends Controller
{
    public function index()
    {
        $unitId = session('resident_unit_id');
        $unit = Unit::findOrFail($unitId);

        return Inertia::render('booking/Profile', [
            'unit' => [
                'unit_number' => $unit->unit_number,
                'owner_name' => $unit->owner_name,
            ],
            'userUnit' => $unit->unit_number,
        ]);
    }

    public function updatePin(UpdatePinRequest $request)
    {
        $unit = Unit::find(session('resident_unit_id'));

        $unit->update([
            'access_code' => Hash::make($request->new_pin),
        ]);

        return back()->with('success', 'PIN berhasil diperbarui!');
    }
}
