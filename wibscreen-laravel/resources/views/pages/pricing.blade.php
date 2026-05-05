@extends('layouts.app')
@section('title', 'Pricing — Wibscreen')
@section('meta_desc', 'Simple transparent pricing. Free, Pro and Business plans for every need.')

@push('styles')
<style>
  .plan-card { background: var(--wb-surface); border: 1px solid var(--wb-border); border-radius: 24px; padding: 36px 28px; height: 100%; position: relative; transition: all .3s ease; }
  .plan-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(0,0,0,.18); }
  .plan-card.popular { border-color: #6366f1; box-shadow: 0 0 0 1px #6366f1, 0 20px 50px rgba(99,102,241,.15); }
  .popular-badge { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: #6366f1; color: #fff; padding: 4px 20px; border-radius: 50px; font-size: .75rem; font-weight: 700; letter-spacing: .5px; white-space: nowrap; }
  .plan-price { font-family: 'Manrope', sans-serif; font-size: 3rem; font-weight: 900; line-height: 1; }
  .plan-feature { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--wb-border); font-size: .9rem; }
  .plan-feature:last-child { border-bottom: none; }
  .plan-feature i { margin-top: 2px; flex-shrink: 0; }
  .plan-icon { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 16px; }
  .faq-item { border-bottom: 1px solid var(--wb-border); padding: 18px 0; }
  .faq-item:last-child { border-bottom: none; }
  .compare-table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .8px; opacity: .6; font-weight: 700; }
  .compare-table td { vertical-align: middle; font-size: .9rem; padding: 14px 16px; }
  .compare-table tbody tr:hover { background: rgba(99,102,241,.04); }
  .tick { color: #22c55e; } .cross { color: #ef4444; opacity: .5; }
</style>
@endpush

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">Pricing</span>
    <h1 class="wb-hero-title mb-3">Simple, transparent pricing</h1>
    <p class="wb-hero-sub text-body-secondary">Start free. Upgrade when you need more power. No hidden fees.</p>
    <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
      <span class="small fw-semibold">Monthly</span>
      <div class="form-check form-switch mb-0">
        <input class="form-check-input" type="checkbox" id="billing-toggle" style="width:44px;height:22px;cursor:pointer;">
      </div>
      <span class="small fw-semibold">Annual <span class="badge bg-success ms-1 rounded-pill" style="font-size:.65rem;">Save 20%</span></span>
    </div>
  </div>
</section>

<section class="pb-5">
  <div class="container">
    <div class="row g-4 justify-content-center align-items-stretch">
      @foreach($plans as $plan)
      @php
        $icons = [
          'free' => ['icon' => 'fa-seedling', 'bg' => 'rgba(100,116,139,.12)', 'color' => '#64748b'],
          'pro' => ['icon' => 'fa-bolt', 'bg' => 'rgba(99,102,241,.15)', 'color' => '#6366f1'],
          'business' => ['icon' => 'fa-building', 'bg' => 'rgba(234,179,8,.12)', 'color' => '#eab308']
        ];
        $style = $icons[$plan->slug] ?? $icons['free'];
      @endphp
      <div class="col-md-4">
        <div class="plan-card {{ $plan->is_popular ? 'popular' : '' }}">
          @if($plan->is_popular)
            <div class="popular-badge"><i class="fas fa-star me-1"></i> Most Popular</div>
          @endif
          <div class="plan-icon" style="background:{{ $style['bg'] }};color:{{ $style['color'] }};"><i class="fas {{ $style['icon'] }}"></i></div>
          <div class="small fw-bold text-uppercase mb-1" style="letter-spacing:.8px;color:{{ $style['color'] }};">{{ $plan->name }}</div>
          
          <div class="d-flex align-items-end gap-1 mb-1">
            <span class="plan-price" id="{{ $plan->slug }}-price">₹{{ $plan->price_monthly }}</span>
            <span class="text-body-secondary mb-2" id="{{ $plan->slug }}-period">/month</span>
          </div>
          
          <p class="small text-body-secondary mb-4">{{ $plan->description }}</p>

          @auth
            @if(auth()->user()->plan === $plan->slug)
              @if(auth()->user()->isPlanExpired())
                <button class="btn btn-danger w-100 fw-semibold py-2 mb-4 rounded-3" onclick="alert('Your {{ ucfirst($plan->slug) }} plan has expired. Please renew to continue enjoying premium features.');">Plan Expired</button>
              @else
                <button class="btn btn-{{ $plan->is_popular ? 'primary' : 'outline-secondary' }} w-100 fw-semibold py-2 mb-4 rounded-3" disabled>Current Plan</button>
              @endif
            @else
              {{-- Not the current plan --}}
              @if($plan->slug === 'pro')
                <button id="rzp-button-pro" class="btn btn-primary w-100 fw-semibold py-2 mb-4 rounded-3 shadow-sm">Upgrade to Pro</button>
              @elseif($plan->slug === 'business')
                <button id="rzp-button-business" class="btn btn-outline-secondary w-100 fw-semibold py-2 mb-4 rounded-3">Upgrade to Business</button>
              @else
                {{-- Free Plan Button --}}
                @if(auth()->user()->plan !== 'free' && !auth()->user()->isPlanExpired())
                  <button class="btn btn-outline-secondary w-100 fw-semibold py-2 mb-4 rounded-3" onclick="alert('You cannot switch to the Free plan while your {{ ucfirst(auth()->user()->plan) }} plan is still active.');" disabled>Switch to {{ $plan->name }}</button>
                @else
                  <a href="{{ route('upgrade', ['plan' => $plan->slug]) }}" class="btn btn-outline-secondary w-100 fw-semibold py-2 mb-4 rounded-3">Switch to {{ $plan->name }}</a>
                @endif
              @endif
            @endif
          @else
            @if($plan->slug === 'business')
              <a href="{{ route('upgrade', ['plan' => 'business']) }}" class="btn btn-outline-secondary w-100 fw-semibold py-2 mb-4 rounded-3">Contact Sales</a>
            @else
              <a href="{{ route('signup', ['plan' => $plan->slug]) }}" class="btn btn-{{ $plan->is_popular ? 'primary' : 'outline-secondary' }} w-100 fw-semibold py-2 mb-4 rounded-3 shadow-sm">{{ $plan->slug === 'pro' ? 'Start Pro Trial' : 'Get Started Free' }}</a>
            @endif
          @endauth

          @foreach($plan->features as $feature)
            <div class="plan-feature">
              @if(isset($feature['cross']) && $feature['cross'])
                <i class="fas fa-xmark cross"></i>
                <span class="opacity-50">{{ $feature['text'] }}</span>
              @else
                <i class="fas {{ $feature['icon'] ?? 'fa-check' }} {{ $feature['color'] ?? 'tick' }}" style="{{ isset($feature['color']) && str_starts_with($feature['color'], '#') ? 'color:'.$feature['color'] : '' }}"></i>
                <span>{!! (isset($feature['bold']) && $feature['bold']) ? '<b>'.preg_replace('/^(\d+|~?\d+\s\w+)/', '<b>$1</b>', $feature['text']).'</b>' : $feature['text'] !!}</span>
              @endif
            </div>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Comparison Table --}}
