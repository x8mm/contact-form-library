<?php

declare(strict_types=1);

/** @var \ContactForm\Form\FormData $data */

$fields = $data->toArray();

$labels = [
    'name' => 'Name',
    'email' => 'E-Mail',
    'subject' => 'Betreff',
    'message' => 'Nachricht',
];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neue Kontaktanfrage</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #222;">

<h2>Neue Kontaktanfrage</h2>

<p>Es wurde eine neue Nachricht über das Kontaktformular übermittelt.</p>

<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
    <tbody>

    <?php foreach ($fields as $key => $value): ?>

        <tr>
            <th
                align="left"
                style="background:#f5f5f5;width:180px;"
            >
                <?= htmlspecialchars($labels[$key] ?? ucfirst($key), ENT_QUOTES, 'UTF-8') ?>
            </th>

            <td>
                <?= nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) ?>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>
</table>

</body>
</html>
