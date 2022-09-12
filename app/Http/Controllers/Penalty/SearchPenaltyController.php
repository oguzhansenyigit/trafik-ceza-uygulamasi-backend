<?php

namespace App\Http\Controllers\Penalty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penalty;
use App\traits\Utils;

class SearchPenaltyController extends Controller
{
    use Utils;


    public function index(Request $request) {

        $request->validate($rules = [

            'value' => 'required',

        ]);

        $penalty = new Penalty();
        $columns = $this->getTableColumns($penalty->getTableName());

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

        $counter = 0;

        $penalty = $penalty->where(function($q) use($columns, $counter, $request) {
            foreach($columns as $name) {
                if($counter == 0) {
                    $q = $q->where($name, 'LIKE', '%'.$request->value.'%');
                }else {
                    $q = $q->orWhere($name, 'LIKE', '%'.$request->value.'%');
                }
                $counter ++;
            }
        });

        if(request()->has('sort_by')) {
            $penalty = $penalty->orderBy(request()->sort_by, 'DESC');
        }

        $perPage = (request()->has('per_page'))?request()->per_page:env('PER_PAGE');
        if ($perPage == 'All') {
            return response()->json(['data' => $penalty->get()], 201);
        }
        return response()->json($penalty->paginate($perPage), 201);

    }
}
