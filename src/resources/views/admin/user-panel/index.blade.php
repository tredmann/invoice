@extends('layouts.app')

@section('content')

    {{ Breadcrumbs::render() }}

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('admin/user-panel.user-panel') }}</h1>
        <a href="{{route('admin.user-panel.create')}}" class="button is-danger is-radiusless">
            {{ __('admin/user-panel.add') }}
        </a>
    </section>

    <section id="content_section" class="mt-5 box container pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('admin/user-panel.user-panel') }}</h1>
        </div>

        @component('admin.user-panel.components.usersTable', ['users' => $users])
        @endcomponent
    </section>

    {{ $users->links() }}

@endsection
