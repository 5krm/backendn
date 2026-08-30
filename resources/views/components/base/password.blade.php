@props(['name', 'label' => ''])

<x-base.input type="password" name="{{$name}}" label="{{$label}}" >
    <span class="{{$name}}-toggle icon-[mdi--eye-off] "></span>
</x-base.input>

<script>
    
    document.querySelector('.{{$name}}-toggle').addEventListener('click', () => {
        const password = document.querySelector('input[name={{$name}}]');
        if (password.type === 'password') {
            password.type = 'text';
            document.querySelector('.{{$name}}-toggle').classList.add('icon-[mdi--eye]');
            document.querySelector('.{{$name}}-toggle').classList.remove('icon-[mdi--eye-off]');
        } else {
            password.type = 'password';
            document.querySelector('.{{$name}}-toggle').classList.add('icon-[mdi--eye-off]');
            document.querySelector('.{{$name}}-toggle').classList.remove('icon-[mdi--eye]');
        }
        password.focus();
    });
</script>
