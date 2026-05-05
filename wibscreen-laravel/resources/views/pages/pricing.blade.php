@extends('layouts.app')
@section('title', 'Pricing — Wibscreen')
@section('meta_desc', 'Simple transparent pricing. Free, Pro and Business plans for every need.')

@push('styles')
<style>
  .plan-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--wb-border);
    position: relative;
    overflow: hidden;
  }
  .plan-card.popular {
    border: 2px solid var(--wb-primary);
    box-shadow: 0 20px 40px -10px rgba(99,102,241,0.25);
    transform: scale(1.03);
    z-index: 10;
  }
  .popular-badge {
    position: absolute; top: 12px; right: -30px;
    background: var(--wb-primary); color: white;
    padding: 4px 40px; font-size: 0.7rem; font-weight: 800;
    transform: rotate(45deg); text-transform: uppercase;
  }
  .plan-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; margin-bottom: 20px;
  }
  .plan-price { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 2.5rem; letter-spacing: -1px; }
  .plan-feature { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; font-size: 0.9rem; }
  .plan-feature i { margin-top: 4px; font-size: 0.9rem; }
  .plan-feature i.tick { color: #22c55e; }
  .plan-feature i.cross { color: #ef4444; opacity: 0.5; }

  /* Toggle Switch */
  .billing-toggle {
    display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 50px;
  }
  .form-check-input:checked { background-color: var(--wb-primary); border-color: var(--wb-primary); }
</style>
@endpush

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-5">
  <div class="container">
    <span class="wb-section-label">Pricing</span>
    <h1 class="wb-hero-title mb-3">Simple, <span class="text-primary">transparent</span> plans</h1>
    <p class="wb-hero-sub text-body-secondary mb-5">Choose the workspace that fits your workflow. Upgrade or downgrade anytime.</p>

    <div class="billing-toggle">
      <span class="fw-bold" id="monthly-label">Monthly</span>
      <div class="form-check form-switch p-0 m-0" style="min-height: auto;">
        <input class="form-check-input shadow-none" type="checkbox" id="billing-toggle" style="width: 3.5rem; height: 1.75rem; cursor: pointer; float: none; margin-left: 0;">
      </div>
      <span class="text-body-secondary fw-semibold" id="annual-label">Annual <span class="badge rounded-pill bg-success-subtle text-success ms-1">Save 20%</span></span>
    </div>

    <div class="row g-4 align-items-stretch justify-content-center">
      @foreach($plans as $plan)
      <div class="col-md-6 col-lg-4">
        <div class="wb-page-card plan-card h-100 {{ $plan->is_popular ? 'popular' : '' }}">
          @if($plan->is_popular) <div class="popular-badge">Popular</div> @endif
          
          <div class="plan-icon shadow-sm" style="background: {{ $plan->slug === 'pro' ? 'rgba(99,102,241,0.1)' : ($plan->slug === 'business' ? '#fef3c7' : 'rgba(148,163,184,0.1)') }}; color: {{ $plan->slug === 'pro' ? 'var(--wb-primary)' : ($plan->slug === 'business' ? '#d97706' : 'var(--bs-secondary-color)') }};">
            <i class="fas {{ $plan->slug === 'pro' ? 'fa-rocket' : ($plan->slug === 'business' ? 'fa-building' : 'fa-seedling') }}"></i>
          </div>
          
          <h3 class="h4 fw-bold mb-1">{{ $plan->name }}</h3>
          
          <div class="d-flex align-items-end gap-1 mb-1">
            <span class="plan-price" id="{{ $plan->slug }}-price">₹{{ $plan->price_monthly }}</span>
            <span class="text-body-secondary mb-2" id="{{ $plan->slug }}-period">/month</span>
          </div>
          
          <p class="small text-body-secondary mb-4" style="min-height: 40px;">{{ $plan->description }}</p>

          <div class="mb-4">
          @auth
            @php
              $user = auth()->user();
              $isPremiumActive = ($user->plan !== 'free' && !$user->isPlanExpired());
            @endphp

            @if($user->plan === $plan->slug)
              @if($user->isPlanExpired())
                <button class="wb-btn-primary rzp-pay-btn" data-plan="{{ $plan->slug }}">Renew {{ $plan->name }}</button>
              @else
                <button class="btn btn-{{ $plan->is_popular ? 'primary' : 'outline-secondary' }} w-100 fw-bold py-3 rounded-3" disabled>Current Plan</button>
              @endif
            @else
              @if($isPremiumActive)
                <button class="btn btn-outline-secondary w-100 fw-bold py-3 rounded-3" onclick="alert('You already have an active {{ ucfirst($user->plan) }} subscription. You can purchase a new plan once your current one expires.');" disabled>
                   Locked
                </button>
              @else
                @if(in_array($plan->slug, ['pro', 'business']))
                  <button class="wb-btn-primary rzp-pay-btn" data-plan="{{ $plan->slug }}">
                    {{ $plan->slug === 'pro' ? 'Upgrade to Pro' : 'Start Business' }}
                  </button>
                @else
                  <a href="{{ route('upgrade', ['plan' => $plan->slug]) }}" class="btn btn-outline-secondary w-100 fw-bold py-3 rounded-3">Switch to {{ $plan->name }}</a>
                @endif
              @endif
            @endif
          @else
            @if($plan->slug === 'business')
              <a href="{{ route('signup', ['plan' => 'business']) }}" class="btn btn-outline-secondary w-100 fw-bold py-3 rounded-3">Start Business</a>
            @else
              <a href="{{ route('signup', ['plan' => $plan->slug]) }}" class="wb-btn-primary">{{ $plan->slug === 'pro' ? 'Upgrade to Pro' : 'Get Started Free' }}</a>
            @endif
          @endauth
          </div>

          <div class="plan-features pt-2">
            @foreach($plan->features as $feature)
              <div class="plan-feature">
                @if(isset($feature['cross']) && $feature['cross'])
                  <i class="fas fa-xmark cross"></i>
                  <span class="opacity-50">{{ $feature['text'] }}</span>
                @else
                  <i class="fas {{ $feature['icon'] ?? 'fa-check-circle' }} tick"></i>
                  <span class="text-body-emphasis">{!! (isset($feature['bold']) && $feature['bold']) ? '<strong>'.$feature['text'].'</strong>' : $feature['text'] !!}</span>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Comparison Table --}}
