<?php

namespace App\Http\Controllers\SystemStatistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Penalty;

class SystemStatiticsController extends Controller
{
    public function index() {

        //get new vehicles as per today
        $todayTotalVehicles =  Vehicle::whereDate('created_at', Carbon::today())->count();
        //get new penalties as per today
        $todayTotalPenalties =  Penalty::whereDate('created_at', Carbon::today())->count();
        //get new users as per today
        $totalPenalties =  Penalty::count();
        //get total vehicles in system
        $totalVehicles =  Vehicle::count();
        //get weekly stats
        $penalties1 = new Penalty();
        $__vehicle = new Vehicle();
        $vehicleWeeklydata = [];
        $penaltyWeeklydata = [];

        for ($i=0; $i < 7; $i++) { 
            $carbon = Carbon::today()->subDays( $i+1 );
            $penaltyWeeklydata[] = [$carbon->format('l') => $penalties1->whereDate('created_at' , '=', $carbon)->count()];
            $vehicleWeeklydata[] = [$carbon->format('l') => $__vehicle->whereDate('created_at' , '=', $carbon)->count()];
        }
        //get percentage of new vehicles this month vs previous
        $vehicle = new Vehicle();
        $lastMonthVehicle = $vehicle->whereMonth(
            'created_at', '=', Carbon::now()->subMonth()->month
        )->count();
        $currentMonthVehicle = $vehicle->whereMonth(
            'created_at', '=', Carbon::now()->month
        )->count();
        //get percentage of new penalties this month vs previous
        $penalty = new Penalty();
        $lastMonthPenalties = $penalty->whereMonth(
            'created_at', '=', Carbon::now()->subMonth()->month
        )->count();
        $currentMonthPenalties = $penalty->whereMonth(
            'created_at', '=', Carbon::now()->month
        )->count();
        //get percentage of new users this month vs previous
        $user = new User();
        $lastMonthUsers = $user->whereMonth(
            'created_at', '=', Carbon::now()->subMonth()->month
        )->count();
        $currentMonthUsers = $user->whereMonth(
            'created_at', '=', Carbon::now()->month
        )->count();

        //payment report
        $___penalty = new Penalty();
        $paidPayment = $___penalty->where('status', 'Beklemende')->count();
        $pendingPayment = $___penalty->where('status', 'Odendi')->count();


        $data = [
            "todayTotalVehicles" => $todayTotalVehicles,
            "todayTotalPenalties" => $todayTotalPenalties,
            "totalPenalties" => $totalPenalties,
            "totalVehicles" => $totalVehicles,
            "vehicleWeeklydata" => $vehicleWeeklydata,
            "penaltyWeeklydata" => $penaltyWeeklydata,
            "vehicleMonthlyIncrease" => $this->getPercentage($lastMonthVehicle,$currentMonthVehicle),
            "penaltiesMonthlyIncrease" => $this->getPercentage($lastMonthPenalties,$currentMonthPenalties),
            "usersMonthlyIncrease" => $this->getPercentage($lastMonthUsers,$currentMonthUsers),
            "paidPayment" => $paidPayment,
            "pendingPayment" => $pendingPayment,
            "vehicle_unit_garage_status" => $this->getVehicleUnitGarageStatusStats(),
            "vehicle_status" => $this->getVehicleStatusStats(),
            "vehicle_type" => $this->getVehicleTypeStats(),
        ];
        return response()->json($data, 201);
    }

    private function getPercentage($oldValue, $newValue) {

        if($oldValue != 0) {
            //avoid divisibility error by 0
            return (($newValue - $oldValue)/$oldValue) * 100;
        }
        return 0;
    }

    private function getVehicleUnitGarageStatusStats() {
        // unit_garage_status

        $data = [
            "İstaç A.Ş","İgdaş A.Ş","Avrupa yakası Zabıta","Avrupa yakası Mezarlıklar",
            "Makine ikmal","Destek Hizmetleri","İsbak A.Ş","Anadolu yakası Zabıta",
            "Anadolu yakası Mezarlıklar","Ağaç A.Ş","İsfalt A.Ş",
        ];

        
        $stats = [];

        foreach($data as $data_type) {

            $vehicle = new Vehicle();
            $stats[$data_type] = $vehicle->where('unit_garage_status', $data_type)->count();

        }
        return $stats;


    }
    private function getVehicleStatusStats() {
        // unit_garage_status

        $data = [
            "zimmetli degil","bakimda","zimmetli","Serviste",
        ];

        
        $stats = [];

        foreach($data as $data_type) {

            $vehicle = new Vehicle();
            $stats[$data_type] = $vehicle->where('vehicle_status', $data_type)->count();

        }
        return $stats;


    }
    private function getVehicleTypeStats() {
        // unit_garage_status

        $data = [
            "kiralik","resmi","ihale yolu","Protokol",
            "yedek",
        ];

        
        $stats = [];

        foreach($data as $data_type) {

            $vehicle = new Vehicle();
            $stats[$data_type] = $vehicle->where('vehicle_type', $data_type)->count();

        }
        return $stats;


    }
}
