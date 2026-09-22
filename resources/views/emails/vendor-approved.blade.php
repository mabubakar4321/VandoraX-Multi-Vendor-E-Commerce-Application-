<div style="font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px;">🎉 Congratulations!</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 18px;">Your Store is Approved</p>
        </div>

        <!-- Body -->
        <div style="padding: 40px; color: #333;">
            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Hello <strong>{{ $vendor->user->name }}</strong>,
            </p>

            <div style="background: #f0f4ff; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #667eea;">
                    ✓ Your vendor application has been approved!
                </p>
            </div>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0;">
                Great news! Your store <strong>"{{ $vendor->store_name }}"</strong> has been verified and approved by our team. You can now:
            </p>

            <ul style="font-size: 15px; line-height: 2; margin: 20px 0; padding-left: 20px;">
                <li>✓ Access your vendor dashboard</li>
                <li>✓ List and manage products</li>
                <li>✓ Receive and fulfill orders</li>
                <li>✓ Track your sales and earnings</li>
                <li>✓ Manage inventory</li>
            </ul>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ route('vendor.dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                    Go to Your Dashboard
                </a>
            </div>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0;">
                If you have any questions or need assistance, please don't hesitate to contact our support team at <a href="mailto:support@vandorax.com" style="color: #667eea; text-decoration: none;">support@vandorax.com</a>
            </p>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0; font-style: italic; color: #666;">
                Welcome to VandoraX! We're excited to have you as part of our growing community of sellers.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e9ecef; text-align: center; font-size: 12px; color: #666;">
            <p style="margin: 0 0 10px 0;">
                <strong>VandoraX</strong> - Multi-Vendor Ecommerce Platform
            </p>
            <p style="margin: 0;">
                © {{ date('Y') }} VandoraX. All rights reserved.
            </p>
            <p style="margin: 10px 0 0 0; font-size: 11px;">
                <a href="#" style="color: #667eea; text-decoration: none;">Privacy Policy</a> | 
                <a href="#" style="color: #667eea; text-decoration: none;">Terms of Service</a> | 
                <a href="#" style="color: #667eea; text-decoration: none;">Contact Us</a>
            </p>
        </div>
    </div>
</div>