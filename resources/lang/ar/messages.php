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
        "required_name_ar" => "اسم بالعربي مطلبوب",
        "required_precent_deducation" => "نسبة الاقتطاع مطلوبة",
        "unique" => "هذا الحقل موجود من قبل, تفقد بيانتك",
        "min_description" => "يجب ان لا يقل عدد الحروف عن 8",
        'string' => '.يجب أن يكون الحقل عبارة سلسلة',
        'email' => 'يجب ان يكون على صيغة ايميل',
        'numeric' => 'يجب ان يكون رقم',
        'exists' => 'هذا الحقل غير مطابق مع البيانات الموجودة',
        'file' => 'هذا الحقل يجب ان يكون ملف',
        'zip' => 'يجب ان يكون على امتداد zip',
        'icon' => 'يجب عليك ادخال الايقونة',
        "number_developments" => "حقل عدد التطويرات مطلوب",
        'price_developments' => 'أقصى سعر للتطوير مطلوب',
        'number_sales' => 'عدد المبيعات المستوى مطلوب',
        'value_bayer' => 'حقل القيمة الشرائية مطلوب',
        'type_level' => "حقل نوع المستوى مطلوب",
        "parent_id" => "التصنيف الرئيسي مطلوب",
        "thumbnail" => "الصورة الامامية مطلوبة",
        "thumbnail_mimes" => "png , jpg , jpeg يجب ان تكون الصورة الامامية من نوع",
        "thumbnail_size" => "2mb  يجب ان يكون حجم صورة الامامية اقل او يساوي",
        "images_mimes" =>"png , jpg , jpeg يجب ان تكون الصورة العرض من نوع",
        "images_size" => "2mb  يجب ان تكون حجم صورة العرض اقل او يساوي",
        "file_mimes" =>"pdf يجب ان الملف من نوع",
        "file_size" => "2mb  يجب ان يكون الملف اقل او يساوي",
        "url" => "يجب ان يكون هذا الحقل على شكل رابط",
        "title_required" => "عنوان الخدمة مطلوب",
        "subcategory_required" => "تصنيف الفرعي مطلوب",
        "tags_required" => "الوسوم مطلوبة",
        "content_required" => "محتوى الخدمة مطلوب",
        "buyer_instruct_required" => "تعليمات شراء الخدمة مطلوبة",
        "price_required" => "سعر الخدمة مطلوب",
        "developements_title_required" => "عنوان التطوير مطلوب",
        "developements_duration_required" => "مدة التطوير مطلوب",
        "developements_price_required" => "سعر التطوير مطلوب",
        "rating_required" => "التقيم مطلوب",
        "rating_between" => "يجب ان يكون التقيم بين من 0 الى 1",
        "comment_required" => "التعليق مطلوب",
        "product_id" => "الخدمة مطلوبة",
        "file_resource_required" => "المشروع مطلووب",
        "file_resource_file" => "يجب ان يكون المشروع على شكل ملف",
        "file_resource_required" => "المشروع مطلووب",
        "file_resource_mimes" =>"zip يجب ان الملف من نوع",
        "old_password_required" => "كلمة السر القديمة مطلوبة",
        "password_required" => "كلمة السر مطلوبة",
        "password_confirmed" => "كلمة السر غير مطابقة",
        "password_confirmation_required" => "كلمة السر المطاقبة مطلوبة",
        "conversation_title" => "عنوان المحادثة مطلوب",
        "receiver_id" => "مستقبل الرسالة مطلوب",
        "initial_message_required" => "الرسالة المبدئية مطلوبة",
        "email_required" => "الايميل مطلوب",
        "message_required" => "الرسالة مطلوبة",
        "bio_required" => "النبذة مطلوبة",
        "bio_min" => "يجب ان تكون النبذة تحتوي على الاقل 26 حرف",
        "portfolio_required" => "البورتفوليو مطلوب",
        "skills_required" => "المهارات مطلوبة",
        "first_name_required" => "اسم الاول مطلوب",
        "last_name_required" => "اسم الاخير مطلوب",
        "username_required" => "اسم المستخدم مطلوب",
        "date_of_birth_required" => "تاريخ الميلاد مطلوب",
        "gender_required" => "الجنس مطلوب",
        "phone_number_required" => "رقم الهاتف مطلوب",
        "country_id" => "الدولة مطلوبة",
        "avatar_required" => "صورة الشخصية مطلوبة",
        "languages" => "اللغة مطلوبة",
        "provider_id" => "المزود مطلوب",
        "code_required" => "رقم التأكيد مطلوب",
        "code_size" => "رقم التأكيد يجب ان يحتوي على اقل 6 ارقام و 8 على الاكثر",
        "professions" => "المهنة مطلوبة",
        "skills" => "المهارات مطلوبة",
        "price_between" => "يجب ان يكون السعر بين 5 دولار و 5000 دولار",
        "duration_required" => "يجب عليك ادخال المدة",
        "tags_min" => "يجب ان يحتوي الوسم على الاقل 3 حروف",
        "password_min" => "يجب ان تحتوي كلمة السر على الاقل 8 حروف و ارقام",
        "name_en_required" => "يجب ادخال الحقل بالانجليزي",
    ],
    
    /* --------------------------------- الرسائل -------------------------------- */
    // العمليات
    "oprations" => [
        "get_data" => "تم جلب العنصر بنجاح",
        "add_success" =>"تم اضافة العنصر بنجاح",
        "update_success"    =>"تم تعديل على العنصر بنجاح",
        "delete_success" =>"تم حذف العنصر بنجاح",
        "get_all_data" => "جلب العناصر بنجاح",
    ],
    // وضع الموقع
    "mode" => [
        "active_dark_mode" => "لقد تم تغيير الوضع إلى وضع نهاري",
        "disactive_dark_mode" => "لقد تم تغيير الوضع إلى وضع ليلي",
    ],

    // المستخدم
    "user" => [
        "send_eamil_reset_password" => "لقد تم ارسال رابط استعادة كلمة المرور إلى بريدك الالكتروني",
        "error_verify" => "حدث خطأ ما لم يتم العثور على رمز استعادة كلمة المرور الخاص بك",
        "success_verify_reset_password" => "رمز استعادة كلمة المرور الخاص بك صحيح",
        "success_reset_password" => "لقد تم إعادة تعيين كلمة المرور بنجاح",
        "fieled_operation" => "فشلت العملية",
        "error_login" => "المعلومات التي أدخلتها خاطئة",
        "email_already" => "هذا البريد الالكتروني موجود من قبل",
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
        "number_of_products_seller" => "لا يمكن اضافة خدمة فوق عدد الاقصى المحدد لك",
        "success_step_one" => "تم انشاء المرحلة الاولى بنجاح",
        "success_step_two" => "تم انشاء المرحلة الثانية بنجاح",
        "success_step_three" => "تم انشاء المرحلة الثالثة بنجاح",
        "success_step_four" => "تم انشاء المرحلة الرابعة بنجاح",
        "success_step_final" => "تم انهاء المراحل و انشاء الخدمة بنجاح",
        "number_developments_max" => "لا يمكن اضافة تطوير فوق عدد الاقصى المحدد لك",
        "price_development_max" => "لا يمكن اضافة سعر فوق عدد الاقصى المحدد لك",
        "thumbnail_required" => "يجب عليك رفع الصورة الامامية",
        "count_galaries" => "يجب ان يكون عدد الصور المرفوعة لا تزيد عن  5 و لا تقل عن 1",
    ],
    
    /* ------------------------------ رسائل الاخطاء ----------------------------- */
    "errors" => [
        "error_database" => "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك",
        "element_not_found" => "لا يوجد هذا العنصر",
        "url_not_found" => "هذا الرابط غير موجود",
    ],
    /* ------------------------------- لوحة التحكم ------------------------------ */
    "dashboard" => [
        "statistic_dashboard" => "احصائيات لوحة التحكم",
        // تنشيط الخدمة
        "active_status_product" => "تم تنشيط الخدمة بنجاح",
        "reject_status_product" => "تم رفض الخدمة بنجاح",
        // المدير
        "get_login" => "تم تسجيل الدخول بنجاح",
        "get_logout" => "تم تسجيل الخروج بنجاح",
        "valid_login" => "المعلومات التي أدخلتها خاطئة",
        "get_product_actived" => "تم العثور على قائمة الخدمات التي تم تنشيطها",
        "get_product_rejected" => "تم العثور على قائمة الخدمات التي تم رفضها",
    ],
    // السلة
    "cart" => [
        "cartitem_found" => "هذا العنصر موجود فالسلة , اضف عنصر آخر",
        "product_not_buying" => "لا يمكنك شراء هذه الخدمة, تفقد بياناتك",
        "same_developments" =>"التطويرات التي تم ادخالها ليست مطابقة مع هذه الخدمة",
        "not_found_cartitem" => "لا توجد سلة غير مباعة , اضف سلة جديدة من فضلك",
        "cart_not_found" => "لا توجد سلة , الرجاء اعادة عملية الشراء",
        "catitem_not_found" => "لا توجد عناصر فالسلة , الرجاء اعادة عملية الشراء"
    ],
    // المعاملة بين البائع و المشتري
    "item" => [
        "not_may_this_operation" => "لا يمكنك اجراء هذه العملية, تفقد بياناتك",
        "not_found_item_reject" => "لا يوجد الطلب الغاء الطلبية",
        "accept_item_by_seller" => "تم قبول الطلب من قبل البائع",
        "reject_item_by_seller" => "تم رفض الطلب من قبل البائع",
        "accept_item_by_buyer" => "تم قبول الطلب من قبل المشتري",
        "reject_item_by_buyer" => "تم رفض الطلب من قبل المشتري",
        "must_be_dilevery_resources" => "يجب عليك رفع المشروع قبل التسليم",
        "dilevery_resources_founded" => "لقد تم تسليم المشروع مسبقا, تفقد بياناتك",
        "dilevery_resources_success" => "تم تسليم المشروع من قبل البائع",
        "resource_uploaded" => "لقد تم رفع الملف من قبل , الان يجب عليك تسليم",
        "resource_upload" => "تم رفع المشروع و تهيئة تسليمه للمشتري",
        "resource_not_found" => "المشروع لا يوجد",
        "resource_rejected" => "حالة مشروع مرفوضة , تفقد بياناتك",
        "resource_not_dilevery" => "لم يتم تسليم المشروع , تفقد بياناتك",
        "resource_dilevered"     => "تم استلام المشروع من قبل المشتري و انهاء المعاملة",
        "resource_accepted"       => "حالة مشروع مقبول , تفقد بياناتك",
        "resource_not_dilevered" => "تم رفض المشروع من قبل المشتري و رفض الطلبية",
        "request_buyer_sended" => "لقد تم ارسال طلب الغاء من قبل المشتري, قم بقبول الغاء الطلب او ارفضه",
        "request_sended" => "لقد تم ارسال طلب الغاء, انتظر حتى يتم القبول او الرفض",
        "request_seller_success" => "تم ارسال طلب الغاء من قبل البائع",
        "request_buyer_success" => "تم ارسال طلب الغاء من قبل المشتري",
        "request_seller_sended" => "لقد تم ارسال طلب الغاء من قبل البائع, قم بقبول الغاء الطلب او ارفضه",
        "request_buyer_success" => "تم ارسال طلب الغاء من قبل المشتري",
        "request_not_found" => "لم يتم ارسال طلب, تفقد بياناتك",
        "request_accepted_by_seller" => "تم قبول طلب الغاء من قبل البائع و تم رفض الخدمة",
        "request_accepted_by_buyer" => "تم قبول طلب الغاء من قبل المشتري و تم رفض الخدمة",
        "request_rejected_by_seller" => "تم رفض طلب الغاء من قبل البائع , سيتم مراسلة الادارة",
        "request_rejected_by_buyer" => "تم رفض طلب الغاء من قبل المشتري , سيتم مراسلة الادارة",
    ],
    // الفلترة
    "filter" => [
        "filter_success" => "تمت عملية الفلترة بنجاح",
        "filter_field" => "لم يتم العثور على نتائج"
    ],
    "conversation" => [
        "conversation_success" => "لقد تمّ إضافة المحادثة بنجاح",
        "message_success" =>"لقد تمّ إرسال الرسالة بنجاح",
    ]

];
