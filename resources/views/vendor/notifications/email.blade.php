@component('mail::message')
{{-- Greeting --}}
<h1>@lang('Welcome!')</h1>

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p>{{ $line }}</p>
@endforeach

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

@endcomponent
