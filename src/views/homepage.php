<?php $title = "Le blog de l'AVBN"; ?>

<?php ob_start(); ?>
<h1>Le super blog de l'AVBN !</h1>
<p>Derniers billets du blog :</p>

<?php $content = ob_get_clean(); ?>