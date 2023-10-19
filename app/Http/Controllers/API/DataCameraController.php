<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseFormatter;
use App\Models\DataCamera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DataCameraController extends Controller
{
    public function all(Request $request){
        $data_camera = DataCamera::all();

        return ResponseFormatter::success(
            $data_camera,
            'Get Data Camera Successfully'
        );
    }

     function getAvgWeightPerDay()
    {
        $averageWeightsPerDay = DB::table('data_cameras')
            ->select(DB::raw('DATE(created_at) as date'), 
                     DB::raw('AVG(weight) as average_weight'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($averageWeightsPerDay as $averageWeight) {
            $result[] = [
                'date' => $averageWeight->date,
                'average_weight' => $averageWeight->average_weight,
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function getAverageWeightForSpecificMonth(Request $request)
    {
        $month = $request->input('month');

        $averageWeightsForMonth = DB::table('data_cameras')
            ->select(DB::raw('DATE(created_at) as date'), 
                     DB::raw('AVG(weight) as average_weight'))
            ->whereRaw('MONTH(created_at) = ?', [$month])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return ResponseFormatter::success($averageWeightsForMonth, 'Get Data Per Month success');
    }

    function getAverageWeightPerMonth()
    {
        $averageWeightsPerMonth = DB::table('data_cameras')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                     DB::raw('DATE(created_at) as date'),
                     DB::raw('AVG(weight) as average_weight'))
            ->groupBy('month', 'date')
            ->orderBy('month', 'desc')
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($averageWeightsPerMonth as $averageWeight) {
            if (!isset($result[$averageWeight->month])) {
                $result[$averageWeight->month] = [];
            }

            $result[$averageWeight->month][] = [
                'date' => $averageWeight->date,
                'average_weight' => $averageWeight->average_weight,
            ];
        }
        return ResponseFormatter::success($result, 'Get Data Per Month success');

    }

    public function getAverageWeightToday()
    {
        $today = Carbon::today()->toDateString();

        $averageWeightToday = DB::table('data_cameras')
            ->select(DB::raw('AVG(weight) as average_weight'))
            ->whereDate('created_at', $today)
            ->first();

        return ResponseFormatter::success($averageWeightToday, 'Get Data Today success');

    }

    function add(request $request)
    {
        try {
            $request->validate([
                'status' => 'required',
                'name' => 'required',
                'weight' => 'required',
                
            ]);

            $data_camera = DataCamera::create([
                'status' => $request->status,
                'name' => $request->name,
                'weight' => $request->weight,
            ]);
            return ResponseFormatter::success($data_camera, 'Create Data Camera success');
        } catch (ValidationException $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something when wrong',
                    'error' => array_values($error->errors())[0][0],
                ],
                'Add Data Camera Failed',
                500,
            );
        }
    }
}
