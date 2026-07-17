<?php

declare(strict_types=1);

/** @var \ContactForm\Form\FormData $data */

$fields = $data->toArray();

?>

Neue Kontaktanfrage

Es wurde eine neue Nachricht über das Kontaktformular übermittelt.

==================================================

<?php foreach ($fields as $label => $value): ?>

<?= strtoupper($label) ?>


<?= $value ?>


--------------------------------------------------

<?php endforeach; ?>