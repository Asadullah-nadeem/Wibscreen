<x-mail::message>
# Welcome to Wibscreen, {{ $userName }}!

We're excited to have you on board. Your **{{ $planName }}** plan is now active and ready for you to explore.

<x-mail::panel>
### Your Plan Features:
@foreach($features as $feature)
- {{ $feature['text'] }}
@endforeach
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/dashboard'">
Go to My Workspace
</x-mail::button>

If you have any questions, feel free to reach out to our support team at [support.codeaxe@gmail.com](mailto:support.codeaxe@gmail.com).

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
