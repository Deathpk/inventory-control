@component('mail::message')
<h1>Olá {{ $companyName }} !
<h2 style="margin-top: 30px; margin-bottom: 30px">Os seguintes produtos chegaram a quantidade minima em estoque e necessitam de reposição:</h2>
<table>
    <tr>
        <th style="padding-left: 5px">Id</th>
        <th style="padding-left: 5px">Nome</th>
        <th style="padding-left: 5px">Quantidade</th>
        <th style="padding-left: 5px">Quantidade Minima</th>
        <th style="padding-left: 5px">Categoria</th>
        <th style="padding-left: 5px">Marca</th>
    </tr>
    @foreach($products as $product)
        <tr>
            <td style="text-align: center; padding-left: 5px">{{$product['external_product_id'] ?? $product['id']}}</td>
            <td style="text-align: center; padding-left: 5px">{{$product['name']}}</td>
            <td style="text-align: center; padding-left: 5px">{{$product['quantity']}}</td>
            <td style="text-align: center; padding-left: 5px">{{$product['minimum_quantity']}}</td>
            <td style="text-align: center; padding-left: 5px">{{$product['category']['name']}}</td>
            <td style="text-align: center; padding-left: 5px">{{$product['brand']['name']}}</td>
        </tr>
    @endforeach
</table>

<h2 style="margin-top: 50px">Desde já , agradeçemos a preferência.</h2>
<h2>Att. Equipe Stock && Repo</h2>
@endcomponent
