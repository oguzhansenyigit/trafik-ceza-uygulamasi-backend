<?php

namespace App\Http\Controllers\Penalty;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Penalty;

class PenaltyController extends Controller
{
    public function index()
    {
        $penalty = new Penalty();
        if (request()->has('pay_status') && request()->pay_status !== "all") {
            switch (request()->pay_status) {
                case "payed":
                    $penalty = $penalty->where('status', "ÖDENDİ");
                    break;
                case "pending":
                    $penalty = $penalty->where('status', "BEKLEMEDE");
                    break;
                case "canceled":
                    $penalty = $penalty->where('status', "İPTAL EDİLDİ");
                    break;
                default:
                    break;
            }
        }
        if(request()->has('sort_by')) {
            $penalty = $penalty->orderBy(request()->sort_by, 'DESC');
        }

        $perPage = (request()->has('per_page'))?request()->per_page:env('PER_PAGE');
        if ($perPage == 'All') {
            return response()->json(['data' => $penalty->get()]);
        }
        return response()->json($penalty->paginate($perPage));
    }
}
