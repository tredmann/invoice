<div class="notifications">
    @if (session('error'))
        <div class="notification notification__error" role="alert">
            <strong>{{session('error')}}</strong>
        </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="notification notification__error" role="alert">
                <strong>{{$error}}</strong>
            </div>
        @endforeach
    @endif
    @if (session('delete'))
        <div class="notification notification__warning" role="alert">
            <strong>{{session('delete')}}</strong>
        </div>
    @endif
    @if (session('success'))
        <div class="notification notification__success" role="alert">
            <strong>{{session('success')}}</strong>
        </div>
    @endif
</div>
