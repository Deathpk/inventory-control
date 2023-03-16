@component('mail::message')
<h1>Olá {{ $employeeName }} !
<h2 style="margin-top: 30px; margin-bottom: 30px">Você foi convidado para se juntar a sua equipe no Stock && Repo!:</h2>
<p style="margin-top: 30px; margin-bottom: 30px">Estamos muito felizes de ter você conosco. Temos certeza de que apartir de agora a sua jornada com controle de estoque será muito mais tranquila.</p>
<h2 style="margin-top: 30px; text-align: center;">Abaixo se encontra a sua senha temporária, use-a para fazer login pela primeira vez na plataforma, e após, iremos pedir para você definir uma senha segura, ok?</h2>
<h2 style="margin-top: 30px; text-align: center;">{{ $randomPassword }}</h2>
@component('mail::button',  ['url' => $loginLink, 'color' => 'primary'])
Faça seu primeiro login!
@endcomponent
<h2 style="margin-top: 50px">Desde já , agradeçemos a preferência.</h2>
<h2>Att. Equipe Stock && Repo</h2>
@endcomponent
