<x-mail::message>
# Thank You for Upgrading, {{ $userName }}!

Your **{{ $planName }}** plan has been successfully activated. We're thrilled to help you take your productivity to the next level with Wibscreen.

<x-mail::panel>
### 📅 Subscription Details
- **Plan:** {{ $planName }}
- **Status:** Active
- **Valid Until:** {{ $expiry }}
</x-mail::panel>

### 🚀 What's New in Your Workspace?
With the **{{ $planName }}** plan, you now have access to:
@foreach($features as $feature)
- **{{ $feature['text'] }}**
@endforeach

### 💡 Need Help Getting Started?
If you're unsure how to use your new features or need technical assistance, our team is ready to help you:
- **Email:** [support.codeaxe@gmail.com](mailto:support.codeaxe@gmail.com)
- **WhatsApp/Call:** [+91 91190 28555](tel:+919119028555)

@if(strtolower($planName) === 'business')
> **Note for Business Users:** As a Business subscriber, you have a dedicated account manager. Feel free to reach out directly via WhatsApp for any custom white-label setups or team onboarding.
@endif

<x-mail::button :url="config('app.url') . '/dashboard'">
Launch My Workspace
</x-mail::button>

Happy Browsing,<br>
The {{ config('app.name') }} Team
</x-mail::message>
