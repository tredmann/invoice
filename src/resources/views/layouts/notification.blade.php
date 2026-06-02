<!--SUCCESS NOTIFICATION-->
@if(session()->has('success'))
    <div id="success_notification" class="notification notification-absolute mb-0 is-success is-radiusless" role="alert">
        <div class="is-flex is-justify-content-space-between">
            <p>
                {{ session()->get('success') }}
            </p>
            <button type="button" id="closeNotification" aria-label="Close" class="delete"></button>
        </div>
    </div>
@endif

<!--ERROR NOTIFICATION-->
@if ($errors->any())
    <div id="error_notification" class="notification notification-absolute mb-0 is-danger is-radiusless" role="alert">
        <div class="is-flex is-justify-content-space-between">
            <div>
            @foreach($errors->all() as $error)
                <p>
                    {{ $error }}
                </p>

            @endforeach
            </div>
            <button type="button" id="closeNotification" aria-label="Close" class="delete"></button>
        </div>
    </div>
@endif

<!--ERROR NOTIFICATION-->
@if (session()->has('error'))
    <div id="error_notification" class="notification notification-absolute is-danger is-radiusless" role="alert">
        <div class="is-flex is-justify-content-space-between">
            <div>
                <p>
                    {{ session()->get('error') }}
                </p>
            </div>
            <button type="button" id="closeNotification" aria-label="Close" class="delete"></button>
        </div>
    </div>
@endif


<script type="application/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            fadeOutNotification();
        }, 5000);

        $('#closeNotification').click(function () {
            $('#success_notification').hide();
            $('#error_notification').hide();

        });
    });



    function fadeOutNotification() {
        $('#success_notification').fadeOut("slow");
        $('#error_notification').fadeOut("slow");
    }
</script>
