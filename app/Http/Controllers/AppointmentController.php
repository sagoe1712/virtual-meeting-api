<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use App\Models\SlotModel;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $service_table = 'meeting_slot';
    private $status_table = 'appointment_status';
    private $user_table = 'users';
    public $booked_status = 2;
    public function __construct()
    {
        //
    }

    public function bulk_create_appointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d| after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d| after_or_equal:start_date',
            'user_id' => 'required|int|exists:users,id',
            'response' => 'required|array',
            'response.*.day_id' => 'required|int',
            'response.*.slot_duration' => 'required|int',
            'response.*.start_time' => 'required|date_format:H:i',
            'response.*.end_time' => 'required|date_format:H:i',



        ]);
        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors(), 'Validation Error - ' . $validator->errors()->first());
        }

        $create_data = $this->create_appointment_slot_bulk($request->start_date, $request->end_date, $request->user_id, $request->response);

        return $create_data;
    }

    private function create_appointment_slot_bulk($start_date, $end_date, $user_id, $response)
    {
        $return_array = array();

        $date_start = Carbon::parse($start_date);
        $date_end = Carbon::parse($end_date);

        $period = CarbonPeriod::create($date_start, $date_end);
        $day_info = collect($response);
        //dd($day_info);


        foreach ($period as $date) {
            $use_date = Carbon::parse($date);
            $day_id = $use_date->dayOfWeekIso;
            $get_data = $day_info->where('day_id', $day_id)->first();
            //dd($get_data);
            if ($get_data) {
                //dd($get_data);
                $just_start_date_time = $use_date->format('Y-m-d') . " " . $get_data['start_time'];
                $just_start_end_time = $use_date->format('Y-m-d') . " " . $get_data['end_time'];
                $date_start = Carbon::parse($just_start_date_time);
                $date_end = Carbon::parse($just_start_end_time);

                $check_slot = $this->check_date_slot($user_id, $date);

                if ($check_slot) {

                    $r_array['date'] = $date;
                    $r_array['reason'] = "User has slots for this date";
                    $return_array['error'][] = $r_array;
                } else {

                    while ($date_start < $date_end) {
                        $start_time = $date_start->toDateTimeString();
                        $end_time = $date_start->addMinute($get_data['slot_duration']);
                        $this->save_slot($start_time, $end_time, $user_id);
                        //echo $start_time . " - " . $end_time . "<br>";
                    }
                    $r_array['date'] = $use_date->format('Y-m-d');

                    $return_array['success'][] = $r_array;
                }
                //                echo $use_date->format('Y-m-d');
                //                echo "|";
                //                echo $use_date->dayOfWeekIso;
                //                echo ";";
            }



            //$just_start_date_time = $start_date." ";

        }

        return $this->sendSuccessResponse($return_array, 'Success');
    }


    private function check_date_slot($user_id, $start_date)
    {
        //DB::enableQueryLog(); // Enable query log

        $slot = SlotModel::where('user_id', $user_id)
            ->whereDate('appointment_start', $start_date)
            ->first();
        //dd(DB::getQueryLog()); // Show results of log
        //dd($slot);
        return $slot;
    }

    private function save_slot($start_date, $end_date, $doctor_id)
    {

        $slot = SlotModel::create([

            'appointment_start' => $start_date,
            'appointment_end' => $end_date,
            'user_id' => $doctor_id,
            'status' => 1

        ]);

        return $slot;
    }


    public function get_user_appointments(Request $request)
    {

        $get_data = $this->get_set_appointment($request->user_id);

        return $this->sendSuccessResponse($get_data);
    }
    public function get_set_appointment($user_id)
    {

        $get_data = $this->get_four_ten_next_raw($user_id);
        //dd($get_data);

        if ($get_data) {
            $set_data = $get_data;

            $appointment_with_date = $set_data->map(function ($item) use ($user_id) {

                $item->times = $this->get_four_ten_next_date($user_id, $item->avail_date);
                //array_column($array, 'plan');
                return $item;
            });

            return $appointment_with_date;
        }
    }

    private function get_four_ten_next_date($user_id, $date)
    {

        // $start_date = Carbon::now();
        // $end_date = $start_date->addDays(14);
        $get_data = SlotModel::where('user_id', $user_id)
            ->select('id', 'appointment_start', 'appointment_end', 'user_id', 'meeting_id')
            ->selectraw('TIME(appointment_start) AS start_time')
            ->where('status', 1)
            ->whereDate('appointment_start', $date)
            ->orderby('appointment_start', 'ASC')
            ->get();
        //dd($get_data);
        //dd($get_data);
        return $get_data;
    }

    private function get_four_ten_next_raw($user_id)
    {
        $start_date = Carbon::now();
        $end_date = $start_date->addDays(14);
        $get_data = DB::table($this->service_table)
            ->selectRaw('DISTINCT DATE(appointment_start) AS avail_date')
            ->where('status', 1)
            ->where('user_id', $user_id)
            ->where('appointment_start', '>=', Carbon::now())
            ->where('appointment_start', '<', $end_date)
            //->orderby('appointment_start', 'ASC')
            ->get();
        return $get_data;
    }

    public function get_slot_details($slot_id)
    {

        $get_data = SlotModel::where($this->service_table.'.id', $slot_id)
                    ->leftjoin($this->status_table, $this->status_table.'.id', $this->service_table.'.status')
                    ->leftjoin($this->user_table, $this->user_table.'.id', $this->service_table.'.user_id')
                    ->select($this->service_table.'.id', 'user_id', 'appointment_start', 'appointment_end', 'meeting_id', $this->service_table.'.status', 'status_name', 'first_name', 'last_name', 'email' )
                    ->first();
        return $get_data;
    }

    public function update_meeting_slot($meetng_id, $status, $slot_id)
    {

        $save_data = SlotModel::where('id', $slot_id)
            ->update(['meeting_id' => $meetng_id, 'status' => $status]);

        return $save_data;
    }
}