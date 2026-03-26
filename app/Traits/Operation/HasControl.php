<?php

namespace App\Traits\Operation;

use App\Models\Farmer;

use Carbon\Carbon;

trait HasControl{

    public function generateControlNumber($prefix)
    {
        // Get the current year and month from the current date
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->format('m');

        // Fetch the last control number for the given prefix
        $lastControl = Farmer::where('app_no', 'like', "$prefix-$year-$month-%")
            ->orderBy('created_at', 'desc')
            ->first();

        // Generate the next series number
        $lastSeries = $this->generateSeries($lastControl?->app_no);
        $newSeries = sprintf('%05d', $lastSeries); // Zero-padded to 5 digits

        // Create the new control number
        $control_no = "$prefix-$year-$month-$newSeries";

        return $control_no;
    }

    private function generateSeries($control_no)
    {
        if (!$control_no) {
            return 1; // Default to 1 if no previous number exists
        }

        // Extract the series part from the control number (last segment after '-')
        $parts = explode('-', $control_no);
        $series = (int) end($parts);

        return $series + 1; // Increment the series
    }

    // public function generateMRNumber($date_action, $prefix)
    // {

    //     $year = date('Y', strtotime($date_action));
    //     $month = date('m', strtotime($date_action));

    //     // Extract the prefix and series from the last control number

    //     $lastSeries = 0;
    //     $operations_code='';

    //     $control_number = Issuance::where('control_no', 'like', '%' . $prefix . '%')->orderBy('created_at', 'desc')->first();
    //     $newSeries = $this->generateSeries($control_number?->control_no);

    //     $control_no = $year."-$month-" . sprintf('%04d', $newSeries);

    //     return $control_no;


    // }





    // public function generatePMRNumber($date_action)
    // {

    //     $year = date('Y', strtotime($date_action));
    //     $month = date('m', strtotime($date_action));

    //     // Extract the prefix and series from the last control number

    //     $lastSeries = 0;
    //     $operations_code='';
    //     $prefix = 'PMR-LS';

    //     $request_code = Preventive::where('request_code', 'like', '%' . $prefix . '%')->orderBy('created_at', 'desc')->first();
    //     $user_id = Preventive::where('request_code', 'like', '%' . $prefix . '%')->orderBy('created_at', 'desc')->first();
    //     $newSeries = $this->generateSeries($request_code?->request_code);

    //     $request_code = "$prefix-$month-" . sprintf('%04d', $newSeries) . '-' . $year;

    //     return $request_code;


    // }

    // public function generatePRNumber($date_action)
    // {

    //     $year = date('Y', strtotime($date_action));
    //     $month = date('m', strtotime($date_action));

    //     // Extract the prefix and series from the last control number

    //     $lastSeries = 0;
    //     $operations_code='';
    //     $prefix = 'PIR-PEO';

    //     $prerepair_code = Prerepair::where('prerepair_code', 'like', '%' . $prefix . '%')->orderBy('created_at', 'desc')->first();
    //     $user_id = Prerepair::where('prerepair_code', 'like', '%' . $prefix . '%')->orderBy('created_at', 'desc')->first();
    //     $newSeries = $this->generateSeries($prerepair_code?->prerepair_code);

    //     $prerepair_code = "$prefix-$month-" . sprintf('%04d', $newSeries) . '-' . $year;

    //     return $prerepair_code;


    // }




    // public function generateOperation($date_action, $prefix)
    // {
    //     $prefix = session('prefix', '');

    //     $year = Carbon::parse($date_action)->year;
    //     $control_number = Operation::whereYear('date_action', $year)->latest('operations_id')->first();

    //     $newSeries = $this->generateSeries($control_number?->control_no);

    //     $operations_code =  "$prefix" .sprintf('%04d', $newSeries). substr($year, 2);

    //     // dd( $operations_code);

    //     return $operations_code;
    // }

    // protected function generateSeries($data = null)
    // {
    //     if ($data) {
    //         $parts = explode('-', $data);
    //             $lastSeries = (int)$parts[2];
    //             $newSeries = $lastSeries + 1;
    //     } else {
    //         $newSeries = 1;
    //     }
    //     return sprintf('%04d', $newSeries);
    // }

}

