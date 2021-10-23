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

2- Badges:

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
        -   msg: "حدث خطأ غير متوقع"

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
        -   data: level
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"

-   Delete Current Level

    -   url : `http:localhost:8000/dashboard/badges/{id}/delete`
    -   method: post
    -   success response:
        -   success : true,
        -   msg: "لقد تمّ حذف الشارة بنجاح"
    -   error response:
        -   success : false,
        -   msg: "حدث خطأ غير متوقع"
