<x-mail::message>
# Welcome Back!

Hello {{ $userName }},

Your Wibscreen account has been successfully **reactivated**. All your workspaces, tabs, and notes are now available again.

### What's next?
You can now sign in and continue where you left off.

<x-mail::button :url="route('login')">
Sign In to Workspace
</x-mail::button>

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