<section class="py-5 border-top" style="background:var(--wb-surface-2); border-color: var(--wb-border) !important;">
  <div class="container py-4">
    <div class="text-center mb-5">
      <span class="wb-section-label">Details</span>
      <h2 class="h2 fw-bold" style="font-family:'Manrope',sans-serif;">Full feature comparison</h2>
    </div>
    <div class="wb-page-card p-0 overflow-hidden shadow-sm">
      <div class="table-responsive">
        <table class="table compare-table mb-0">
          <thead>
            <tr>
              <th class="py-3 px-4">Workspace Features</th>
              <th class="py-3 px-4 text-center">Free</th>
              <th class="py-3 px-4 text-center" style="color:var(--wb-primary);">Pro</th>
              <th class="py-3 px-4 text-center">Business</th>
            </tr>
          </thead>
          <tbody>
            <tr><td class="px-4 fw-medium text-body-secondary">Workspaces</td><td class="text-center">1</td><td class="text-center fw-bold" style="color:var(--wb-primary);">10</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4 fw-medium text-body-secondary">Tabs per workspace</td><td class="text-center">10</td><td class="text-center fw-bold" style="color:var(--wb-primary);">Unlimited</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4 fw-medium text-body-secondary">Email accounts</td><td class="text-center">10</td><td class="text-center fw-bold" style="color:var(--wb-primary);">Unlimited</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4 fw-medium text-body-secondary">Monthly login time</td><td class="text-center">150 hrs</td><td class="text-center fw-bold" style="color:var(--wb-primary);">720 hrs</td><td class="text-center">Always-on</td></tr>
            <tr><td class="px-4 fw-medium text-body-secondary">Support</td><td class="text-center">Community</td><td class="text-center">Priority</td><td class="text-center">24/7 SLA</td></tr>
            <tr class="border-0"><td class="px-4 fw-medium text-body-secondary">Ads</td><td class="text-center text-success"><i class="fas fa-check"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td><td class="text-center text-danger"><i class="fas fa-times"></i></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const toggle = document.getElementById('billing-toggle');
const plans = @json($plans);

if(toggle) {
  toggle.addEventListener('change', function() {
    const isAnnual = this.checked;
    
    plans.forEach(plan => {
      const priceEl = document.getElementById(plan.slug + '-price');
      const periodEl = document.getElementById(plan.slug + '-period');
      
      if (priceEl && periodEl) {
        const price = isAnnual ? plan.price_yearly : plan.price_monthly;
        const period = isAnnual ? '/year' : '/month';
        
        if (price !== null && price !== undefined) {
          priceEl.textContent = '₹' + price.toLocaleString('en-IN');
          periodEl.textContent = period;
        }
      }
    });
  });
}

function handleRazorpay(planSlug) {
    const isAnnual = toggle ? toggle.checked : false;
    const plan = plans.find(p => p.slug === planSlug);
    
    if (!plan) return;

    const amount = isAnnual ? plan.price_yearly : plan.price_monthly;
    
    const options = {
      "key": "{{ env('RAZORPAY_KEY') }}",
      "amount": amount * 100,
      "currency": "INR",
      "name": "Wibscreen",
      "description": plan.name + " Plan Subscription",
      "image": "{{ asset('assets/img/logo.png') }}",
      "handler": function (response){
          const duration = isAnnual ? '1 Year' : '1 Month';
          window.location.href = "{{ route('upgrade') }}?plan=" + planSlug + "&payment_id=" + response.razorpay_payment_id + "&duration=" + duration;
      },
      "prefill": {
          "name": "{{ auth()->user()->name ?? '' }}",
          "email": "{{ auth()->user()->email ?? '' }}"
      },
      "theme": { "color": "#6366f1" }
    };
    const rzp = new Razorpay(options);
    rzp.open();
}

document.addEventListener('click', function(e) {
  const btn = e.target.closest('.rzp-pay-btn');
  if (btn) {
    e.preventDefault();
    handleRazorpay(btn.getAttribute('data-plan'));
  }
});

window.addEventListener('load', function() {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('checkout')) {
    @auth
      const userPlan = "{{ auth()->user()->plan }}";
      if (userPlan === 'pro' || userPlan === 'business') handleRazorpay(userPlan);
    @endauth
  }
});
</script>
@endpush
