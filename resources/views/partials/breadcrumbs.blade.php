<li class="breadcrumb-item">
    <a href="/" class="blue-underline">Home</a>
</li>
@foreach (generateBreadcrumbs(url()->current()) as $breadcrumb)
    <li class="breadcrumb-item">
        <a href="{{ $breadcrumb['url'] }}" class="text-black blue-underline"> {{ $breadcrumb['name'] }}</a>
    </li>
@endforeach
<style>
    .blue-underline {
        text-decoration: underline;
        text-decoration-color: blue;
        color: black;
    }
    .breadcrumb-item {
        display: inline-block;
        font-size: 1rem;
    }
</style>