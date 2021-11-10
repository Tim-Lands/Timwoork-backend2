## Timwoork

Write instructions here

## How to use Endpoints:

-   endpoint url
-   endpoint method type
-   endpoint parameters

### DashBoard endpoints

1- Auth:

-   Admin login endpoint:

    -   url : `http:localhost:8000/dashboard/login`
    -   method: post
    -   parameters:
        -   email: must be email
        -   password: 6 least characters.
    -   response:
        -   success : json data with token store in cookie
        -   error: json error message

-   Admin details endpoint:

    -   url : `http:localhost:8000/dashboard/me`
    -   method: get
    -   response:
        -   success : json data with token store in cookie
        -   error: 401 unauthentecated

-   Admin logout endpoint:
    -   url : `http:localhost:8000/dashboard/logout`
    -   method: post
    -   response:
        -   success : json data with remove cookie & token
        -   error: 401 unauthentecated

2- Levels:

-   Show Levels

    -   url : `http:localhost:8000/dashboard/levels`
    -   method: get
    -   parameters:
        -   type (get query ?type=): 0 buyer levels , 1 seller levels, null all levels
    -   response:
        -   success : true,
        -   msg: "لقد تمّ جلب المستويات بنجاح"
        -   data: levels

-   Show Level Details

    -   url : `http:localhost:8000/dashboard/levels/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "لقد تمّ جلب المستوى بنجاح"
        -   data: level

-   Add New Level

    -   url : `http:localhost:8000/dashboard/levels/store`
    -   method: post
    -   parameters:
        -   name_ar: string|required|unique
        -   name_en: string
        -   name_fr: string
        -   type: 0 for buyer level, 1 for seller level
        -   number_developments: integer
        -   price_developments: float
        -   number_sales: integer
        -   value_bayer: float
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة المستوى بنجاح"
        -   data: level
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"

-   Update Current Level

    -   url : `http:localhost:8000/dashboard/levels/{id}/update`
    -   method: post
    -   parameters:
        -   name_ar: string|required|unique
        -   name_en: string
        -   name_fr: string
        -   type: 0 for buyer level, 1 for seller level
        -   number_developments: integer
        -   price_developments: float
        -   number_sales: integer
        -   value_bayer: float
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على المستوى بنجاح"
        -   data: level
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"

-   Delete Current Level

    -   url : `http:localhost:8000/dashboard/levels/{id}/delete`
    -   method: post
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ حذف المستوى بنجاح"
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"

3- Badges:

-   Show Badges

    -   url : `http:localhost:8000/dashboard/badges`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "لقد تمّ جلب الشارات بنجاح"
        -   data: badges

-   Show Badge Details

    -   url : `http:localhost:8000/dashboard/badges/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "لقد تمّ جلب الشارة بنجاح"
        -   data: badge

-   Add New Badge

    -   url : `http:localhost:8000/dashboard/badges/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string
        -   name_fr: string
        -   precent_deducation: نسبة العمولة أقل من 100%

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة الشارة بنجاح"
        -   data: badge
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Badge

    -   url : `http:localhost:8000/dashboard/badges/{id}/update`
    -   method: post
    -   parameters:
        -   name_ar: string|required|unique
        -   name_en: string
        -   name_fr: string
        -   precent_deducation: نسبة العمولة أقل من 100%
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على الشارة بنجاح"
        -   data: badge
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"

-   Delete Current Badge

        -   url : `http:localhost:8000/dashboard/badges/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   msg: "لقد تمّ حذف الشارة بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

4 - Categories :

-   Show Catogories

    -   url : `http:localhost:8000/dashboard/categories`
    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل تصنيفات الرئيسية و الفرعية'
        -   data: categories

