<x-mail::message>
# Hi {{ $user->name }},

We noticed you were interested in the **Wibscreen {{ $planName }}** plan, but your payment was not completed.

Is there anything holding you back? Whether it was a technical issue with Razorpay or you just need more information, we're here to help!

### Why {{ $planName }}?
- Full access to all premium workspace features.
- No distractions and no limits.
- Priority support for your workflow.

<x-mail::button :url="route('pricing')">
Complete My Payment
</x-mail::button>

### Need Help?
If you faced any problem during checkout, please reply to this email or message us on WhatsApp:
- **WhatsApp:** [+91 91190 28555](tel:+919119028555)
- **Email:** [support.codeaxe@gmail.com](mailto:support.codeaxe@gmail.com)

We'd love to have you in the Pro community!

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
