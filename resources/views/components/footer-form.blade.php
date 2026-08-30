<div class="flex items-stretch gap-2" x-data="formApp()">
    <div class="w-full">
        <input id="emailInput" type="email" placeholder="{{ __('auth.email') }}" class="input input-bordered w-full" type="email" />
        <p id="emailError" class="text-error text-sm hidden">{{ __('auth.invalid_email') }}</p>
    </div>
    <button onclick="register()" class="btn btn-primary flext items-center">{{ __('auth.register.btn') }}
        @if ($direction == 'rtl')
        <span class="icon-[mdi--arrow-left]"></span>
        @else
        <span class="icon-[mdi--arrow-right]"></span>
        @endif
    </button>
</div>
<script>
    const emailError = document.getElementById('emailError');
    const emailInput = document.getElementById('emailInput');

    function validEmail() {
        const re = /\S+@\S+\.\S+/;
        const valid = re.test(emailInput.value);
        if (!valid) {
            emailError.classList.remove('hidden');
        } else {
            emailError.classList.add('hidden');
        }

        return valid;
    }

    emailInput.onkeyup = validEmail;

    function register() {
        if (!validEmail()) return;

        const email = emailInput.value;
        window.location.href = `{{ route('auth.register') }}?email=${email}`
    }
</script>
