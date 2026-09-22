<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Category Request Approved</title></head>
<body style="margin:0;padding:24px;background:#f4f7f5;color:#17352d;font-family:Arial,sans-serif;line-height:1.6;">
	<div style="max-width:600px;margin:0 auto;padding:32px;background:#fff;">
		<h1>Your Category Request Has Been Approved</h1>
		<p>Hello {{ $request->vendor->user->name }},</p>
		<p>Your request for <strong>&quot;{{ $request->category_name }}&quot;</strong> has been approved by the admin team.</p>
		<p>The category <strong>{{ $category->name }}</strong> is now available for your products.</p>
		<p><strong>Commission rate:</strong> {{ $category->commission_rate }}%<br><strong>Approved on:</strong> {{ $request->approved_at->format('F d, Y') }}</p>
		@if($request->admin_comment)<p><strong>Admin comment:</strong><br>{{ $request->admin_comment }}</p>@endif
		<p><a href="{{ route('vendor.dashboard') }}" style="display:inline-block;padding:12px 18px;background:#0f513f;color:#fff;text-decoration:none;">Go to Dashboard</a></p>
		<p>Thanks,<br><strong>VandoraX Team</strong></p>
	</div>
</body>
</html>