<section class="py-5 border-top" style="background:var(--wb-surface-2);">
  <div class="container">
    <div class="text-center mb-4">
      <span class="wb-section-label">Compare Plans</span>
      <h2 class="h3 fw-bold" style="font-family:'Manrope',sans-serif;">Full feature breakdown</h2>
    </div>
    <div class="wb-page-card p-0 overflow-hidden">
      <div class="table-responsive">
        <table class="table compare-table mb-0">
          <thead class="border-bottom"><tr><th class="py-3 px-4">Feature</th><th class="py-3 px-4 text-center">Free</th><th class="py-3 px-4 text-center" style="color:#6366f1;">Pro</th><th class="py-3 px-4 text-center">Business</th></tr></thead>
          <tbody>
            <tr><td class="px-4">Workspaces</td><td class="text-center">1</td><td class="text-center fw-semibold" style="color:#6366f1;">10</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4">Tabs per workspace</td><td class="text-center">10</td><td class="text-center fw-semibold" style="color:#6366f1;">Unlimited</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4">Email accounts</td><td class="text-center">10</td><td class="text-center fw-semibold" style="color:#6366f1;">Unlimited</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4">Monthly login hours</td><td class="text-center">~150 hrs</td><td class="text-center fw-semibold" style="color:#6366f1;">720 hrs</td><td class="text-center">Always-on</td></tr>
            <tr><td class="px-4">Bot support queries</td><td class="text-center">10/mo</td><td class="text-center fw-semibold" style="color:#6366f1;">Unlimited</td><td class="text-center">Unlimited</td></tr>
            <tr><td class="px-4">Ads shown</td><td class="text-center"><i class="fas fa-check tick"></i></td><td class="text-center"><i class="fas fa-xmark cross"></i></td><td class="text-center"><i class="fas fa-xmark cross"></i></td></tr>
            <tr><td class="px-4">Priority support</td><td class="text-center"><i class="fas fa-xmark cross"></i></td><td class="text-center"><i class="fas fa-check tick"></i></td><td class="text-center">24/7 SLA</td></tr>
            <tr><td class="px-4">White-label option</td><td class="text-center"><i class="fas fa-xmark cross"></i></td><td class="text-center"><i class="fas fa-xmark cross"></i></td><td class="text-center"><i class="fas fa-check tick"></i></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

