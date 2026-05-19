<x-mail::message>
# New Business Lead!

A user has requested more information about the **Business Plan**.

### User Details:
- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **User ID:** #{{ $user->id }}

### Plan Requested:
- **Plan:** Business (Contact Sales)
- **Status:** Pending Approval

Please reach out to this user within the next 24 hours to discuss custom solutions and white-label options.

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>
