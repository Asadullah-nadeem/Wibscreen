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
    display: flex;
    flex-direction: column;
  }
  .plan-card.popular {
    border: 2px solid var(--wb-primary);
    box-shadow: 0 30px 60px -12px rgba(var(--wb-primary-rgb), 0.25);
    transform: scale(1.02);
    z-index: 10;
  }
  .popular-badge {
    position: absolute; top: 12px; right: -35px;
    background: var(--wb-primary); color: white;
    padding: 6px 45px; font-size: 0.75rem; font-weight: 800;
    transform: rotate(45deg); text-transform: uppercase;
    letter-spacing: 1px;
  }
  .plan-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 24px;
  }
  .plan-price { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 3rem; letter-spacing: -2px; line-height: 1; }
  .plan-feature { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; font-size: 0.95rem; }
  .plan-feature i { margin-top: 2px; font-size: 1.1rem; }
  .plan-feature i.tick { color: #22c55e; }
  .plan-feature i.cross { color: #ef4444; opacity: 0.5; }

  /* Billing Toggle Customization */
  .billing-toggle {
    display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 60px;
  }
  .form-check-input:checked { background-color: var(--wb-primary); border-color: var(--wb-primary); }
</style>
@endpush

@section('content')
@include('partials.navbar')

<!-- Hero Section -->
<section class="wb-hero pb-5">
  <div class="container text-center">
    <span class="wb-section-label">Pricing</span>
    <h1 class="wb-hero-title">Simple, <span class="text-gradient">transparent</span> plans</h1>
    <p class="wb-hero-sub">Choose the workspace that fits your workflow. Upgrade or downgrade anytime with no hidden fees.</p>

    <!-- Billing Toggle -->
    <div class="billing-toggle">
      <span class="fw-bold fs-5" id="monthly-label">Monthly</span>
      <div class="form-check form-switch p-0 m-0">
        <input class="form-check-input shadow-none" type="checkbox" id="billing-toggle" style="width: 4rem; height: 2rem; cursor: pointer; float: none;">
      </div>
      <span class="text-secondary fw-semibold fs-5" id="annual-label">Annual <span class="badge rounded-pill bg-success-subtle text-success ms-2">Save 20%</span></span>
    </div>

    <!-- Pricing Cards -->
    <div class="row g-4 align-items-stretch justify-content-center text-start">
      @foreach($plans as $plan)
      <div class="col-md-6 col-lg-4">
        <div class="wb-card plan-card h-100 {{ $plan->is_popular ? 'popular' : '' }}">
          @if($plan->is_popular) <div class="popular-badge">Best Value</div> @endif
          
          <div class="plan-icon shadow-sm" style="background: {{ $plan->slug === 'pro' ? 'rgba(var(--wb-primary-rgb), 0.1)' : ($plan->slug === 'business' ? '#fef3c7' : 'var(--wb-surface-3)') }}; color: {{ $plan->slug === 'pro' ? 'var(--wb-primary)' : ($plan->slug === 'business' ? '#d97706' : 'var(--bs-secondary-color)') }};">
            <i class="bi {{ $plan->slug === 'pro' ? 'bi-rocket-takeoff-fill' : ($plan->slug === 'business' ? 'bi-building-fill' : 'bi-seedling-fill') }}"></i>
          </div>
          
          <h3 class="h3 fw-bold mb-2">{{ $plan->name }}</h3>
          <p class="small text-secondary mb-4" style="min-height: 48px;">{{ $plan->description }}</p>

          <div class="d-flex align-items-end gap-2 mb-5">
            <span class="plan-price" id="{{ $plan->slug }}-price">₹{{ number_format($plan->price_monthly) }}</span>
            <span class="text-secondary fw-medium mb-2 fs-5" id="{{ $plan->slug }}-period">/month</span>
          </div>

          <div class="mb-5 mt-auto">
          @auth
            @php
              $user = auth()->user();
              $isPremiumActive = ($user->plan !== 'free' && !$user->isPlanExpired());
            @endphp

            @if($user->plan === $plan->slug)
              @if($user->isPlanExpired())
                <button class="btn btn-primary-wb w-100 py-3 rzp-pay-btn" data-plan="{{ $plan->slug }}">Renew {{ $plan->name }}</button>
              @else
                <button class="btn btn-outline-wb w-100 py-3" disabled><i class="bi bi-check2-circle me-2"></i> Current Plan</button>
              @endif
            @else
              @if($isPremiumActive)
                <button class="btn btn-outline-wb w-100 py-3" onclick="alert('You already have an active {{ ucfirst($user->plan) }} subscription.');" disabled>
                   Locked
                </button>
              @else
                @if(in_array($plan->slug, ['pro', 'business']))
                  <button class="btn btn-primary-wb w-100 py-3 rzp-pay-btn" data-plan="{{ $plan->slug }}">
                    {{ $plan->slug === 'pro' ? 'Upgrade to Pro' : 'Start Business' }}
                  </button>
                @else
                  <a href="{{ route('upgrade', ['plan' => $plan->slug]) }}" class="btn btn-outline-wb w-100 py-3">Switch to {{ $plan->name }}</a>
                @endif
              @endif
            @endif
          @else
            @if($plan->slug === 'business')
              <a href="{{ route('signup', ['plan' => 'business']) }}" class="btn btn-outline-wb w-100 py-3">Start Business</a>
            @else
              <a href="{{ route('signup', ['plan' => $plan->slug]) }}" class="btn btn-primary-wb w-100 py-3">{{ $plan->slug === 'pro' ? 'Upgrade to Pro' : 'Get Started Free' }}</a>
            @endif
          @endauth
          </div>

          <div class="plan-features border-top border-emphasis-subtle pt-4">
            <h6 class="small fw-bold text-uppercase opacity-50 mb-3">What's included</h6>
            @foreach($plan->features as $feature)
              <div class="plan-feature">
                @if(isset($feature['cross']) && $feature['cross'])
                  <i class="bi bi-x-circle-fill cross"></i>
                  <span class="opacity-50 text-decoration-line-through">{{ $feature['text'] }}</span>
                @else
                  <i class="bi bi-check-circle-fill tick"></i>
                  <span class="text-emphasis">{!! (isset($feature['bold']) && $feature['bold']) ? '<strong>'.$feature['text'].'</strong>' : $feature['text'] !!}</span>
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

<!-- Comparison Table Section -->
<section class="py-5 border-top border-emphasis-subtle bg-body-tertiary">
  <div class="container py-5">
    <div class="text-center mb-5">
      <span class="wb-section-label">Details</span>
      <h2 class="display-6 fw-bold">Full feature comparison</h2>
    </div>
    <div class="wb-card p-0 overflow-hidden shadow-sm border-emphasis-subtle">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="bg-body-secondary">
            <tr>
              <th class="py-4 px-4 border-0">Workspace Features</th>
              <th class="py-4 px-4 text-center border-0">Free</th>
              <th class="py-4 px-4 text-center border-0 text-primary">Pro</th>
              <th class="py-4 px-4 text-center border-0">Business</th>
            </tr>
          </thead>
          <tbody class="border-top-0">
            <tr><td class="px-4 py-3 fw-medium text-secondary">Workspaces</td><td class="text-center py-3">1</td><td class="text-center py-3 fw-bold text-primary">10</td><td class="text-center py-3">Unlimited</td></tr>
            <tr><td class="px-4 py-3 fw-medium text-secondary">Tabs per workspace</td><td class="text-center py-3">10</td><td class="text-center py-3 fw-bold text-primary">Unlimited</td><td class="text-center py-3">Unlimited</td></tr>
            <tr><td class="px-4 py-3 fw-medium text-secondary">Email accounts</td><td class="text-center py-3">10</td><td class="text-center py-3 fw-bold text-primary">Unlimited</td><td class="text-center py-3">Unlimited</td></tr>
            <tr><td class="px-4 py-3 fw-medium text-secondary">Monthly login time</td><td class="text-center py-3">150 hrs</td><td class="text-center py-3 fw-bold text-primary">720 hrs</td><td class="text-center py-3">Always-on</td></tr>
            <tr><td class="px-4 py-3 fw-medium text-secondary">Support</td><td class="text-center py-3">Community</td><td class="text-center py-3">Priority</td><td class="text-center py-3">24/7 SLA</td></tr>
            <tr class="border-0"><td class="px-4 py-3 fw-medium text-secondary border-0">Ad-Free Experience</td><td class="text-center py-3 text-success border-0"><i class="bi bi-check-lg fs-5"></i></td><td class="text-center py-3 text-success border-0"><i class="bi bi-check-lg fs-5"></i></td><td class="text-center py-3 text-success border-0"><i class="bi bi-check-lg fs-5"></i></td></tr>
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
