<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
     @import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');
     a {
         text-decoration: none;
     }
     table {
            border-collapse: collapse;
            width: 100%;
    }

    .summary-table td, .summary-table th {
    border: 1px solid #29166F;
    text-align: left;
    padding: 8px;
    }

    .summary-table tr:nth-child(even) {
    background-color: #29166F;
    }
        </style>
</head>
<body>
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px">
        <tbody>
            <tr>
                <td>&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%; color:#29166F; background-color:#fff; line-height: 26px; font-family: 'Open Sans', sans-serif;">
                    <tbody>
                        <tr>
                            <td>
                            <table align="" border="0" cellpadding="0" cellspacing="0" style="width:600px; ">
                                <tbody>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="https://rewardsboxnigeria.com/images/meeting/logo.jpg" height="100%"  alt="">
                                        </td>
                                        <!-- <td><a href="#"><img src="./img/header@3x.png" style="width:600px" /></a></td> -->
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
                                <tbody>
                                    <tr>
                                        <td>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 600px">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="">Dear {{$first_name}} {{$last_name}},</p> 
                                                        <p>Thank you for booking a virtual meeting with {{$user_first_name}} of Loyalty Solutions Limited.</p>
                                                        <p>Your meeting information is as follows.</p>
                                                        <p><b>Meeting Date And Time: </b> {{$meeting_date}}</p>
                                                        <p><b>Meeting Channel: </b> {{$meeting_channel}}</p>
                                                        <p><b>Your Company Name: </b> {{$company_name}}</p>

                                                        @if(isset($meeting_url))
                                                        <p><b>Meeting URL: </b> {{$meeting_url}}</p>
                                                        @endif

                                                        <p>{{$user_first_name}} {{$user_last_name}}  would contact you to confirm the meeting on this email: {{$booking_email}} and call you on this number: {{$booking_phone}} </p>
                                                         </td>
                                                    
                                                </tr>
                                               
                                                <tr>
                                                    <td>
                                                        <a style="display: flex; flex-direction: row; justify-content: center; margin: 3rem; text-decoration: none;" href="https://loyaltysolutionsnigeria.com">
                                                            <button style="height: 40px; background-color: #29166F; color: white; border: none; width: 40%; cursor: pointer; border-radius: 5px;">Visit Site</button>
                                                        </a>
                                                       
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%;border-top: 1px solid rgba(0, 0, 0, 0.151);">
                                {{-- <a style="display: flex; color: #535353; justify-content: center; margin-top: 10px; font-size: 14px;" href="">Dont want to receive mail?</a> --}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a style="display: flex; color: #29166F; justify-content: center; margin-top: 10px; font-size: 14px;" href="#">
                                Copyright Loyalty Solutions Limited {{date('Y')}}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>