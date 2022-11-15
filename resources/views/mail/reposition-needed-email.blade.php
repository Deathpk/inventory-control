@component('mail::message')
    <h1>Olá {{ $companyName }} !</h1>
    <h2>Os seguintes produtos chegaram a quantidade minima em estoque e necessitam de reposição:</h2>
    <ul>
        @foreach($products as $product)
            <li>{{$product['name']}}</li>
        @endforeach
    </ul>
@endcomponent