{{-- FAQ --}}
<section class="py-5"><div class="container" style="max-width:700px;">
  <div class="text-center mb-4"><span class="wb-section-label">FAQ</span><h2 class="h3 fw-bold" style="font-family:'Manrope',sans-serif;">Common questions</h2></div>
  <div class="wb-page-card">
    <div class="faq-item"><div class="fw-semibold mb-1">Can I upgrade from Free to Pro anytime?</div><p class="text-body-secondary small mb-0">Yes. Your workspace data and collections carry over automatically.</p></div>
    <div class="faq-item"><div class="fw-semibold mb-1">What happens if I exceed my login hours on Free?</div><p class="text-body-secondary small mb-0">You'll be prompted to upgrade or your session will reset at the monthly cycle. Your saved data stays intact.</p></div>
    <div class="faq-item"><div class="fw-semibold mb-1">Are the ads intrusive on the Free plan?</div><p class="text-body-secondary small mb-0">Ads on the Free plan are minimal — displayed in a dedicated area, not overlapping your workspace content.</p></div>
    <div class="faq-item"><div class="fw-semibold mb-1">How do I contact Business sales?</div><p class="text-body-secondary small mb-0">Use our <a href="{{ route('support') }}" class="text-primary">Support page</a> and select "Business Inquiry". Our team responds within 24 hours.</p></div>
  </div>
</div></section>

<section class="py-5 border-top text-center" style="border-color:var(--wb-border)!important;">
  <div class="container">
    <h2 class="h3 fw-bold mb-2" style="font-family:'Manrope',sans-serif;">Start building your workspace today</h2>
    <p class="text-body-secondary mb-4">Free plan available — no credit card required.</p>
    <a href="{{ route('signup') }}" class="btn btn-primary px-5 py-2 fw-semibold me-2">Get Started Free</a>
    <a href="{{ route('support') }}" class="btn btn-outline-secondary px-4 py-2">Talk to Sales</a>
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
      
      if (priceEl && periodEl && plan.slug !== 'business') {
        const price = isAnnual ? plan.price_yearly : plan.price_monthly;
        const period = isAnnual ? '/year' : '/month';
        
        // Format with comma
        priceEl.textContent = '₹' + price.toLocaleString('en-IN');
        periodEl.textContent = period;
      }
    });
  });
}

function handleRazorpay(planSlug) {
    const isAnnual = toggle ? toggle.checked : false;
    const plan = plans.find(p => p.slug === planSlug);
    const amount = isAnnual ? (plan ? plan.price_yearly : 1999) : (plan ? plan.price_monthly : 199);
    
    const options = {
      "key": "{{ env('RAZORPAY_KEY') }}",
      "amount": amount * 100, // in paise
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
      "theme": {
          "color": "#6366f1"
      }
    };
    const rzp = new Razorpay(options);
    rzp.open();
}

const rzpProBtn = document.getElementById('rzp-button-pro');
if(rzpProBtn) {
  rzpProBtn.onclick = function(e) {
    e.preventDefault();
    handleRazorpay('pro');
  }
}

const rzpBizBtn = document.getElementById('rzp-button-business');
if(rzpBizBtn) {
  rzpBizBtn.onclick = function(e) {
    e.preventDefault();
    handleRazorpay('business');
  }
}
</script>
@endpush
