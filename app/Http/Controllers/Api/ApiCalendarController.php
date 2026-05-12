<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use App\Models\VaccineSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiCalendarController extends Controller
{
    public function calendar($profileId)
    {
        $profile = HealthProfile::findOrFail($profileId);

        $ageMonths = Carbon::parse(
            $profile->birth_date
        )->diffInMonths(now());

        $query = VaccineSchedule::with('vaccine')

            /*
        |--------------------------------------------------------------------------
        | ÂGE
        |--------------------------------------------------------------------------
        */
            ->where('min_age_months', '<=', $ageMonths)

            ->where(function ($q) use ($ageMonths) {

                $q->where('max_age_months', '>=', $ageMonths)
                    ->orWhereNull('max_age_months');
            })

            /*
        |--------------------------------------------------------------------------
        | SEXE
        |--------------------------------------------------------------------------
        */
            ->where(function ($q) use ($profile) {

                $q->where('gender', 'all')
                    ->orWhere('gender', $profile->gender);
            });

        /*
    |--------------------------------------------------------------------------
    | GROSSESSE
    |--------------------------------------------------------------------------
    */

        if ($profile->is_pregnant) {

            $query->where(function ($q) {

                $q->where('only_pregnant', true)
                    ->orWhere('only_pregnant', false);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | VOYAGEUR
    |--------------------------------------------------------------------------
    */

        if ($profile->is_traveler) {

            $query->where(function ($q) {

                $q->where('for_travelers', true)
                    ->orWhere('for_travelers', false);
            });
        }

        $vaccines = $query
            ->orderBy('priority')
            ->get();

        return response()->json($vaccines);
    }

}