-   Show Category Details

    -   url : `http:localhost:8000/dashboard/categories/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: category

-   Add New Category

    -   url : `http:localhost:8000/dashboard/categories/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique
        -   description_ar: string|nullable
        -   description_en: string|nullable
        -   description_fr: string|nullable
        -   icon: rerquired

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة التصنيف بنجاح"
        -   data: category
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Category

    -   url : `http:localhost:8000/dashboard/categories/{id}/update`
    -   method: post
    -   parameters:
        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique
        -   description_ar: string|nullable
        -   description_en: string|nullable
        -   description_fr: string|nullable
        -   icon: rerquired
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على التصنيف بنجاح"
        -   data: category
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current Category

        -   url : `http:localhost:8000/dashboard/categories/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   data: category
            -   msg: "لقد تمّ حذف التصنيف بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

5 - SubCategories :

-   Show Catogories

    -   url : `http:localhost:8000/dashboard/categories/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل تصنيفات الرئيسية'
        -   data: categories

-   Show SubCategory Details

    -   url : `http:localhost:8000/dashboard/subcategories/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: subcategory

-   Add New Category

    -   url : `http:localhost:8000/dashboard/subcategories/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique
        -   description_ar: string|nullable
        -   description_en: string|nullable
        -   description_fr: string|nullable
        -   icon: rerquired
        -   parent_id: rerquired

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة التصنيف الفرعي بنجاح"
        -   data: subcategory
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current SubCategory

    -   url : `http:localhost:8000/dashboard/subcategories/{id}/update`
    -   method: post
    -   parameters:
        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique
        -   description_ar: string|nullable
        -   description_en: string|nullable
        -   description_fr: string|nullable
        -   icon: rerquired
        -   parent_id
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على التصنيف الفرعي بنجاح"
        -   data: subcategory
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current SubCategory

        -   url : `http:localhost:8000/dashboard/subcategories/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   msg: "لقد تمّ حذف التصنيف الفرعي بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

6 - Tags :

-   Show tags - url : `http:localhost:8000/dashboard/tags`

    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل الوسوم'
        -   data: tags

-   Show Tag Details

    -   url : `http:localhost:8000/dashboard/tags/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: tag

-   Add New Tag

    -   url : `http:localhost:8000/dashboard/tags/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|nullable|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة وسم بنجاح"
        -   data: tag
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Tag

    -   url : `http:localhost:8000/dashboard/tags/{id}/update`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على الوسم بنجاح"
        -   data: tag
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current Tag

    -   url : `http:localhost:8000/dashboard/tags/{id}/delete`
    -   method: post
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ حذف الوسم بنجاح"
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

7 - Product :

-   Show all products

    -   url : `http:localhost:8000/dashboard/products`
    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل الخدمات'
        -   data: products

-   Show product Actived :

    -   url : `http:localhost:8000/dashboard/products/active/status`
    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل الخدمات التي تم تنشيطها'
        -   data: products_actived

-   Show product Rejected :

    -   url : `http:localhost:8000/dashboard/products/reject/status`
    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل الخدمات التي تم رفضها'
        -   data: products_rejected

-   Show products Details

    -   url : `http:localhost:8000/dashboard/products/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: product

-   Active product

    -   url : `http:localhost:8000/dashboard/products/{id}/activeProduct`
    -   method: post
    -   response:
        -   success : true,
        -   msg: "تم تنشيط الخدمة بنجاح"
        -   data: product

-   Reject product

        -   url : `http:localhost:8000/dashboard/products/{id}/rejectProduct`
        -   method: post
        -   response:
            -   success : true,
            -   msg: "تم رفض الخدمة بنجاح"
            -   data: product

8 - Countries :

-   Show countries - url : `http:localhost:8000/dashboard/countries`

    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل الدول'
        -   data: countries

-   Show Country Details

    -   url : `http:localhost:8000/dashboard/countries/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: country

-   Add New Country

    -   url : `http:localhost:8000/dashboard/countries/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|nullable|unique
        -   name_fr: string|nullable|unique
        -   code_phone: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة دولة بنجاح"
        -   data: country
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Country

    -   url : `http:localhost:8000/dashboard/countries/{id}/update`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique
        -   code_phone: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على الدولة بنجاح"
        -   data: country
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current Country

        -   url : `http:localhost:8000/dashboard/countries/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   msg: "لقد تمّ حذف الدولة بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

9 - Statistic Dashboard :

-   Show tags - url : `http:localhost:8000/dashboard`

        -   method: get
        -   response:
            -   success : true,
            -   msg: 'احصائيات لوحة التحكم'
            -   data: data

10 - Skills :

-   Show skills - url : `http:localhost:8000/dashboard/skills`

    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل المهارات'
        -   data: skills

-   Show Skill Details

    -   url : `http:localhost:8000/dashboard/skills/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: skill

