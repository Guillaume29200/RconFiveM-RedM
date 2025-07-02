<h1>📡 PHP RCON Client for FiveM / RedM</h1>

<p>
Cette classe PHP permet de se connecter à un serveur <strong>FiveM</strong> ou <strong>RedM</strong> via RCON, d’envoyer des commandes, et de récupérer les réponses.
</p>

<h2>🚀 Installation</h2>

<ol>
  <li>Téléchargez et placez le fichier <code>RconFiveM.php</code> dans votre projet.</li>
  <li>Incluez-le dans votre script PHP :</li>
</ol>

<pre><code>require_once 'Class_rconFiveM.php';
</code></pre>

<h2>📘 Exemple d'utilisation</h2>

<pre><code>&lt;?php
require_once 'Class_rconFiveM.php';

try {
    // Connexion au serveur RCON
    $rcon = new RconFiveM('127.0.0.1', 30120, 'votre_mot_de_passe_rcon');
    $rcon-&gt;connect();

    // Envoi d'une commande, exemple : liste des ressources
    $response = $rcon-&gt;sendCommand('resources');
    echo "Réponse :\n$response\n";

    // Déconnexion propre
    $rcon-&gt;disconnect();
} catch (Exception $e) {
    echo "Erreur : " . $e-&gt;getMessage();
}
?&gt;
</code></pre>

<h2>🧪 Commandes RCON utiles (FiveM)</h2>

<ul>
  <li><code>resources</code> : Affiche les ressources actives sur le serveur</li>
  <li><code>status</code> : Infos sur le serveur (hostname, joueurs, map, etc.)</li>
  <li><code>fpsmeter</code> : Si le plugin <code>fpsmeter_server</code> est installé, retourne les stats de performance</li>
</ul>

<h2>❗ Informations complémentaires</h2>

<ul>
  <li>Le port RCON est souvent <code>30120</code> pour FiveM, mais cela dépend de votre configuration.</li>
  <li>La classe gère automatiquement les réponses multi-paquets (split responses).</li>
  <li>Les erreurs (auth, socket, etc.) lèvent des <code>Exception</code> PHP.</li>
</ul>

<h2>🔒 Sécurité</h2>
<ul>
  <li>Assurez-vous que le port RCON est bien protégé (firewall ou IP whitelist).</li>
  <li>Le mot de passe RCON doit être fort et unique.</li>
</ul>

<h2>📂 Licence</h2>
<p>Projet open-source sous licence MIT.</p>
