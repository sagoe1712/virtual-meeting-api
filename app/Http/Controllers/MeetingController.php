<?php

namespace App\Http\Controllers;

use App\Models\MeetingChannel;
use App\Models\MeetingDetails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\SlotModel;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppointmentController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookMeeting;



class MeetingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $appointment;
    private $channel_table = "meeting_channel";
    public function __construct(AppointmentController $appointmentController)
    {
        $this->appointment = $appointmentController;
    }

    //

    public function insert_meeting_detail($slot_id, $firstname, $lastname, $email, $phone, $company, $subject, $channel, $link = null, $status = 1)
    {

        $insert = MeetingDetails::create([
            'slot_id' => $slot_id,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'company_name' =>  $company,
            'subject' =>  $subject,
            'meeting_channel_id' =>  $channel,
            'meeting_link' =>  $link,
            'status' => $status,

        ]);

        return $insert;
    }

    public function create_meeting_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "slot_id" => "required|int",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|email",
            "phone" => "nullable|numeric",
            "company_name" => "required|string",
            "subject" => "required|string",
            "meeting_channel_id" => "required|int|exists:appointment_status,id",
            "meeting_link" => "nullable|url|string",
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors(), $validator->errors()->first());
        }


        $save_data =  $this->meeting_detail_process($request->slot_id, $request->first_name, $request->last_name, $request->email, $request->phone, $request->company_name, $request->subject, $request->meeting_channel_id, $request->meeting_link);
        //dd($save_data);
        return $save_data;
    }

    public function show_channels(Request $request){

        $get_data = $this->list_channels();
        return $this->sendSuccessResponse('Success',$get_data);

    }
    private function meeting_detail_process($slot_id, $firstname, $lastname, $email, $phone, $company, $subject, $channel, $link)
    {

        $check_channel = $this->check_channel($channel);

        if($check_channel){
            $check_slot = $this->appointment->get_slot_details($slot_id);
            

            if ($check_slot) {

                if ($check_slot->status == 1) {

                    DB::beginTransaction();
                    $save_data = $this->insert_meeting_detail($slot_id, $firstname, $lastname, $email, $phone, $company, $subject, $channel, $link);

                    if ($save_data) {

                        $update_data = $this->appointment->update_meeting_slot($save_data->id, $this->appointment->booked_status, $slot_id);

                        if ($update_data) {
                            //dd($check_slot->email);
                            //dd($check_channel->channel_name);
                            DB::commit();
                            $app_date = Carbon::parse($check_slot->appointment_start);
                            $appointment_date = $app_date->toDayDateTimeString();
                            
                            $email_data = array("first_name"=>$firstname, "last_name"=>$lastname, "meeting_date"=>$appointment_date, "booking_email"=>$email, "booking_phone"=>$phone, "meeting_channel"=>$check_channel->channel_name, "meeting_url"=>$link, "user_first_name" => $check_slot->first_name, "user_last_name" => $check_slot->last_name, "user_email"=> $check_slot->email, "company_name"=>$company);

                            try{

                            $send_mail = Mail::to($email)->bcc($check_slot->email)->send(new BookMeeting($email_data));
                            // // dd($send_mail);
                            }catch (\Exception $e){
                                return response()->json(['data' => 'An Error Occurred. Try again. '. $e->getMessage(), 'status' => '400'], 400);
                                         
                            }
                            return $this->sendSuccessResponse('Success', ['first_name' => $firstname, 'last_name' => $lastname, 'appointment_date' => $appointment_date, 'start_date' => $check_slot->appointment_start, 'end_date' => $check_slot->appointment_end]);
                        } else {

                            DB::rollBack();
                            return $this->sendBadRequestResponse([], 'Error updating details');
                        }
                    } else {

                        return $this->sendBadRequestResponse([], 'Error saving details');
                    }
                } else {

                    return  $this->sendBadRequestResponse(['status' => $check_slot->status, 'status_name' => $check_slot->status_name], 'unable to book slot- status (' . $check_slot->status_name . ')');
                }
            } else {

                return $this->sendBadRequestResponse([], 'Slot not found');
            }
        } else {

            return $this->sendBadRequestResponse([], 'Channel not found');
        }
    }

    public function get_meeting_details($slug = null)
    {

        $view = DB::table('user');
        $view->leftJoin('meeting_slot', 'user.id', '=', 'meeting_slot.user_id');
        $view->leftJoin('meeting_details', 'meeting_slot.meeting_id', '=', 'meeting_details.id');
        $view->leftJoin('meeting_channel', 'meeting_details.meeting_channel_id', '=', 'meeting_channel.id');
        $view->select('user.id as user_id', 'user.first_name', 'user.last_name', 'user.email', 'meeting_slot.id as meeting_slot_id', 'meeting_slot.appointment_start', 'meeting_slot.appointment_end', 'meeting_details.id as meeting_detail_id', 'meeting_details.first_name as visitor_first_name', 'meeting_details.last_name as visitor_last_name', 'meeting_details.email as visitor_email', 'meeting_details.phone as visitor_phone', 'meeting_details.company_name as visitor_company_name', 'meeting_details.subject as subject', 'meeting_details.meeting_channel_id as channel_id', 'meeting_details.meeting_link as meeting_url', 'meeting_channel.channel_name as channel_name');
        if ($slug != null) {
            $view->where('slug', '=', $slug);
        }
        return $view->paginate();
    }

    public function view_meeting_details($slug = null)
    {
        $data = $this->get_meeting_details($slug);

        if ($data) {
            return $this->sendSuccessResponse("Success", $data);
        } else {
            return $this->sendBadRequestResponse("Unable to retrieve meetings", $data->errors());
        }
    }

    private function check_channel($id){

        return DB::table($this->channel_table)
        ->select('id', 'channel_name')
        ->where('id',$id)
        ->first();

    }

    private function list_channels(){

        return DB::table($this->channel_table)
        ->select('id','channel_name')
        ->get();
    }

    public function email_test(){
        $email_data = array("first_name"=>"Johnny", "meeting_date"=>"29th Nov 2021 10:00", "meeting_channel"=>"Zoom", "meeting_url"=>"www.yahoo.com", "user_first_name" => "Test Employee", "user_last_name" => "LSL", "user_email"=> "olayinka@loyaltysolutionsnigeria.com", "company_name"=>"LSL Test");

        try{

                            $send_mail = Mail::to($email_data["user_email"])->bcc("qudus@loyaltysolutionsnigeria.com")->send(new BookMeeting($email_data));
                            }catch (\Exception $e){
                                return response()->json(['data' => 'An Error Occurred. Try again. '. $e->getMessage(), 'status' => '400'], 400);
                                         
                            }
    }
}