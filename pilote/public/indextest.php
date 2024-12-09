<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Chambres d'hôte - Page simple de test</title>
<style type="text/css">
<!--
body
{
    background-color: #FFFF99;
}
div {
	border-radius: 50px;
}
#fond {
    position:relative;
    width:955px;
    height:600px;
    z-index:0;
    background-color: #FFCC99;
}
#entete {
    position:absolute;
    width:955px;
    height:102px;
    z-index:1;
    left: 0px;
    top: 0px;
    background-color: #FFA062;
    font-family: "Century Gothic", "Verdana", "Arial";
    font-weight: normal;
    color: #006F3C;
    text-align: center;
		vertical-align: middle;
}
#presentation {
    position:absolute;
    width:352px;
    height:432px;
    z-index:4;
    left: 78px;
    top: 168px;
    font-family: "Century Gothic", "Verdana", "Arial";
    font-size: 14px;
    font-weight: normal;
    color: #006F3C;
    text-align: justify;
    padding-right: 10px;
    overflow : auto;
		border-radius: 0px;
}
#canvas {
    position:absolute;
    width:400px;
    height:400px;
    z-index:1;
    left: 500px;
    top: 150px;
    background-color: #B9D7D9;
		border-radius: 100%;
		border: 2px solid #339966;
}
-->
</style>
</head>

<body>
<div align="center">
	<div id="fond">
		<!-- Contenu -->
		<div align="left" id="entete"><h1>Chambres d'hôtes</h1></div>
		<div align="left" id="presentation">
		Vacances en agrotourisme au coeur du pays basque français.
		<br>
		<br>Maison de charme avec piscine entourée de chênes centenaires, située à seulement 3 kms de la plage de Saint-Jean-de-Luz.
		<br>Elle produit localement son propre cidre, c'est-à-dire développe une exploitation de pommiers à cidre avec transformation.
		<br>Pour les amoureux de l'équitation elle offre une pension pour chevaux dans des installations modernes.
		<br>Vous serez séduits par un cadre accueillant et pittoresque.
		<br>
		<br>Chambres d'hôtes doubles 1 personne avec salle de bain à 55 la nuitée.
		<br>Chambres d'hôtes doubles 2 personnes avec salle de bain à 65 la nuitée.
		<br>15 pour chaque personne supplémentaire.
		<br>Petit-déjeuner compris.
		</div>
		<div id="canvas"></div>
	</div>
</body>
</html>