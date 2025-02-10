<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailUtil extends Controller
{
    public function employeeWelcomeEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.employee-welcome-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Employee Welcome Note - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Employee Welcome Note - BOXLEO COURIER');
        });
    }





    public function passwordResetEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.password-reset', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Account Password Reset - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Account Password Reset - BOXLEO COURIER');
        });
    }

    public function passwordResetCode($name, $email, $reset_code){

        $data = array(
            'email'=>$email,
            'reset_code'=>$reset_code,
            'name'=>$name,
        );

        Mail::send('emails.password-reset-code', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Account Password Reset - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Account Password Reset Code - BOXLEO COURIER');
        });
    }

    public function ticketReceivedEmail($name, $email){

        $data = array(
            'email'=>$email,
            'name'=>$name,
        );

        Mail::send('emails.ticket-received-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('EMployee Ticket Received - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Ticket Received - BOXLEO HRM');
        });
    }


}
