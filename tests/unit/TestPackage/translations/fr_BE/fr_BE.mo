��    .      �  =   �      �     �  	   �  
               
   "  
   0     >  
   G     R  
   W     b     n  
   z     �     �     �     �     �     �  	   �     �     �     �     �       
        ,     ;     L     ]  
   p     ~  
   �     �  
   �     �     �     �  
   �     �     �               
  �       �  
   �  
   �     �    �  �   	     �	  
   �	  !   

     ,
  5   A
  -   w
  O   �
  c   �
     Y  !   v     �  	   �  
   �     �  �   �     �     �  -  �  ~   �  �   o  �   ?  >   �  j    <   �     �  5   �       �   /  �     �   �  O     �   p  �   J  1   �  ^   )     �     �     �     �               -                                 .      )                                           
       $   (                  
            +   #   %   &                  	          ,         "   '   *   !          about configure contribute database docker.about docker.browse docker.devbox download home.intro i18n index.crew index.crew2 index.crew3 index.install index.install2 index.intro installation language layouts learn learn.777 learn.composer learn.config learn.config.drop learn.config.registry learn.db learn.folders learn.globally learn.i18n.about learn.i18n.usage learn.install.bone learn.layouts learn.learn learn.logs learn.logs.usage learn.mail learn.mail.hog learn.routes learn.routes.params learn.tagline learn.vhosts logs mail routes visit Project-Id-Version: Bone
POT-Creation-Date: 2018-11-06 18:48+0100
PO-Revision-Date: 2018-11-06 19:39+0100
Last-Translator: Derek McLean <delboy1978uk@gmail.com>
Language-Team: 
Language: fr_BE
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 1.5.3
X-Poedit-KeywordsList: t;translate;gettext_noop
X-Poedit-Basepath: ../../../
X-Poedit-SourceCharset: UTF-8
X-Poedit-SearchPath-0: src
 Sur Configurer Contribuer Base de données Bone est livré avec un fichier docker-compose.yml dans le projet. Vous pouvez donc instantanément lancer un serveur de développement si vous utilisez Docker (testé à l'aide d'une machine virtuelle VirtualBox par défaut). Ajoutez simplement ceci à votre fichier hosts Ensuite, vous pouvez accéder au site à l'adresse https://awesome.scot dans votre navigateur. Bien sûr, si vous n'utilisez pas docker, vous pouvez l'ajouter à votre pile LAMP de la manière habituelle. Docker boîte de développement Télécharger Un framework PHP pour les pirates Internationalisation Nous sommes toujours à la recherche de contributeurs pour aider Bone MVC à être encore meilleur. Si vous aimez Bone et souhaitez contribuer à son développement, rejoignez-le! You can download the Bone MVC source code by browsing over to Github. But we recommend you install  et le faire de cette façon. Un framework PHP pour les pirates Installation La langue Mises en page Apprendre Bone MVC Framework Rendez le dossier de données accessible en écriture. 777 donne à tout le monde un accès en écriture, donc définissez-le à 775 avec votre utilisateur Apache du groupe. D'abord, assurez-vous d'avoir Le dossier de configuration Vous pouvez déposer n'importe quel nombre de fichiers <span class = "label label-success">.php</span> dans la <span class="label label-success">config/</span>. Assurez-vous qu'ils renvoient un tableau avec la configuration. Vous pouvez remplacer la configuration en fonction de l'environnement var <span class="label label-success">APPLICATION_ENV</span>, par exemple si l'environnement était en production il chargerait la configuration supplémentaire du sous-répertoire de production.</ p><p> Il existe plusieurs fichiers de configuration par défaut: Dans vos fichiers de configuration, vous pouvez ajouter ce que vous voulez. Il est stocké dans le registre Bone\Mvc\Registry. Définissez vos informations d'identification de base de données par défaut dans le fichier config / db.php principal, ainsi que toute configuration spécifique à l'environnement dans un sous-répertoire. Vous pouvez voir un dossier config, data, public et src. Conserver le dossier du fournisseur, c’est là que composeur installe les dépendances du projet. ou si vous n'avez pas installé le compositeur globalement ... Bone prend en charge la traduction dans différents lieux. Les fichiers de traduction (gettext .po et .mo) doivent être placés dans data / translations, dans un sous-répertoire de la langue, par exemple data / translations / en_GB / en_GB.po. Vous pouvez définir les paramètres régionaux par défaut et un tableau des paramètres régionaux pris en charge. Pour utiliser le traducteur, vous pouvez simplement appeler: Ensuite, installez Bone. Ignorer cette config. C'est vieux non-sens obsolète. Apprendre Bone Framework MVC Bone utilise monolog/monolog. On peut trouver n logs dans <span crew="label label-success">data/logs</ span>. Actuellement, nous ne prenons en charge que les fichiers en écriture, mais vous pouvez ajouter autant de canaux que vous aimez: Bone utilise Laminas Mail. Pour configurer le client de messagerie, il suffit de déposer votre configuration (voir zend mail docs) Bone utilise Laminas Mail. Pour configurer le client de messagerie, il suffit de déposer votre configuration (voir zend mail docs) Si vous utilisez la boîte Docker fournie par bone, vous avez également le formidable MailHog à votre disposition. Naviguez vers awesome.scot:8025 et vous verrez apparaître une boîte de réception électronique complète, de sorte que vous n’aurez jamais à vous soucier des courriels de développement atteignant le monde réel. Les itinéraires suivent un modèle par défaut de /controller/action/param/value/nextparam/nextvalue/etc/etc<br> Vous pouvez également remplacer les itinéraires en les définissant dans le tableau de configuration: Lors de la définition des itinéraires, les variables obligatoires dans l'URI ont deux points comme: id <br /> Les uri vars facultatifs sont entourées par [] comme [: id] Il est facile de se lancer avec un nouveau projet Dans vos hôtes virtuels Apache, définissez la racine du document en tant que dossier public. Les journaux Courrier Routes Visite 