-   Add New Skill

    -   url : `http:localhost:8000/dashboard/skills/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|nullable|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة مهارة بنجاح"
        -   data: skill
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Skill

    -   url : `http:localhost:8000/dashboard/skills/{id}/update`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على الوسم بنجاح"
        -   data: skill
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current Skill

        -   url : `http:localhost:8000/dashboard/skills/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   msg: "لقد تمّ حذف الوسم بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

11 - Languages :

-   Show languages - url : `http:localhost:8000/dashboard/languages`

    -   method: get
    -   response:
        -   success : true,
        -   msg: 'عرض كل اللغات'
        -   data: languages

-   Show Languages Details

    -   url : `http:localhost:8000/dashboard/languages/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: language

-   Add New Language

    -   url : `http:localhost:8000/dashboard/languages/store`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|nullable|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ إضافة اللغة بنجاح"
        -   data: language
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Update Current Language

    -   url : `http:localhost:8000/dashboard/languages/{id}/update`
    -   method: post
    -   parameters:

        -   name_ar: string|required|unique
        -   name_en: string|required|unique
        -   name_fr: string|nullable|unique

    -   success response:
        -   success : true,
        -   msg: "لقد تمّ التعديل على اللغة بنجاح"
        -   data: language
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

-   Delete Current Language

        -   url : `http:localhost:8000/dashboard/languages/{id}/delete`
        -   method: post
        -   success response:
            -   success : true,
            -   msg: "لقد تمّ حذف اللغة بنجاح"
        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

12 - Insert product :

    -   fetch categories and tags - url : `http:localhost:8000/api/product/create`

        -   method: get
        -   response:

            -   success : true,
            -   msg: 'عرض كل تصنيفات الرئيسية و الفرعيىة و الوسوم من اجل انشاء خدمة'
            -   data: languages

    -   store step one

        -   url : `http:localhost:8000/api/product/id/product-step-one`
        -   method: post
        -   parameters:

            -   title : required|string|max:255|unique
            -   subcategory : required|exists
            -   tags[] : required|exists

        -   response:

            -   success : true,
            -   msg: "تم انشاء المرحلة الاولى بنجاح"
            -   data: product

        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

    -   store step two

        -   url : `http:localhost:8000/api/product/{id}/product-step-two`
        -   method: post
        -   parameters:

            -   price : required|integer
            -   duration : required
            -   developments[] : sometimes
            -   developments.title : required|string|max:255
            -   developments.duration : required
            -   developments.price : required|integer

        -   response:

            -   success : true,
            -   msg: "تم انشاء المرحلة الثانية بنجاح"
            -   data: product

        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

    -   store step three

        -   url : `http:localhost:8000/api/product/{id}/product-step-three`
        -   method: post
        -   parameters:

            -   buyer_instruct : required|string|max: 255
            -   content : required|string|max: 255

        -   response:

            -   success : true,
            -   msg: "تم انشاء المرحلة الثالثة بنجاح"
            -   data: product

        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

    -   store step four

        -   url : `http:localhost:8000/api/product/{id}/product-step-four`
        -   method: post
        -   parameters:

            -   thumbnail : sometimes|image|mimes:png,jpg,jpeg|max:2048
            -   images : sometimes
            -   images[]     : mimes:png,jpg,jpeg|max:2048
            -   file          : mimes:pdf|max:2048
            -   url_video    : nullable|url

        -   response:

            -   success : true,
            -   msg: "تم انشاء المرحلة الرابعة بنجاح"
            -   data: product

        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

    -   Store step five and completed

        -   url : `http:localhost:8000/api/product/{id}/product-step-five`
        -   method: post

        -   response:

            -   success : true,
            -   msg: "تم انهاء المراحل و انشاء الخدمة بنجاح"
            -   data: product

        -   error response:
            -   success : false,
            -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"

13 - Delete product :

    -   url : `http:localhost:8000/api/product/{id}/deleteProduct`
    -   method: post
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ حذف الخدمة بنجاح"
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"
