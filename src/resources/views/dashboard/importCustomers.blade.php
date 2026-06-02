@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">Import</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>Upload Excel file:</h1>
        </div>
        <form class="has-text-centered" method="post" action="{{ route('import.customers.store', ['tenant' => $tenant]) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-section has-text-centered">
                <label for="excel_file" class="marg">File: </label>
                <input type="file" name="excel_file" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </section>

@endsection
