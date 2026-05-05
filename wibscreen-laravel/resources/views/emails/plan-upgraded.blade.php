<x-mail::message>
# Thank You for Upgrading, {{ $userName }}!

Your **{{ $planName }}** plan has been successfully activated. We're thrilled to help you take your productivity to the next level with Wibscreen.

<x-mail::panel>
### 📅 Plan Validity
Your current subscription is active until: **{{ $expiry }}**
</x-mail::panel>

### 🚀 What's New in Your Workspace?
With the **{{ $planName }}** plan, you now have access to:
@foreach($features as $feature)
- **{{ $feature['text'] }}**
@endforeach

### 💡 How to Get Started:
1. **Create More Workspaces:** Head to your dashboard and use the "New Folder" button to organize different projects.
2. **Unlimited Tabs:** Feel free to open as many tabs as you need within your collections—no more limits!
3. **No Distractions:** Your workspace is now completely Ad-Free for a cleaner experience.

### 📞 Need Help?
If you have any questions or need technical assistance, our priority support team is here for you:
- **Email:** [support.codeaxe@gmail.com](mailto:support.codeaxe@gmail.com)
- **WhatsApp/Call:** [+91 91190 28555](tel:+919119028555)

<x-mail::button :url="config('app.url') . '/dashboard'">
Launch My Pro Workspace
</x-mail::button>

Happy Browsing,<br>
The {{ config('app.name') }} Team
</x-mail::message>
