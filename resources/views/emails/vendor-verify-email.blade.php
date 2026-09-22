<div style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px;">
        <!-- Header -->
        <h1 style="color: #333; margin-top: 0;">Verify Your Email Address</h1>
        <p style="color: #666;">Hello {{ $user->name }},</p>

        <!-- Content -->
        <p style="color: #666; line-height: 1.6;">
            Thank you for registering as a seller on VandoraX! To complete your registration, please verify your email address by clicking the button below.
        </p>

        <!-- CTA Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" style="background-color: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Verify Email Address
            </a>
        </div>

        <!-- Or Link -->
        <p style="color: #999; text-align: center; margin: 20px 0;">
            Or copy and paste this link:<br>
            <code style="background-color: #f0f0f0; padding: 10px; display: block; margin-top: 10px; word-break: break-all;">
                {{ $verificationUrl }}
            </code>
        </p>

        <!-- Footer -->
        <p style="color: #999; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; font-size: 12px;">
            This link expires in 1 hour. If you didn't register for VandoraX, please ignore this email.
        </p>
    </div>
</div>