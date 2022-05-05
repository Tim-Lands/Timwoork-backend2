<?php

return [

     /* ------------------------------ رسائل التحقق ------------------------------ */
    'username' => [
        'required' => 'username is required'
    ],
    'password' => [
        'required' => 'password required'
    ],
    "validation" => [
        "required_name_en" => "Arabic name is required",
        "required_precent_deducation" => "deduction percentage is required",
        "unique" => "This field already exists, check your data",
        "min_description" => "The number of characters must be at least 8",
        'string' => 'The field must be letters, numbers and symbols',
        'email' => 'the input value must be an email',
        'numeric' => 'The input value must be numbers',
        'exists' => 'This field does not match the existing data',
        'file' => 'This field must be a file',
        'zip' => 'must be on zip',
        'icon' => 'You must enter the icon',
        "number_developments" => "The number of developments field is required",
        'price_developments' => 'maximum development price required',
        'number_sales' => 'number_sales level required',
        'value_bayer' => 'The purchase value field is required',
        'type_level' => "The level type field is required",
        "parent_id" => "parent_id is required",
        "thumbnail" => "front image required",
        'thumbnail_image' => "png , jpg , jpeg The foreground image must be of type images",
        "thumbnail_mimes" => "png , jpg , jpeg The foreground image must be of type",
        "thumbnail_size" => "The size of the foreground image must not exceed 2MB",
        "images_mimes" =>"png , jpg , jpeg The display image must be of type",
        "images_size" => "The display image size must not exceed 2MB",
        "file_mimes" =>"pdf file must be of type",
        "file_size" => "The file must be no more than 2MB",
        "url" => "URL - This field must be a link",
        "title_required" => "The title of the service is required",
        "title_size" =>"The title should not be less than 20 characters and cannot be greater than 55 characters",
        "title_required" => "The title of the service is required",
        "subcategory_required" => "subcategory is required",
        "tags_required" => "tags are required",
        "tags_value_required" => "No tags should be empty",
        "content_required" => "content_required",
        "buyer_instruct_required" => "Service purchase instructions required",
        "price_required" => "price required",
        "price_two_after_number" => "price must have two digits after the comma only",

         "developements_title_required" => "Development title required",
        "developements_duration_required" => "development period required",
        "developements_duration_great_zero" => "development time is at least 1 day",
        "developements_price_required" => "development price required",
        "developements_price_great_zero" => "Development price must be greater than 0",
        "developements_price_two_after_number" => "The price must have two digits after the comma only",
        "rating_required" => "rating is required",
        "rating_between" => "rating should be between 0 to 1",
        "comment_required" => "comment is required",
        "product_id" => "service is required",
        "file_resource_required" => "Project is required",
        "file_resource_file" => "The project must be in the form of a file",
        "file_resource_required" => "Project is required",
        "file_resource_mimes" =>"zip file must be of type",
        "old_password_required" => "old password is required",
        "password_required" => "password is required",
        "password_confirmed" => "password does not match",
        "password_confirmation_required" => "a matching password is required",
        "conversation_title" => "Conversation title is required",
        "receiver_id" => "receiver for message is required",
        "initial_message_required" => "initial message is required",
        "email_required" => "email is required",
        "message_required" => "message is required",
        "bio_required" => "biography is required",
        "bio_min" => "The bio must contain at least 26 characters",
        "portfolio_required" => "your profile is required",
        "skills_required" => "skills required",
        "first_name_required" => "first name is required",
        "last_name_required" => "last name is required",
        "username_required" => "username is required",
        "date_of_birth_required" => "birthday is required",
        "gender_required" => "gender is required",
        "phone_number_required" => "phone number is required",
        "country_id" => "Country is required",

        "avatar_required" => "avatar photo is required",
        "languages" => "language is required",
        "provider_id" => "provider is required",
        "code_required" => "Confirmation number is required",
        "code_size" => "The confirmation number must contain a minimum of 6 digits and a maximum of 8",
        "professions" => "professional required",
        "skills" => "skills required",
        "price_between" => "price must be between $5 and $5000",
        "duration_required" => "You must enter the duration",
        "tags_min" => "The tag must contain at least 1 character",
        "password_min" => "The password must contain at least 8 letters and numbers",
        "name_en_required" => "The field must be entered in English",
        "content_min" => "content must be at least 30 characters",
        "subject_required" => "You must enter the subject of the message",
        "full_name_required" => "You must enter your full name",
        "type_message_required" =>"You must enter a message type",
        "message_required" => "You must enter the message text",
        "email_unique" => "email already exists",
    ],

    /* --------------------------------- الرسائل -------------------------------- */
    // العمليات
    "oprations" => [
        "get_data" => "The item was fetched successfully",
        "add_success" =>"The item was added successfully",
        "update_success" =>"The item has been modified successfully",
        "delete_success" =>"The item was deleted successfully",
        "restore_delete_success" =>"The item was restored successfully",
        "get_all_data" => "get items successfully",
        'nothing_this_operation' => 'You cannot perform this operation, you will lose your data'
    ],
    // وضع الموقع
    "mode" => [
        "active_dark_mode" => "The mode has been changed to daytime mode",
        "disactive_dark_mode" => "The mode has been changed to night mode",
    ],

    // المستخدم
    "user" => [
        "send_eamil_reset_password" => "A password recovery link has been sent to your email",
        "error_verify" => "Something went wrong...Your password recovery code was not found",
        "success_verify_reset_password" => "Your password recovery code is correct",
        "success_reset_password" => "The password has been reset successfully",
        "fieled_operation" => "the operation failed",
        "error_login" => "The information you entered is wrong",
        "email_already" => "This email already exists",
        "logout_all" => "all devices are logged out",
        "logout" => "You have been logged out",
    ],
    // البائع
    "seller" => [
        "active_product" => "The service has been activated successfully",
        "disactive_product" => "The service has been successfully disabled",
        "disactived_product" => "the service is disabled",
        "actived_product" => "the service is activated",
        "my_products" => "Your services have been found",
        "add_profile_seller" => "Seller profile has been successfully registered",
    ],
    // الخدمة
    "product" => [
        "number_of_products_seller" => "A service cannot be added above the maximum number specified for you",
        "success_step_one" => "first stage created successfully",
        "success_step_two" => "The second stage has been created successfully",
        "success_step_three" => "the third stage has been created successfully",
        "success_step_four" => "Phase 4 has been successfully created",
        "success_upload_thumbnail" => "image uploaded successfully",
        "success_upload_images" => "The display images have been uploaded successfully",
        "success_step_final" => "The stages have been completed and the service has been created successfully",
        "number_developments_max" => "A development cannot be added above the maximum number specified for you",
        "price_development_max" => "A price cannot be added above your maximum number",
        "thumbnail_required" => "You must upload the foreground image",
        "count_galaries" => "the number of images uploaded must be no more than 5 and not less than 1",
        "success_upload_galaries" => "The display images have been uploaded successfully",
        "upload_images" => "You must upload images"
    ],

    /* ------------------------------ رسائل الأخطاء ----------------------------- */
    "errors" => [
         "error_database" => "An error occurred in a database, please check it",
         "element_not_found" => "This element does not exist",
         "url_not_found" => "This link does not exist",
     ],
    /* ------------------------------- لوحة التحكم ------------------------------ */
    "dashboard" => [
        "statistic_dashboard" => "dashboard stats",
        // activate the service
        "active_status_product" => "The service has been activated successfully",
        "reject_status_product" => "rejected service",
        // the boss
        "get_login" => "Successfully logged in",
        "get_logout" => "Successfully logged out",
        "valid_login" => "The information you entered is wrong",
        "get_product_actived" => "The list of activated services was found",
        "get_product_rejected" => "The list of rejected services was found",
    ],
    // السلة
    "cart" => [
        "cartitem_found" => "This item is already in the basket, add another item",
        "product_not_buying" => "You cannot buy this service, you lose your data",
        "same_developments" =>"Developments that are not compatible with this service",
        "not_found_cartitem" => "No unsold cart, add a new cart please",
        "cart_not_found" => "there is no cart, please return the purchase",
        "catitem_not_found" => "There are no items in the basket, please return the purchase",
        "can_not_buying" => "You cannot buy this number of services, follow the instructions included on the purchase card",
        "profile_not_complete" => "Cannot purchase, you must activate your account"
    ],
    // المعاملة بين البائع و المشتري
    "item" => [
        "not_may_this_operation" => "You cannot perform this operation, you will lose your data",
        "not_found_item_reject" => "Order not found...Order canceled",
        "accept_item_by_seller" => "The order was accepted by the seller",
        "reject_item_by_seller" => "The order was rejected by the seller",
        "accept_item_by_buyer" => "the order was accepted by the buyer",
        "reject_item_by_buyer" => "the order was rejected by the buyer",
        "must_be_dilevery_resources" => "You must upload the project before delivery",
        "dilevery_resources_founded" => "The project has already been submitted, check your data",
        "delivery_resources_success" => "the project was delivered by the seller",
        "resource_uploaded" => "The file has already been uploaded, now you must submit",
        "resource_upload" => "The project has been uploaded and ready to be delivered to the buyer",
        "resource_not_found" => "project not found",
        "resource_rejected" => "Project status rejected, you lose your data",
        "resource_not_dilevery" => "the project was not delivered, you lose your data",
        "resource_dilevered" => "the project has been received by the buyer and the transaction is completed",
        "resource_accepted" => "Project status is accepted, you lose your data",
        "resource_not_dilevered" => "The project was rejected by the buyer and the order was rejected",
        "request_buyer_sended" => "A cancellation request has been sent by the buyer, accept or decline the request",
        "request_sended" => "A cancellation request has been sent, wait for it to be accepted or rejected",
        "request_seller_success" => "A cancellation request has been sent by the seller",
        "request_buyer_success" => "A cancellation request has been sent by the buyer",
        "request_seller_sended" => "A cancellation request has been sent by the seller, accept or reject the cancellation",
        "request_buyer_success" => "A cancellation request has been sent by the buyer",
        "request_not_found" => "No request was sent, you lose your data",
        "request_accepted_by_seller" => "Cancellation request was accepted by the seller and the service was refused",
        "request_accepted_by_buyer" => "A cancellation request was accepted by the buyer and the service was refused",
        "request_rejected_by_seller" => "The cancellation request has been rejected by the seller, and the administration will be contacted if the problem is not resolved within 24 hours",
        "request_rejected_by_buyer" => "The cancellation request was rejected by the buyer, the administration will be contacted if the problem is not resolved within 24 hours",

        "request_modified_by_buyer" => "A cancellation request has been sent by the buyer",
         "accepted_modified_by_seller" => "modification accepted by seller",
         "reject_modified_by_seller" => "The modification has been rejected by the seller, and the administration will be contacted if the problem is not resolved within 24 hours",
         "found_request_modified" => "modification request already exists",
         "found_request_rejected" => "cancellation request already exists",
         "resolve_the_conflict_between_them" => "the dispute between the parties has been resolved"
    ],
    // الفلتر
     "filter" => [
         "filter_success" => "filter completed successfully",
         "filter_field" => "No results found"
     ],
     "conversation" => [
         "conversation_success" => "Conversation added successfully",
         "message_success" =>"The message has been sent successfully",
     ],
     "contact" => [
        "not_found_url" => "The file must be uploaded to Google Drive or Dropbox",
        "success_message_contact" => "The message was sent successfully",
        "cannot_sent_before_48" => "You must wait after 48 hours to be able to send a new message",
        "failures_send_email" => "The email was not sent, please try again",
        "success_send_message_to_email" => "The message has been sent to the email successfully",
     ],
     "bank" =>[
        "city_required" => "You must enter the province",
        "state_required" => "You must enter a city",
        "address_line_one_required" => "You must enter the address",
        "code_postal_required" => "You must enter your postal code",
        "id_type_required" => "You must enter the type of attachment",
        "attachments_required" => "the attachment must be in the form of an image",
        "bank_name_required" => "You must enter the name of the bank",
        "bank_branch_required" => "You must enter the name of the branch of the bank",
        "bank_swift_required" => "You must enter a bank transfer (swift)",
        "bank_swift_required" => "You must enter a bank transfer (swift)",
        "bank_swift_required" => "You must enter a bank transfer (swift)",
        "bank_iban_required" => "You must enter IBAN Bank",
        "bank_number_account_required" => "You must enter the bank number",
        "bank_adress_line_one_required" => "You must enter the bank address"
     ],

     "type_payment" => [
        "active_payment" => "Portal has been activated successfully",
        "disactive_payment" => "Portal has been disabled successfully",
        "precent_of_payment_required" => "You must enter the gate percentage",
        "precent_of_payment_numeric" => "the percentage must be in the form of a number",
        "precent_of_payment_between" => "the ratio must be between 0 and 100",
        "value_of_cent_required" => "You must enter the value of a cent",
        "value_of_cent_numeric" => "the value of a cent must be in the form of a number"
    ]

];
