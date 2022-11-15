@component('mail::message')
<h1>Olá {{ $companyName }} !
<h2>Os seguintes produtos chegaram a quantidade minima em estoque e necessitam de reposição:</h2>
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
                <td style="text-align: center" style="padding-left: 5px">{{$product['external_product_id'] ?? $product['id']}}</td>
                <td style="text-align: center" style="padding-left: 5px">{{$product['name']}}</td>
                <td style="text-align: center" style="padding-left: 5px">{{$product['quantity']}}</td>
                <td style="text-align: center" style="padding-left: 5px">{{$product['limit_for_restock']}}</td>
                <td style="text-align: center" style="padding-left: 5px">{{$product['category']['name']}}</td>
                <td style="text-align: center" style="padding-left: 5px">{{$product['brand']['name']}}</td>
            </tr>
        @endforeach
    </table>
<br>
<h2>Desde já , agradeçemos a preferência.</h2>
<h2>Att. Equipe Stock && Repo</h2>
@endcomponent
