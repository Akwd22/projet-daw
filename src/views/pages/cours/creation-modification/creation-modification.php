<?php require 'views/components/header/header.php';
require 'views/components/footer/footer.php';
require 'views/components/radio/radio.php';
require 'views/components/message/message.php';

function afficherVue(bool $isEditMode, $cours = null)
{


    // PDF CONTAINER
    // Cette div doit s'afficher uniquement quand nous sommes en mode d'édition, sinon nous sommes en mode création
    $pdf_container = function () {
        global $isEditMode; //On importe la variable $isEditMode donnée en parametre dans le scope de la fonction
        if ($isEditMode === true) { //mode edition
            return <<<HTML
                <div class="creation-container-pdf-container">
                    <label for="pdfFile" id='pdf-file'>Fichier PDF</label>
                    <input type="file" name="pdfFile" id="pdfFile">
                    <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                </div>
HTML;
        }
    };

    // FONCTION POUR REMPLIR LES CHAMPS EXISTANTS SI MODIFICATIONS
    $handleForm_isEditMode = function ($type) {
        global $isEditMode;
        global $cours;

        $value = "";
        if ($isEditMode and $type === "titre")
            $value = $cours->getTitre();
        else if ($isEditMode and $type === "tempsMoyen")
            $value = $cours->getTempsMoyen();
        else if ($isEditMode and $type === "niveauRecommandé")
            $value = $cours->getNiveauRecommande();
        else if ($isEditMode and $type == "description")
            $value = $cours->getDescription();

        return $value;
    };

    // Ecriture des bons mots suivant l'état de $isEditMode
    $modification_creation = function ($type) {
        global $isEditMode;
        $value = "";

        if ($isEditMode === true and $type === "titre") { //Si mode édition et titre 
            $value = "Modification cours";
        } else if ($isEditMode === false and $type === "titre") { //Si mode création et titre 
            $value = "Création cours";
        } else if ($isEditMode === true and $type === "btn") { //Si mode édition et boutton 
            $value = "'Modifier le cours'";
        } else if ($isEditMode === false and $type === "btn") { //Si mode création et boutton 
            $value = "'Créer le cours'";
        }

        return $value;
    };

?>

    <head>
        <?php infoHead('Modifier son profil', 'Modifier son profil', '/views/pages/cours/creation-modification/creation-modification.css'); ?>
        <link rel="stylesheet" type="text/css" href="/views/components/header/header.css">
        <link rel="stylesheet" type="text/css" href="/views/components/footer/footer.css">
    </head>

    <body>
        <div id="mainContainer">
            <header>
                <?php createrNavbar(); ?>
            </header>
            <main class="coursCreation-page">
                <div class="creation-container">
                    <div class="creation-container-form-structure">
                        <form action="" class="creation-container-form">
                            <!-- TITRE -->
                            <h2 class="creation-container-titre"><?php echo $modification_creation("titre") ?></h2>

                            <!-- TITRE CONTAINER -->
                            <div class="form-container-input creation-container-titre-container">
                                <label for="input-titre">Titre du cours</label>
                                <input class="input l" type="text" name="titre" id="input-titre" placeholder='Titre du cours' value="<?php echo $handleForm_isEditMode("titre"); ?>">
                            </div>

                            <!-- TEMPS MOYENS -->
                            <div class="form-container-input creation-container-temps-container">
                                <label for="temps-input">Temps moyen de complétion</label>
                                <input class="input l" type="text" name='tempsMoyen' id="temps-input" placeholder='Durée du cours' value="<?php echo $handleForm_isEditMode("tempsMoyen"); ?>">
                            </div>

                            <!-- NIVEAU RECOMMANDÉ -->
                            <div class="form-container-input creation-container-niveau-container">
                                <label for="niveau-select">Niveau recommandé</label>
                                <?php ?>
                                <select name="niveau-recommande" id="niveau-select">
                                    <?php
                                    $arr = EnumNiveauCours::getFriendlyNames();
                                    $cours !== null ? $nomAffichage = $arr[$cours->getNiveauRecommande()] : $nomAffichage = "Sélectionner une valeur";

                                    echo "<option>{$nomAffichage}</option>";
                                    foreach ($arr as $niv => $nom) {
                                        echo "<option value=" . $niv . ">" . $nom . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- DESCRIPTION -->
                            <div class="form-container-input creation-container-description-container">
                                <textarea name="description-texte" id="description-area" placeholder="Description du cours"><?php echo $handleForm_isEditMode("description"); ?></textarea>
                            </div>
                            <!-- NEW IMAGE -->

                            <div class="form-container-input creation-container-image-container">
                                <label for="image" id='new-image'>Nouvelle image</label>
                                <input type="file" name="image" id="image">
                                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                            </div>

                            <!-- FORMAT -->
                            <div class="creation-container-format-container">
                                <label for="radio-format">Format du cours</label>
                                <?php createRadio('radio-format-texte', 'radio-format', 'Texte', 'texte', 'm', 'enabled', ($cours !== null ? ($cours::FORMAT === EnumFormatCours::TEXTE ? "checked" : "") : "checked")); ?>
                                <?php createRadio('radio-format-video', 'radio-format', 'Vidéo', 'video', 'm', 'enabled', ($cours !== null ? ($cours::FORMAT === EnumFormatCours::VIDEO ? "checked" : "") : "uncheked")); ?>
                            </div>

                            <!-- FICHIER PDF -->
                            <?php echo $pdf_container() ?>

                            <!-- BOUTON SUBMIT -->
                            <input class="default m" type="submit" id="submit-btn" value=<?php echo $modification_creation('btn') ?>>

                            <hr>




                            <div class="delete-container-form">
                                <label for="btn-delete">Supprimer le cours</label>
                                <a href='#'><input class="default s" type="button" id="btn-delete" value="Supprimer"></a>
                            </div>
                    </div>

                    <!-- CONTAINER DES LIENS VERS LES VIDEOS -->
                    <div class='lien-container'>
                        <div class="lien-container-form">
                            <h2 class="creation-container-titre">Liens vidéo de la formation</h2>

                            <div class="lien-container-lien-container">

                                <div class="lien-container-list-lien">
                                    <?php
                                    $nbLiensValue = 1;

                                    if ($cours !== null) {
                                        foreach ($cours->getVideosUrl() as $n => $url) {
                                            $n++;
                                            echo "<div class='lien-container-input-container'>";
                                            echo "<label for='input-lien'>$n</label>";
                                            echo "<input class='input m' type='text' name='lien{$n}' id='input-lien' placeholder='Lien de la vidéo youtube' value='{$url}'>";
                                            echo "</div>";
                                            $nbLiensValue++;
                                        }
                                    } else {
                                        echo "<div class='lien-container-input-container'>";
                                        echo "<label for='input-lien'>$nbLiensValue</label>";
                                        echo "<input class='input m' type='text' name='lien{$nbLiensValue}' id='input-lien' placeholder='Lien de la vidéo youtube' value='{$nbLiensValue}'>";
                                        echo "</div>";
                                        $nbLiensValue++;
                                    }
                                    echo "<input class='lien-container-hidden' type='hidden' name='nbLiens' value={$nbLiensValue}>";
                                    ?>
                                </div>


                                <!-- BOUTON D'AJOUT -->
                                <input class="default s" type="button" id="submit-btn-lien" value='Ajouter un lien de vidéo'>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </main>
        </div>
        <?php createFooter(); ?>
        <script src="../views/pages/cours/creation-modification/creation-modification.js"></script>
    </body>
<?php
}
