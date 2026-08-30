@props(['key', 'default' => ''])
@php($value = __($key))
{{ $value !== $key ? $value : $default }}
