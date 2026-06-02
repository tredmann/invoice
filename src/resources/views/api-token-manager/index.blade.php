@extends('layouts.app')

@section('content')

    @if(session()->has('plainTextToken'))
        <div id="plainTextToken_notification" class="notification notification-absolute mb-0 is-success is-radiusless" role="alert">
            <div class="is-flex is-justify-content-space-between">
                <p>
                    {{ session()->get('plainTextToken') }}
                </p>
                <button type="button" id="closeNotification" aria-label="Close" class="delete"></button>
            </div>
        </div>
    @endif

    {{ Breadcrumbs::render() }}

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('api-token-manager.api_tokens') }}</h1>
    </section>

    @component('api-token-manager.manager', ['apiTokens' => $apiTokens])
    @endcomponent
@endsection

@section('scripts@Footer')
    <script type="application/javascript">
        $(document).ready(function () {

            $('#closeNotification').click(function () {
                $('#plainTextToken_notification').hide();
            });
        });
    </script>
@endsection
