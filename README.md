## Wazzfny

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

-   Show Levels - url : `http:localhost:8000/dashboard/levels` - method: get - parameters: - type (get query ?type=): 0 buyer levels , 1 seller levels, null all levels - response: - success : true, - msg: "لقد تمّ جلب المستويات بنجاح" - data: levels

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

-   Show Badges - url : `http:localhost:8000/dashboard/badges` - method: get - response: - success : true, - msg: "لقد تمّ جلب الشارات بنجاح" - data: badges

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

-   Show Catogories - url : `http:localhost:8000/dashboard/categories` - method: get - response: - success : true, - msg: 'عرض كل تصنيفات الرئيسية و الفرعية' - data: categories

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

-   Show Catogories - url : `http:localhost:8000/dashboard/subcategories` - method: get - response: - success : true, - msg: 'عرض كل تصنيفات الرئيسية' - data: categories

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

-   Show tags - url : `http:localhost:8000/dashboard/tags` - method: get - response: - success : true, - msg: 'عرض كل الوسوم' - data: tags

-   Show Tag Details

    -   url : `http:localhost:8000/dashboard/tags/{id}`
    -   method: get
    -   response:
        -   success : true,
        -   msg: "تم جلب العنصر بنجاح"
        -   data: tag

-   Add New Category

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

-   Delete Current SubCategory

    -   url : `http:localhost:8000/dashboard/tags/{id}/delete`
    -   method: post
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ حذف الوسم بنجاح"
    -   error response:
        -   success : false,
        -   msg: "هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك"
