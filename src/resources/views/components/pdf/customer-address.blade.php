@props(['customer'])

<section class="customer-address">
    <p>{{ $customer->company }}</p>
    <p>{{ $customer->name }}</p>
    <p>{{ $customer->street }}</p>
    <p>{{ $customer->postal }} {{ $customer->city }}</p>
    <p>{{ $customer->country }}</p>
</section>
