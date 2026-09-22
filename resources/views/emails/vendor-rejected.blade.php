<div style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">Application Status Update</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">VandoraX Vendor Registration</p>
        </div>

        <!-- Body -->
        <div style="padding: 40px; color: #333;">
            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Hello <strong>{{ $vendor->user->name }}</strong>,
            </p>

            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #856404;">
                    ℹ️ Update on Your Vendor Application
                </p>
            </div>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0;">
                Thank you for applying to become a vendor on VandoraX! After careful review of your application, we regret to inform you that we are unable to approve it at this time.
            </p>

            <h3 style="font-size: 16px; color: #333; margin: 25px 0 15px 0;">Reason for Decision:</h3>
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #555;">
                    {{ $vendor->rejection_reason ?? 'No specific reason provided' }}
                </p>
            </div>

            <h3 style="font-size: 16px; color: #333; margin: 25px 0 15px 0;">What's Next?</h3>
            <p style="font-size: 15px; line-height: 1.6; margin: 15px 0;">
                We encourage you to address the concerns mentioned above and consider reapplying. Here are some tips to improve your application:
            </p>

            <ul style="font-size: 15px; line-height: 2; margin: 15px 0; padding-left: 20px;">
                <li>📋 Ensure all required documents are clear and legible</li>
                <li>💼 Provide accurate and complete business information</li>
                <li>📸 Use professional photos for your store profile</li>
                <li>📝 Write a clear and compelling store description</li>
                <li>✓ Verify all personal and banking details are correct</li>
            </ul>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0;">
                You can reapply after 30 days. We'd love to see you try again with the necessary improvements!
            </p>

            <!-- Contact Support -->
            <div style="background: #f0f4ff; padding: 20px; border-radius: 4px; margin: 30px 0; text-align: center;">
                <p style="margin: 0 0 15px 0; font-size: 15px; font-weight: bold; color: #667eea;">
                    Have questions about the decision?
                </p>
                <p style="margin: 0; font-size: 14px;">
                    Contact our support team at 
                    <a href="mailto:support@vandorax.com" style="color: #667eea; text-decoration: none; font-weight: bold;">
                        support@vandorax.com
                    </a>
                </p>
            </div>

            <p style="font-size: 15px; line-height: 1.6; margin: 20px 0; color: #666;">
                We appreciate your interest in VandoraX and hope to welcome you as a vendor soon. Thank you for your understanding!
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