<?php

/* index.php */
class __TwigTemplate_c5d48ccf30a0c4b88541a9a737ac3399aec76bef4de4e380f4e5f0a5b92c2def extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\" />
<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
<title>Chambres d'hôte - Page simple de test</title>
<style type=\"text/css\">
<!--
body
{
    background-color: #FFFF99;
}
div {
\tborder-radius: 50px;
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
    font-family: \"Century Gothic\", \"Verdana\", \"Arial\";
    font-weight: normal;
    color: #006F3C;
    text-align: center;
\t\tvertical-align: middle;
}
#presentation {
    position:absolute;
    width:352px;
    height:432px;
    z-index:4;
    left: 78px;
    top: 168px;
    font-family: \"Century Gothic\", \"Verdana\", \"Arial\";
    font-size: 14px;
    font-weight: normal;
    color: #006F3C;
    text-align: justify;
    padding-right: 10px;
    overflow : auto;
\t\tborder-radius: 0px;
}
#canvas {
    position:absolute;
    width:400px;
    height:400px;
    z-index:1;
    left: 500px;
    top: 150px;
    background-color: #B9D7D9;
\t\tborder-radius: 100%;
\t\tborder: 2px solid #339966;
}
-->
</style>
</head>

<body>
<div align=\"center\">
\t<div id=\"fond\">
\t\t<!-- Contenu -->
\t\t<div align=\"left\" id=\"entete\"><h1>Chambres d'hôtes</h1></div>
\t\t<div align=\"left\" id=\"presentation\">
\t\tVacances en agrotourisme au coeur du pays basque français.
\t\t<br>
\t\t<br>Maison de charme avec piscine entourée de chênes centenaires, située à seulement 3 kms de la plage de Saint-Jean-de-Luz.
\t\t<br>Elle produit localement son propre cidre, c'est-à-dire développe une exploitation de pommiers à cidre avec transformation.
\t\t<br>Pour les amoureux de l'équitation elle offre une pension pour chevaux dans des installations modernes.
\t\t<br>Vous serez séduits par un cadre accueillant et pittoresque.
\t\t<br>
\t\t<br>Chambres d'hôtes doubles 1 personne avec salle de bain à 55 la nuitée.
\t\t<br>Chambres d'hôtes doubles 2 personnes avec salle de bain à 65 la nuitée.
\t\t<br>15 pour chaque personne supplémentaire.
\t\t<br>Petit-déjeuner compris.
\t\t</div>
\t\t<div id=\"canvas\"></div>
\t</div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.php";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.php", "E:\\localweb\\moowgly\\pilote\\src\\static\\twig\\index.php");
    }
}
