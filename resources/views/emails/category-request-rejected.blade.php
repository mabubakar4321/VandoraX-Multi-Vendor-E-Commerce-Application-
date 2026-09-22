<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Category Request Rejected</title></head>
<body style="margin:0;padding:24px;background:#f4f7f5;color:#17352d;font-family:Arial,sans-serif;line-height:1.6;">
	<div style="max-width:600px;margin:0 auto;padding:32px;background:#fff;">
		<h1>Category Request Not Approved</h1>
		<p>Hello {{ $request->vendor->user->name }},</p>
		<p>Your request for <strong>&quot;{{ $request->category_name }}&quot;</strong> was not approved by the admin team.</p>
		<p><strong>Admin feedback:</strong><br>{{ $request->admin_comment ?: 'No feedback provided.' }}</p>
		<p><a href="{{ route('vendor.categories.requests') }}" style="display:inline-block;padding:12px 18px;background:#0f513f;color:#fff;text-decoration:none;">View Your Requests</a></p>
		<p>Thanks,<br><strong>VandoraX Team</strong></p>
	</div>
</body>
</html>