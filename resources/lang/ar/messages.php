<?php

return [

    /* ------------------------------ رسائل التحقق ------------------------------ */
    'username' => [
        'required' => 'اسم المستخدم مطلوب'
    ],
    'password' => [
        'required' => 'كلمة المرور مطلوبة'
    ],
    "validation" => [
        "required_name_ar" => "الاسم بالعربي مطلوب",
        "required_precent_deducation" => "نسبة الاقتطاع مطلوبة",
        "unique" => "هذا الحقل موجود من قبل, تفقد بياناتك",
        "min_description" => "يجب أن لا يقل عدد الحروف عن 8 أحرف",
        'string' => 'يجب أن يكون الحقل عبارة عن حروف و أرقام و رموز ',
        'email' => 'يجب أن تكون قيمة الإدخال أيميل',
        'numeric' => 'يجب أن تكون قيمة الإدخال أرقام',
        'exists' => 'هذا الحقل غير مطابق مع البيانات الموجودة',
        'file' => 'هذا الحقل يجب أن يكون ملف',
        'zip' => 'يجب أن يكون على امتداد zip',
        'icon' => 'يجب عليك إدخال الإيقونة',
        "number_developments" => "حقل عدد التطويرات مطلوب",
        'price_developments' => 'أقصى سعر للتطوير مطلوب',
        'number_sales' => 'مستوى عدد المبيعات مطلوب',
        'value_bayer' => 'حقل القيمة الشرائية مطلوب',
        'type_level' => "حقل نوع المستوى مطلوب",
        "parent_id" => "التصنيف الرئيسي مطلوب",
        "thumbnail" => "الصورة الأمامية مطلوبة",
        'thumbnail_image' => " png , jpg , jpeg  يجب أن تكون الصورة الأمامية على شكل صور من نوع ",
        "thumbnail_mimes" => "png , jpg , jpeg يجب أن تكون الصورة الأمامية من نوع",
        "thumbnail_size" => "2MB يجب أن يكون حجم صورة الأمامية لا يتجاوز",
        "images_mimes" =>"png , jpg , jpeg يجب أن تكون صورة العرض من نوع",
        "images_size" => "2MB  يجب أن تكون حجم صورة العرض لا تتجاوز",
        "file_mimes" =>"pdf يجب أن يكون الملف من نوع",
        "file_size" => "2MB  يجب أن يكون الملف لا يتجاوز",
        "url" => "URL - يجب أن يكون هذا الحقل على شكل رابط",
        "title_required" => "عنوان الخدمة مطلوب",
        "title_size" => "العنوان لا يقل عن 20 حرف و لا يكون اكبر من 55 حرف",
        "title_required" => "عنوان الخدمة مطلوب",
        "subcategory_required" => "تصنيف الفرعي مطلوب",
        "tags_required" => "الوسوم مطلوبة",
        "content_required" => "محتوى الخدمة مطلوب",
        "buyer_instruct_required" => "تعليمات شراء الخدمة مطلوبة",
        "price_required" => "سعر الخدمة مطلوب",
        "price_two_after_number" => "يجب أن يكون السعر لديه رقمين بعد الفاصلة فقط",

        "developements_title_required" => "عنوان التطوير مطلوب",
        "developements_duration_required" => "مدة التطوير مطلوب",
        "developements_duration_great_zero" => "مدة التطوير على الأقل تكون يوم واحد",
        "developements_price_required" => "سعر التطوير مطلوب",
        "developements_price_great_zero" => "سعر التطوير يجب أن يكون اكبر من 0",
        "developements_price_two_after_number" => "يجب أن يكون السعر لديه رقمين بعد الفاصلة فقط",
        "rating_required" => "التقييم مطلوب",
        "rating_between" => "يجب أن يكون التقييم بين من 0 إلى 1",
        "comment_required" => "التعليق مطلوب",
        "product_id" => "الخدمة مطلوبة",
        "file_resource_required" => "المشروع مطلوب",
        "file_resource_file" => "يجب أن يكون المشروع على شكل ملف",
        "file_resource_required" => "المشروع مطلوب",
        "file_resource_mimes" =>"zip يجب أن يكون الملف من نوع",
        "old_password_required" => "كلمة السر القديمة مطلوبة",
        "password_required" => "كلمة السر مطلوبة",
        "password_confirmed" => "كلمة السر غير مطابقة",
        "password_confirmation_required" => "كلمة السر المطابقة مطلوبة",
        "conversation_title" => "عنوان المحادثة مطلوب",
        "receiver_id" => "مستقبل الرسالة مطلوب",
        "initial_message_required" => "الرسالة المبدئية مطلوبة",
        "email_required" => "الايميل مطلوب",
        "message_required" => "الرسالة مطلوبة",
        "bio_required" => "النبذة مطلوبة",
        "bio_min" => "يجب أن تكون النبذة تحتوي على الأقل 26 حرف",
        "portfolio_required" => "النبذه عنك مطلوب",
        "skills_required" => "المهارات مطلوبة",
        "first_name_required" => "الاسم الاول مطلوب",
        "first_name_min" => "الاسم الاول قصير جدا",
        "last_name_required" => "الاسم الاخير مطلوب",
        "last_name_min" => "الاسم الاخير قصير جدا",
        "username_required" => "اسم المستخدم مطلوب",
        "date_of_birth_required" => "تاريخ الميلاد مطلوب",
        "gender_required" => "الجنس مطلوب",
        "phone_number_required" => "رقم الهاتف مطلوب",
        "country_id" => "الدولة مطلوبة",
        "avatar_required" => "صورة الشخصية مطلوبة",
        "languages" => "اللغة مطلوبة",
        "provider_id" => "المزود مطلوب",
        "code_required" => "رقم التأكيد مطلوب",
        "code_size" => "رقم التأكيد يجب أن يحتوي على اقل 6 أرقام و 8 على الأكثر",
        "professions" => "المهنة مطلوبة",
        "skills" => "المهارات مطلوبة",
        "price_between" => "يجب أن يكون السعر بين 5 دولار و 5000 دولار",
        "duration_required" => "يجب عليك إدخال المدة",
        "tags_min" => "يجب أن يحتوي الوسم على الأقل 1 حرف",
        "password_min" => "يجب أن تحتوي كلمة السر على الأقل 8 حروف و أرقام",
        "name_en_required" => "يجب إدخال الحقل باللغة الانجليزية",
        "content_min" => "يجب أن لا يقل المحتوى عن 30 حرف",
        "subject_required" => "يجب عليك ادخال موضوع الرسالة",
        "full_name_required" => "يجب عليك ادخال الاسم الكامل",
        "type_message_required" =>"يجب عليك ادخال نوع الرسالة",
        "message_required" => "يجب عليك ادخال النص الرسالة"
    ],

    /* --------------------------------- الرسائل -------------------------------- */
    // العمليات
    "oprations" => [
        "get_data" => "تم جلب العنصر بنجاح",
        "add_success" =>"تم إضافة العنصر بنجاح",
        "update_success"    =>"تم تعديل على العنصر بنجاح",
        "delete_success" =>"تم حذف العنصر بنجاح",
        "get_all_data" => "جلب العناصر بنجاح",
        'nothing_this_operation' => 'لا يمكنك إجراء هذه العملية , تفقد بياناتك'
    ],
    // وضع الموقع
    "mode" => [
        "active_dark_mode" => "لقد تم تغيير الوضع إلى وضع نهاري",
        "disactive_dark_mode" => "لقد تم تغيير الوضع إلى وضع ليلي",
    ],

    // المستخدم
    "user" => [
        "send_eamil_reset_password" => "لقد تم إرسال رابط استعادة كلمة المرور إلى بريدك الالكتروني",
        "error_verify" => "حدث خطأ ما... لم يتم العثور على رمز استعادة كلمة المرور الخاص بك",
        "success_verify_reset_password" => "رمز استعادة كلمة المرور الخاص بك صحيح",
        "success_reset_password" => "لقد تم إعادة تعيين كلمة المرور بنجاح",
        "fieled_operation" => "فشلت العملية",
        "error_login" => "المعلومات التي أدخلتها خاطئة",
        "email_already" => "هذا البريد الالكتروني موجود من قبل",
        "logout" => "لقد تمّ تسجيل خروجك بنجاح",
    ],
    // البائع
    "seller" => [
        "active_product" => "تم تنشيط الخدمة بنجاح",
        "disactive_product" => "تم تعطيل الخدمة بنجاح",
        "disactived_product" => "الخدمة معطلة",
        "actived_product" => "الخدمة منشطة",
        "my_products" => "لقد تم العثور على خدماتك",
        "add_profile_seller" => "تم تسجيل بروفايل البائع بنجاح",
    ],
    // الخدمة
    "product" => [
        "number_of_products_seller" => "لا يمكن إضافة خدمة فوق العدد الأقصى المحدد لك",
        "success_step_one" => "تم إنشاء المرحلة الأولى بنجاح",
        "success_step_two" => "تم إنشاء المرحلة الثانية بنجاح",
        "success_step_three" => "تم إنشاء المرحلة الثالثة بنجاح",
        "success_step_four" => "تم إنشاء المرحلة الرابعة بنجاح",
        "success_upload_thumbnail" => "تم رفع الصورة بنجاح",
        "success_upload_images" => "تم رفع صور العرض بنجاح",
        "success_step_final" => "تم إنهاء المراحل و إنشاء الخدمة بنجاح",
        "number_developments_max" => "لا يمكن إضافة تطوير فوق العدد الأقصى المحدد لك",
        "price_development_max" => "لا يمكن إضافة سعر فوق العدد الأقصى المحدد لك",
        "thumbnail_required" => "يجب عليك رفع الصورة الأمامية",
        "count_galaries" => "يجب أن يكون عدد الصور المرفوعة لا تزيد عن  5 و لا تقل عن 1",
        "success_upload_galaries" => "تم رفع صور العرض بنجاح",
        "upload_images" => "يجب عليك رفع الصور",
    ],

    /* ------------------------------ رسائل الأخطاء ----------------------------- */
    "errors" => [
        "error_database" => "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك",
        "element_not_found" => "هذا العنصر غير موجود",
        "url_not_found" => "هذا الرابط غير موجود",
    ],
    /* ------------------------------- لوحة التحكم ------------------------------ */
    "dashboard" => [
        "statistic_dashboard" => "إحصائيات لوحة التحكم",
        // تنشيط الخدمة
        "active_status_product" => "تم تنشيط الخدمة بنجاح",
        "reject_status_product" => "تم رفض الخدمة ",
        // المدير
        "get_login" => "تم تسجيل الدخول بنجاح",
        "get_logout" => "تم تسجيل الخروج بنجاح",
        "valid_login" => "المعلومات التي أدخلتها خاطئة",
        "get_product_actived" => "تم العثور على قائمة الخدمات التي تم تنشيطها",
        "get_product_rejected" => "تم العثور على قائمة الخدمات التي تم رفضها",
        "failures_send_email" => "لم يتم الارسال الى الايميل, اعد المحاولة",
        "success_send_message_to_email" => "تم ارسال الرسالة الى الايميل بنجاح",
    ],
    // السلة
    "cart" => [
        "cartitem_found" => "هذا العنصر موجود فالسلة , أضف عنصر آخر",
        "product_not_buying" => "لا يمكنك شراء هذه الخدمة, تفقد بياناتك",
        "same_developments" =>"التطويرات التي تم إدخالها ليست مطابقة مع هذه الخدمة",
        "not_found_cartitem" => "لا توجد سلة غير مباعة , أضف سلة جديدة من فضلك",
        "cart_not_found" => "لا توجد سلة , الرجاء إعادة عملية الشراء",
        "catitem_not_found" => "لا توجد عناصر داخل السلة , الرجاء إعادة عملية الشراء"
    ],
    // المعاملة بين البائع و المشتري
    "item" => [
        "not_may_this_operation" => "لا يمكنك إجراء هذه العملية, تفقد بياناتك",
        "not_found_item_reject" => "الطلب غير موجود ... إلغاء الطلبية",
        "accept_item_by_seller" => "تم قبول الطلب من قبل البائع",
        "reject_item_by_seller" => "تم رفض الطلب من قبل البائع",
        "accept_item_by_buyer" => "تم قبول الطلب من قبل المشتري",
        "reject_item_by_buyer" => "تم رفض الطلب من قبل المشتري",
        "must_be_dilevery_resources" => "يجب عليك رفع المشروع قبل التسليم",
        "dilevery_resources_founded" => "لقد تم تسليم المشروع مسبقا, تفقد بياناتك",
        "dilevery_resources_success" => "تم رفع المشروع و تسليمه من قبل البائع",
        "resource_not_found" => "المشروع غير موجود",
        "resource_rejected" => "حالة المشروع مرفوضة , تفقد بياناتك",
        "resource_not_dilevery" => "لم يتم تسليم المشروع , تفقد بياناتك",
        "resource_dilevered"     => "تم استلام المشروع من قبل المشتري",
        "resource_not_dilevered" => "تم رفض المشروع من قبل المشتري",
        "request_seller_sended" => "لقد تم إرسال طلب إلغاء من قبل البائع, قم بقبول إلغاء الطلب أو ارفضه",
        "request_sended" => "لقد تم إرسال طلب إلغاء, انتظر حتى يتم القبول أو الرفض",
        "request_buyer_success" => "تم إرسال طلب إلغاء من قبل المشتري",
        "request_not_found" => "لم يتم إرسال طلب, تفقد بياناتك",
        "request_accepted_by_seller" => "تم قبول طلب إلغاء من قبل البائع و تم رفض الخدمة",
        "request_rejected_by_seller" => "تم رفض طلب الإلغاء من قبل البائع ,  وسيتم مراسلة الإدارة في حالة لم يتم حل المشكلة خلال 24 ساعة",
        "request_rejected_by_buyer" => "تم رفض طلب الإلغاء من قبل المشتري ,  سيتم مراسلة الإدارة في حالة لم يتم حل المشكلة خلال 24 ساعة",
        "is_dilevered" => "تم التسليم بنجاح",
        "attachment_size" => "70MB يجب ان يكون الملف اقل من",

        "request_modified_by_buyer" => "تم إرسال طلب إلغاء من قبل المشتري",
        "accepted_modified_by_seller" => "تم قبول التعديل من قبل البائع",
        "reject_modified_by_seller" => "تم رفض التعديل من قبل البائع ,  وسيتم مراسلة الإدارة في حالة لم يتم حل المشكلة خلال 24 ساعة",
        "found_request_modified" => "طلب التعديل موجود من قبل",
        "found_request_rejected" => "طلب الالغاء موجود من قبل",
        "resolve_the_conflict_between_them" => "تم حل النزاع بين الطرفين"
    ],
    // الفلترة
    "filter" => [
        "filter_success" => "تمت عملية الفلترة بنجاح",
        "filter_field" => "لم يتم العثور على نتائج"
    ],
    "conversation" => [
        "conversation_success" => "لقد تمّ إضافة المحادثة بنجاح",
        "message_success" => "لقد تمّ إرسال الرسالة بنجاح",
    ],
    "contact" => [
        "not_found_url" => "يجب ان يكون الملف مرفوع في غوغل درايف او دروب بوكس",
        "success_message_contact" => "تم ارسال الرسالة بنجاح",
        "cannot_sent_before_48" => "يجب عليك انتظار بعد 48 ساعة كي تستطيع ارسال رسالة جديدة",
        "failures_send_email" => "لم يتم الارسال الى الايميل, اعد المحاولة",
        "success_send_message_to_email" => "تم ارسال الرسالة الى الايميل بنجاح",
    ]

];
