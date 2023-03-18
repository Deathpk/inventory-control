@component('mail::message')
<h1>Uma recuperação de senha foi solicitada!</h1>
<h2 style="margin-top: 30px; margin-bottom: 30px">Olá, recebemos a solicitação de recuperação de senha para sua conta. Caso não tenha sido você, ignore o e-mail, alguém pode ter colocado por engano. Caso você ache que alguém possa estar tentando acessar sua conta entre em contato imediatamente com o nosso suporte através do e-mail versianidev@outlook.com .</p>
<h2 style="margin-top: 30px; text-align: center;">Abaixo se encontra a sua senha temporária, use-a para resetar sua senha.</h2>
<h2 style="margin-top: 30px; text-align: center;">{{ $randomPassword }}</h2>
<h2 style="margin-top: 50px">Desde já , agradeçemos a preferência.</h2>
<h2>Att. Equipe Stock && Repo</h2>
@endcomponent
