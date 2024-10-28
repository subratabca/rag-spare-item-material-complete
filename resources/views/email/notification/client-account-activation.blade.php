<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Your Brand</a>
        </div>
        
        @if ($client->is_email_verified == 1)
            <p>Dear {{ $client->firstName }},</p>
            <p>Your account has been successfully activated. You can now log in. Below are your account details for your reference:</p>
        @else
            <p>Dear {{ $client->firstName }},</p>
            <p>We would like to inform you that your account has been inactivated. As a result, you will not be able to log in until your account is reactivated. Below are your account details for reference:</p>
        @endif
        
        <p style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
            <strong>Name:</strong> {{ $client->firstName }}<br />
            <strong>Email:</strong> {{ $client->email }}
        </p>
        
        <p style="font-size:0.9em;">Regards,<br />Your Brand</p>
        <hr style="border:none;border-top:1px solid #eee" />
        
        <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
            <p>Your Brand Inc</p>
            <p>1600 Amphitheatre Parkway</p>
            <p>California</p>
        </div>
    </div>
</div>
