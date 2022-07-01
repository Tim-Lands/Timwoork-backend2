<?php

return [
    /* messages de vérification de ------------------------------ ------------------------------ */
    'username' => [
        'required' =>'Nomd’utilisateur requis'
    ],
    'password' => [
        'required' => 'mot de passe requis'
    ],
    "validation" => [
        "required_name_ar" => "Nom arabe requis",
          "required_precent_deducation" => "Taux de déduction requis",
          "unique" => "Ce champ existe déjà, vérifiez vos données",
        "min_description" => "Le nombre de lettres ne doit pas être inférieur à 8 caractères",
        'string' => 'Le champ doit être composé de lettres, de chiffres et de symboles ',
        'email' => 'le champ doit être sous forme d’un email',
        'numeric' => 'la valeur d’entrée doit être des nombres',
        'exists' => 'Ce champ n’est pas identique aux données existantes',
        'file' => 'Ce champ doit être un fichier',
        'zip' => 'doit être une extension zip',
        'icon' => 'Vous devez entrer l’icône',
        "number_developments" => "Le champs de mises à niveau est requis",
        'price_developments' => "Le prix maximal pour les mises à niveau/développement est requis",
        'numbre_sales' => 'Le niveau du nombre de vente est requis',
        'value_bayer' => 'Le champ de valeur d’achat est requis',
        'type_level' => "Le champ type de niveau est requis",
        "parent_id" => "La catégorie principale est requise",
        "thumbnail" => "L’image en miniature est requise",
        'thumbnail_image' => "La miniature doit être sous forme  png , jpg, jpeg",
        'thumbnail_required' =>  "Vous devez télécharger une image en miniature",
        "thumbnail_mimes" => "La miniature doit être sous forme de png , jpg, jpeg",
        "thumbnail_size" => "La taille de miniature ne doit pas dépasser2MB",
        "images_mimes" => "L’image d’affichage doit être au format png , jpg, jpeg",
        "images_size" => "La  taille de l’image d’affichage ne doit pas dépasser 2MB",
        "file_mimes" => "Le fichier doit être un pdf",
        "file_size" => "Le fichier ne doit pas dépasser 2MB",
        "url" => "Ce champ doit être sous la forme d’un lien URL",
        "title_required" => "Le titre du service est nécessaire",
        "title_size" => "Le titre comporte au moins 20 caractères et au plus 60 caractères.",
        "title_required" => "Le titre du service est nécessaire",
        "subcategory_required"=> "La sous-catégorie est nécessaire",
        "tags_required" => "Vous devriez mettre au moins un tag",
        "tags_value_required" => "Aucun tag ne doit être vide",
        "content_required" => "Le contenu du service est nécessaire",
        "buyer_instruct_required" => "Les instructions d’achat du service sont requises",
        "price_required" => "Le prix du service est requis",
        "price_two_after_number" => "Le prix doit contenir deux chiffres seulement après la virgule",

        "developpements_title_required" => "Le titre de mise à niveau/ développement est requis",
        "developpements_duration_required" => "Ladurée de mise à niveau/ développement est requise",
        "developpements_duration_great_zero" => "La durée de mie à niveau/ développement est d’au moins un jour",
        "developpements_price_required" => "Prix de mise à niveau/ développement requis",
        "developpements_price_great_zero" => "Le prix de mise à niveau/développement doit être supérieur à 0",
        "developpements_price_two_after_number" => "Le prix doit avoir deux chiffres seulement après la virgule",
        "rating_required" => "Notation requise",
        "rating_between " => "La note doit être comprise entre 0 et 1",
        "commentaire_required" => "Commentaire requis",
        "product_id" => "Service requis",
        "file_resource_required" => "Le projet est requis",
        "file_resource_file" => "Le projet doit être un fichier",
        "file_resource_required" => "Projet requis",
        "file_resource_mimes" => "Le fichier doit être en format zip",
        "old_password_required" => "Ancien mot de passe requis",
        "password_required" => "Mot de passe requis",
        "password_confirmed" => "Le mot de passe n’est pas identique",
        "password_confirmation_required" => "Le mot de passe identique est requis",
        "conversation_title" => "Le titre de conversation est requis",
        "receiver_id" => "Destinataire requis",
        "initial_message_required" => "Message initial requis",
        "email_required" => "Vous devez entrer un e-mail",
        "message_required" => "Message requis",
        "bio_required" => "Profil requis",
        "bio_min" => "Le profil doit contenir au moins 26 caractères",
        "portfolio_required" => "Votre présentation est requise",
        "skills_required" => "Compétences requises",
        "first_name_required" => "Prénom requis",
        "first_name_min" => "Prénom très court",
        "last_name_required" => "Nom requis",
        "last_name_min" => "Le nom est trop court",
        "username_required" => "Nom d’utilisateur requis",
        "date_of_birth_required" => "Date de naissance requise",
        "genre_required" => "sexe requis",
        "phone_number_required" => " Vous devez entrer le numéro de téléphone",
          "country_id" => "Vous devez entrer le pays",
        "avatar_required" => "Photo de profil requise",
        "languages" => "Langue requise",
        "provider_id" => "Fournisseur requis",
        "code_required" => "Code de confirmation requis",
        "code_size" => "Le code de confirmation doit contenir au moins 6 chiffres et 8 au maximum",
        "professions" => "Profession",
        "skills" => "Compétences nécessaires",
        "prix_between" => "Le prix doit être compris entre 5 $ et 1000 $",
        "duration_required" => "Vous devez entrer la durée",
        "tags_min" => "Le tag doit contenir au moins 1 caractère",
        "password_min" => "Le mot de passe doit contenir au moins 8 lettres et chiffres",
        "name_en_required" => "Ce champ doit être saisi en anglais",
        "content_min" => "Le contenu doit comporter au moins 30 caractères",
        "subject_required" => "Vous devez entrer l’objet du message",
        "Full_name_required" => "Vous devez entrer le nom complet",
        "type_message_required" => "Vous devez entrer le type de message",
        "message_required" => "Vous devez entrer le texte du message",
        "email_unique" => "Cette adresse électronique existe déjà",
        "phone_unique" => "Ce numéro de téléphone existe déjà",
        "phone_regex" => "Le numéro de téléphone doit avoir des chiffres",
        "phone_max" => "Le numéro de téléphone doit contenir au moins 8 chiffres",
        "phone_min" => "Le numéro de téléphone doit contenir au plus de 16 chiffres",
        'phone_number_numeric' => 'Le numéro de téléphone doit contenir des chiffres',
        'code_phone_required' => 'Vous devez entrer le code téléphonique de votre pays',
        'phone_digits_between'=> "Le numéro de téléphone doit être compris entre 4 et 16 chiffres",
        'username_regex' => 'Le nom d\'utilisateur ne doit contenir que des lettres et des chiffres'
    ],

    /* --------------------------------- Messages -------------------------------- */
    "oprations" => [
        "get_data" => "L’objet a été apporté avec succès",
        "add_success" => "L’élément a été ajouté avec succès",
        "update_success" => "L’élément a été modifié avec succès",
        "delete_success" => "L’élément a été supprimé avec succès",
        "restore_delete_success" => "L’élément a été restauré avec succès",
        "get_all_data" => "Tous les éléments sont apportés avec succès",
        'nothing_this_operation' => 'Vous ne pouvez pas poursuivre cette opération, vérifiez vos données'
    ],

    "mode" => [
        "active_dark_mode" => "Le mode a été changé en mode jour",
        "disactive_dark_mode" => "Le mode a été changé en mode nuit",
    ],

    "user" => [
        "send_eamil_reset_password" => "Le lien de récupération du mot de passe a été envoyé à votre adresse électronique",
        "erreur_verify" => "Quelque chose s’est mal passé... Votre code de récupération de mot de passe est introuvable",
        "success_verify_reset_password" => "Votre code de récupération de mot de passe est correct",
        "success_reset_password" => "Le mot de passe a été réinitialisé avec succès",
        "fieled_operation" => "L’opération a échoué",
        "error_login" => "Les informations que vous avez saisies sont erronées",
        "email_already" => "Cet email existe déjà",
        "logout" => "Déconnexion réussie",
        "logout_all" => "Vous êtes déconnecté de tous les appareils",
        "user_not_banned" => "Cet utilisateur n’est pas bloqué",
        "user_banned" => "Utilisateur bloqué",
        "ban_success" => "L’utilisateur a été bloqué avec succès",
        "unban_success" => "Le blocage de l’utilisateur est annulé avec succès",
    ],
    "seller" => [
    "active_product" => "Le service a été activé avec succès",
    "disactive_product" => "Le service a été désactivé avec succès",
    "disactived_product" => "Service suspendu",
    "actived_product" => "Service actif",
    "my_products" => "Vos services ont été trouvés",
    "add_profile_seller" => "Le profil du vendeur a été enregistré avec succès",
    ],

    "product" => [
      "number_of_products_seller" => "Vous ne pouvez pas ajouter un service au-dessus du nombre maximum spécifié pour vous",
      "success_step_one" => "La première phase a été créée avec succès",
      "success_step_two" => "La phase 2 a été créée avec succès",
      "success_step_three" => "La phase 3 a été créée avec succès",
      "success_step_four" => "La phase 4 a été créée avec succès",
      "success_upload_thumbnail" => "L’image a été téléchargée avec succès",
      "success_upload_images" => "Les images d’affichage ont été téléchargées avec succès",
      "success_step_final" => "Les étapes ont été terminées et créées avec succès",
      "numbre_developments_max" => "La mise à niveau/ le développement ne peut pas être ajouté au-dessus du nombre maximum spécifié pour vous",
      "price_development_max" => "Le prix ne peut pas être ajouté au-dessus du nombre maximum spécifié pour vous",
      "thumbnail_required" => "Vous devez importer la photo de profil",
      "count_galaries" => "Le nombre d’images téléchargées ne doit pas dépasser 5 ni moins de 1",
      "success_upload_galaries" => "Les images d’affichage ont été téléchargées avec succès",
      "upload_images" => "Vous devez importer des photos",
      "delete_galary" => "Une image a été supprimée de la galerie",
      "thumbnail_required" => "Vous devez importer la photo de profil",
      "galaries_required" => "Vous devez télécharger une vue",
      "cause_required" => "Vous devez entrer la raison du rejet",
      "accepted_product" => "Ce service a déjà été accepté",
      "rejected_product" => "Ce service a déjà été rejeté"
    ],

    /* ------------------------------ ----------------------------- Messages d’erreur */
    "errors" => [
        "error_database" => "Quelque chose s’est mal passé dans la base de données , assurez-vous de cela",
        "element_not_found " => "Cet élément n’existe pas",
        "url_not_found" => "Ce lien n’existe pas",
        "upload_images" => "Vous devez importer des photos",
        "too_many_attempts" => "Vos tentatives autorisées ont été dépassées, attendez une minute jusqu'à ce que vous puissiez répéter le processus",
    ],
/* Tableau de bord ------------------------------- ------------------------------*/
    "dahshboard" => [
        "statistic_dashboard" => "Statistiques du tableau de bord",
        "active_status_product" => "Le service a été activé avec succès",
        "reject_status_product" => "Service rejeté",
        "disactive_status_product" => "Service désactivé",
 //Directeur
        "get_login" => "Connecté avec succès",
        "get_logout" => "Déconnecté avec succès",
        "valide_login" => "Les informations que vous avez saisies sont erronées",
        "get_product_actived" => "La liste des services activés a été trouvée",
        "get_product_rejected" => "La liste des services rejetés a été trouvée",
        "failures_send_email" => "Non envoyé à l’e-mail, réessayez",
         "success_send_message_to_email" => "Le message a été envoyé à l’e-mail avec succès",
    ],
//Panier
    "cart" => [
      "cartitem_found" => "Cet article existe dans le panier, ajoutez un autre élément",
      "product_not_buying" => "Vous ne pouvez pas acheter ce service, vérifiez vos données",
      "same_developments" => "Les mises à niveau/ développements qui ont été introduits ne correspondent pas à ce service",
      "not_found_cartitem " => "pas de panier invendu,  veuillez ajouter un nouveau panier.",
      "cart_not_found" => "Pas de panier, veuillez racheter",
      "catitem_not_found" => "Il n’y a pas d’articles dans le panier, veuillez racheter",
      "can_not_buying" => "Vous ne pouvez pas acheter ce nombre de services, suivez les instructions incluses dans la carte d’achat",
      "profile_not_complete" => "Vous ne pouvez pas acheter, vous devez activer votre compte",
      "product_work" => "Service actif"
    ],
//Transaction entre le vendeur et l’acheteur
    "item" => [
      "not_may_this_operation" => "Vous ne pouvez pas réaliser cette opération, vérifiez vos données",
      "not_found_item_reject" => "La demande n’existe pas ... Annuler la commande",
      "accept_item_by_seller" => "La commande a été acceptée par le vendeur",
      "reject_item_by_seller" => "La commande a été rejetée par le vendeur",
      "accept_item_by_buyer" => "La demande a été acceptée par l’acheteur",
      "reject_item_by_buyer" => "La demande a été rejetée par l’acheteur",
      "must_be_dilevery_resources" => "Vous devez importer le projet avant la livraison",
        "dilevery_resources_founded" => "Le projet a déjà été livré, vérifiez vos données",
      "dilevery_resources_success" => "Le projet a été importé et livré par le vendeur.",
      "ressource_not_found" => "Le projet n’existe pas",
      "resource_rejected" => "L’état du  projet est rejeté, vérifiez vos données.",
      "resource_not_dilevery" => "Le projet n’a pas été livré, vérifiez vos données.",
      "ressource_dilevered" => "Le projet a été reçu par l’acheteur",
       "ressource_not_dilevered" => "Le projet a été rejeté par l’acheteur",
      "request_seller_sended" => "Une demande d’annulation a été envoyée par le vendeur, accepter ou rejeter l’annulation de la commande",
      "request_sended" => "Demande d’annulation envoyée, veuillez attendre l’acceptation ou le rejet",
      "request_buyer_success" => "La demande d’annulation a été envoyée par l’acheteur",
       "request_not_found" => "Aucune demande envoyée, vérifiez vos données",
       "request_accepted_by_seller" => "La demande d’annulation a été acceptée par le vendeur et le service a été refusé",
      "request_rejected_by_seller" => "La demande d’annulation a été rejetée par  le vendeur, l’administration sera contactée si le problème n’est pas résolu dans les 24 heures",
      "request_rejected_by_buyer" => "la demande d’annulation a été rejetée par  l’acheteur, l’administration  sera contactée si le problème n’est pas résolu dans les 24 heures.",
       "is_dilevered" => "Livraison réussie",
      "attachment_size" => "Le fichier ne doit pas dépasser 70 MB",
       "request_modified_by_buyer" => "La demande d’annulation a été envoyée par l’acheteur",
      "accepted_modified_by_seller" => "La modification a été acceptée par le vendeur",
      "reject_modified_by_seller" => "La modification a été rejetée par  le vendeur, l’administration sera contactée si le problème n’est pas résolu dans les 24 heures",
       "found_request_modified" => "La demande de modification existe déjà",
      "found_request_rejected" => "La demande d’annulation existe déjà",
      "resolve_the_conflict_between_them" => "Différend entre les deux parties résolu"
    ],
//Filtrage
    "filter" => [
      "filter_success" => "Le filtrage a été effectué avec succès",
      "filter_field" => "Aucun résultat trouvé"
    ],
    "conversation" => [
     "conversation_success" => "La conversation a été ajoutée avec succès",
    "Message_success" => "Le message a été envoyé avec succès",
    ],
    "contact" => [
        "not_found_url" => "Le fichier doit être téléchargé sur Google Drive ou Dropbox",
         "success_message_contact" => "Le message a été envoyé avec succès",
        "cannot_sent_before_48" => "Vous devez attendre 48 heures pour pouvoir envoyer un nouveau message",
        "failures_send_email" => "Non envoyé à l’e-mail, veuillez réessayer",
        "success_send_message_to_email" => "Le message a été envoyé à l’e-mail avec succès",
    ],
    "bank" => [
        "city_required" => "Vous devez entrer votre province",
        "state_required" => "Vous devez entrer votre ville",
        "address_line_one_required" => "Vous devez entrer l’adresse",
        "code_postal_required" => "Vous devez entrer le code postal",
         "id_type_required" => "Vous devez entrer le type de pièce jointe",
         "attachments_required" => "Vous devez importer les pièces jointes",
        "attachments_size" => " Le fichier ne doit pas dépasser 2 MB",
        "attachments_image" => "Les pièces jointes doivent être sous forme d’images",
        "bank_name_required" => "Vous devez entrer le nom de la banque",
        "bank_branch_required" => "Vous devez entrer le sous-nom de la banque",
        "bank_swift_required" => "Vous devez entrer un transfert bancaire (swift)",
        "bank_swift_required" => "Vous devez entrer un transfert bancaire (swift)",
        "bank_swift_required" => " Vous devez entrer un transfert bancaire (swift)",
        "bank_iban_required" => "Vous devez entrer l’IBAN",
        "bank_number_account_required" => "Vous devez entrer le code de banque",
        "bank_adress_line_one_required" => "Vous devez entrer l’adresse de la banque",
        "amount_numeric" => "La somme tirée doit être sous forme de chiffres",
        "amount_required" => "Vous devez entrer le montant",
        "amount_PayPal_gte" => "Le montant retiré de PayPal doit  être supérieur ou égal à 10",
        "amount_wise_gte" => "Le montant retiré de Wise doit  être supérieur ou égal à 10",
        "amount_bank_gte" => "Le montant retiré d’une banque doit être supérieur ou égal à 50",
        "amount_bank_transfer_gte" => "Le montant retiré d’un virement bancaire doit être supérieur ou égal à 50",
        "pending_withdrawal" => "Vous avez un retrait en attente",
        "Success_PayPal_withdrawal" => "Le retrait de PayPal a été enregistré avec succès",
        "Success_wise_withdrawal" => "Le retrait de Wise a été  enregistré  avec succès",
        "Success_bank_withdrawal" => "Le retrait vers une banque a été enregistré avec succès",
        "Success_bank_transfer_withdrawal" => "Le retrait a été enregistré avec succès dans un virement bancaire",
        "not_enough_balance" => "Votre solde n’est pas suffisant pour effectuer cette opération",
        "account_PayPal_not_found" => "Vous devez ajouter un compte PayPal pour pouvoir effectuer un retrait",
        "account_wise_not_found" => " Vous devez ajouter  un compte Wise  pour pouvoir effectuer un retrait.",
        "account_bank_not_found" => "Vous devez ajouter un compte bancaire pour pouvoir effectuer un retrait",
        "account_bank_transfer_detail_not_found" => "Vous devez ajouter un virement bancaire pour pouvoir effectuer un retrait."


    ],

    "type_payment" => [
      "active_payment" => "Le portail a bien été activée avec succès",
      "disactive_payment" => "Le portail a été désactivée avec succès",
      "precent_of_payment_required" => "Vous devez entrer la proportion du portail",
      "precent_of_payment_numeric" => "La proportion doit être sous forme de nombre",
      "precent_of_payment_between" => "la proportion doit être compris entre 0 et 100",
       "value_of_cent_required" => "Vous devez entrer la valeur d’un cent",
      "value_of_cent_numeric" => "La valeur d’un cent doit être sous la forme de nombre"
    ]


];
