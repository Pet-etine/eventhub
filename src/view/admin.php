<?php $this->layout('template', ['title' => 'Ylläpito']) ?>

<h2>Käyttäjien hallinta</h2>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Nimi</th>
    <th>Sähköposti</th>
    <th>Rooli</th>
  </tr>

  <?php foreach ($kayttajat as $user): ?>
  <tr>
    <td><?= htmlspecialchars($user['idhenkilo']) ?></td>
    <td><?= htmlspecialchars($user['nimi']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['rooli']) ?></td>
  </tr>
  <?php endforeach; ?>

</table>
