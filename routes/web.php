<?php
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TokenVerificationMiddleware;
use App\Http\Middleware\AdminTokenVerificationMiddleware;
use App\Http\Middleware\ClientTokenVerificationMiddleware;

// Admin
use App\Http\Controllers\Backend\Auth\AdminAuthController;
use App\Http\Controllers\Backend\AdminProfileController; 
use App\Http\Controllers\Backend\AdminDashboardController;
use App\Http\Controllers\Backend\FoodController;
use App\Http\Controllers\Backend\AdminOrderController;
use App\Http\Controllers\Backend\ClientListController;
use App\Http\Controllers\Backend\CustomerListController;
use App\Http\Controllers\Backend\AdminComplainController;
use App\Http\Controllers\Backend\AdminNotificationController;
use App\Http\Controllers\Backend\AdminReportController;
use App\Http\Controllers\Backend\AdminContactMessageController;

use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\TermsConditionsController;
use App\Http\Controllers\Backend\AboutController;


// Client
use App\Http\Controllers\Client\Auth\ClientAuthController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientFoodController;
use App\Http\Controllers\Client\ClientOrderController;
use App\Http\Controllers\Client\ClientNotificationController;
use App\Http\Controllers\Client\ClientReportController;
use App\Http\Controllers\Client\ClientComplainController;
use App\Http\Controllers\Client\ClientTermsConditionsController;
use App\Http\Controllers\Client\ClientCustomerListController;

// Frontend
use App\Http\Controllers\Frontend\SocialAuthController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PagesController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\ComplainController;

Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});


// Frontend API Routes
Route::controller(HomeController::class)->group(function () {
    Route::get('/','HomePage')->name('home');
    Route::get('/setting-list', 'SettingList');
    Route::get('/food-list', 'FoodList');
    Route::get('/search-food','searchFood');
});

Route::controller(PagesController::class)->group(function () {
    Route::get('/about-us','AboutPage')->name('about');
    Route::get('/about-page-info','AboutPageInfo');

    Route::get('/food-details/{id}','FoodDetailsPage')->name('food.by.id');
    Route::get('/food-details-info/{id}','FoodDetailsInfo');

    Route::get('/contact-us','ContactPage')->name('contact.us.page');
    Route::post('/store-contact-info','StoreContactInfo');
});

Route::prefix('user')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/registration/terms-conditions/{name}','TermsConditionsPage');
        Route::get('/registration/terms-conditions/info/{name}','TermsConditionsInfo');
        
        Route::get('/registration','RegistrationPage')->name('register.page');
        Route::post('/registration','Registration');

        Route::get('/verify','VerifyCustomer')->name('verify.new.customer');
        Route::get('/login','LoginPage')->name('login.page');
        Route::post('/login','Login');

        Route::get('/sendOtp','SendOtpPage');
        Route::post('/send-otp','SendOTPCode');

        Route::get('/verifyOtp','VerifyOTPPage');
        Route::post('/verify-otp','VerifyOTP');

        Route::get('/resetPassword','ResetPasswordPage');
        Route::post('/reset-password','ResetPassword');
    });
});

Route::prefix('user')->middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile','ProfilePage')->name('user.profile');
        Route::get('/profile/info','Profile');
        Route::post('/profile/update','UpdateProfile');
        Route::get('/update/password','PasswordPage')->name('user.update.password');
        Route::post('/password/update','UpdatePassword');
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard','DashboardPage')->name('user.dashboard');
        Route::get('/total/information', 'TotalInfo');
        Route::get('/logout','Logout')->name('logout');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::post('/store/food-request', 'StoreFoodRequest');
        Route::get('/order-list', 'OrderPage')->name('orders');
        Route::get('/orders','OrderList');
        Route::get('/order-details/{order_id}','OrderDetailsPage');
        Route::get('/order-details-info/{order_id}','OrderDetailsInfo');
    });

    Route::controller(ComplainController::class)->group(function () {
        Route::get('/food/complain/{order_id}','FoodComplainPage');
        Route::post('/store-complain-info','StoreComplainInfo');

        Route::post('/upload-editor-image','uploadEditorImage');
        Route::post('/delete-editor-image','deleteEditorImage');

        Route::get('/complain-list', 'ComplainPage')->name('complains');
        Route::get('/complains','ComplainList');

        Route::get('/complain/reply/{complain_id}','FoodComplainReplyPage');
        Route::post('/store-complain-reply-info','StoreComplainReplyInfo');

        Route::get('/complain-details/{id}','ComplainDetailsPage');
        Route::get('/complain-details-info/{id}','ComplainDetailsInfo');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notification-list', 'NotificationPage')->name('notifications');
        Route::get('/limited/notification/list', 'LimitedNotificationList');
        Route::get('/notification/list/info', 'NotificationList');
        Route::get('/markAsRead', 'MarkAsRead')->name('markRead');
        Route::delete('/delete/notification/{notificationId}', 'deleteNotification');
    });
});
    

// Admin API Routes
Route::prefix('admin')->group(function () {
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('/registration','RegistrationPage')->name('admin.registration.page');
        Route::post('/registration','Registration');

        Route::get('/login','LoginPage')->name('admin.login.page');
        Route::post('/login','Login');

        Route::get('/sendOtp','SendOtpPage');
        Route::post('/send-otp','SendOTPCode');

        Route::get('/verifyOtp','VerifyOTPPage');
        Route::post('/verify-otp','VerifyOTP');

        Route::get('/resetPassword','ResetPasswordPage');
        Route::post('/reset-password','ResetPassword');
    });
});

Route::prefix('admin')->middleware([AdminTokenVerificationMiddleware::class])->group(function () {
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/dashboard','DashboardPage')->name('admin.dashboard');
        Route::get('/total/information', 'TotalInfo');

        Route::post('/update/client/account/{client_id}', 'UpdateClientAccount');
        Route::get('/logout','Logout')->name('admin.logout');
    });

    Route::controller(AdminProfileController::class)->group(function () {
        Route::get('/update/profile','ProfilePage');
        Route::get('/profile/info','Profile');
        Route::post('/profile/update','UpdateProfile');
        Route::get('/update/password','PasswordPage');
        Route::post('/password/update','UpdatePassword');
    });

    Route::controller(FoodController::class)->group(function () {
        Route::get('food-list', 'FoodPage')->name('foods');
        Route::get('/index','index');
        Route::get('/create/food','CreatePage');
        Route::post('/store/food','store');
        Route::get('/food/details/{id}','DetailsPage');
        Route::get('/food/info/{id}','show');
        Route::get('/edit/food/{id}','EditPage');
        Route::post('/update/food','update');
        Route::post('/food/delete','delete');
        Route::post('/update/food/status','FoodPublish');
    });

    Route::controller(AdminOrderController::class)->group(function () {
        Route::get('/order/list', 'OrderPage')->name('admin.orders');
        Route::get('/orders','OrderList');
        Route::get('/order/details/{order_id}','OrderDetailsPage');
        Route::get('/order/details/info/{order_id}','OrderDetails');
        Route::post('/order/delete','delete');
    });

    Route::controller(AdminComplainController::class)->group(function () {
        Route::get('/complain-list', 'ComplainPage')->name('admin.complains');
        Route::get('/complains','ComplainList');
        Route::get('/complain/details/{complain_id}','ComplainDetailsPage');
        Route::get('/complain/details/info/{complain_id}','ComplainDetails');
        Route::get('/complain-send/{id}','ComplainSendToClient');

        Route::post('/upload-editor-image','uploadEditorImage');
        Route::post('/delete-editor-image','deleteEditorImage');

        Route::post('/complain-solved','ComplainSolved');
        Route::post('/complain-investigation','ComplainInvestigation');

        Route::post('/complain/delete','delete');
    });

    Route::controller(ClientListController::class)->group(function () {
        Route::get('/client-list', 'ClientPage')->name('clients');
        Route::get('/clients','ClientList');

        Route::get('/client/details/{client_id}','ClientDetailsPage');
        Route::get('/client/details/info/{client_id}','ClientDetailsInfo');

        Route::get('/order/list/by/client/{client_id}','OrderListPageByClient');
        Route::get('/order/list/by/client/info/{client_id}','OrderListByClient');

        Route::get('/complain/list/by/client/{client_id}','ComplainListPageByClient');
        Route::get('/complain/list/by/client/info/{client_id}','ComplainListByClient');

        Route::get('/customer/list/by/client/{client_id}','CustomerListPageByClient');
        Route::get('/customer/list/by/client/info/{client_id}','CustomerListByClient');

        Route::get('/food/list/by/client/{client_id}','FoodListPageByClient');
        Route::get('/food/list/by/client/info/{client_id}','FoodListByClient');

        Route::post('/client/delete','delete');
    });

    Route::controller(CustomerListController::class)->group(function () {
        Route::get('/customer-list', 'CustomerPage')->name('customers');
        Route::get('/customers','CustomerList');

        Route::get('/customer/details/{customer_id}','CustomerDetailsPage');
        Route::get('/customer/details/info/{customer_id}','CustomerDetailsInfo');

        Route::get('/order/list/by/customer/{customer_id}','OrderListPageByCustomer');
        Route::get('/order/list/by/customer/info/{customer_id}','OrderListByCustomer');

        Route::get('/complain/list/by/customer/{customer_id}','ComplainListPageByCustomer');
        Route::get('/complain/list/by/customer/info/{customer_id}','ComplainListByCustomer');

        Route::get('/client/list/by/customer/{customer_id}','ClientListPageByCustomer');
        Route::get('/client/list/by/customer/info/{customer_id}','ClientListByCustomer');

        Route::post('/customer/delete','delete');
    });

    Route::controller(AdminContactMessageController::class)->group(function () {
        Route::get('/contact-message/list', 'ContactPage')->name('admin.contact.message');
        Route::get('/contact-message/list/info','index');
        Route::get('/contact-message/details/{id}','DetailsPage');
        Route::get('/contact-message/info/{id}','show');
        Route::post('/delete/contact-message','delete');
    });


    Route::controller(SiteSettingController::class)->group(function () {
        Route::get('/setting-page', 'SettingPage')->name('site.settings');
        Route::get('/site-setting-list','index');
        Route::get('/create/site-setting','create');
        Route::post('/store/site-setting','store');
        Route::get('/site-setting/info/{id}','show');
        Route::get('/edit/site-setting/{id}','EditPage');
        Route::post('/update/site-setting','update');
        Route::post('/delete/site-setting','delete');
    });

    Route::controller(TermsConditionsController::class)->group(function () {
        Route::get('/terms-conditions/list', 'TermsConditionsPage')->name('terms.conditions');
        Route::get('/terms-conditions/list/info','index');
        Route::get('/create/terms-conditions','CreatePage');
        Route::post('/store/terms-conditions','store');
        Route::get('/terms-conditions/details/{id}','DetailsPage');
        Route::get('/terms-conditions/info/{id}','show');
        Route::get('/edit/terms-conditions/{id}','EditPage');
        Route::post('/update/terms-conditions','update');
        Route::post('/delete/terms-conditions','delete');

        Route::get('/terms-conditions/{name}','TermsConditionsPageByType');
        Route::get('/terms-conditions-info/by/{name}','TermsConditionsInfoByType');

    });


    Route::controller(AboutController::class)->group(function () {
        Route::get('/about/page', 'AboutPage')->name('abouts');
        Route::get('/about/list','index');
        Route::get('/create/about','create');
        Route::post('/store/about','store');
        Route::get('/show/about/info/{id}','show');
        Route::get('/edit/about/{id}','EditPage');
        Route::post('/update/about','update');
        Route::post('/delete/about','delete');
    });

    Route::controller(AdminNotificationController::class)->group(function () {
        Route::get('/notification/list', 'NotificationPage')->name('admin.notifications');
        Route::get('/limited/notification/list', 'LimitedNotificationList');
        Route::get('/notification/list/info', 'NotificationList');
        Route::get('/markAsRead', 'MarkAsRead')->name('admin.markRead');
        Route::delete('/delete/notification/{notificationId}', 'deleteNotification');
    });

    Route::controller(AdminReportController::class)->group(function () {
        Route::get('/todays/order/reports', 'TodaysReportPage')->name('todays.report');
        Route::get('/todays/order/information', 'TodaysOrderInfo');

        Route::get('search/reports', 'ReportSearchPage')->name('search.report');
        Route::post('order/by/search', 'OrderBySearch');
    });
});



// Client API Routes
Route::prefix('client')->group(function () {
    Route::controller(ClientAuthController::class)->group(function () {
        Route::get('/registration/terms-conditions/{name}','TermsConditionsPage');
        Route::get('/registration/terms-conditions/info/{name}','TermsConditionsInfo');

        Route::get('/registration','RegistrationPage')->name('client.registration.page');
        Route::post('/registration','Registration');

        Route::get('/login','LoginPage')->name('client.login.page');
        Route::post('/login','Login');

        Route::get('/sendOtp','SendOtpPage');
        Route::post('/send-otp','SendOTPCode');

        Route::get('/verifyOtp','VerifyOTPPage');
        Route::post('/verify-otp','VerifyOTP');

        Route::get('/resetPassword','ResetPasswordPage');
        Route::post('/reset-password','ResetPassword');
    });
});

Route::prefix('client')->middleware([ClientTokenVerificationMiddleware::class])->group(function () {
    Route::controller(ClientDashboardController::class)->group(function () {
        Route::get('/dashboard','DashboardPage')->name('client.dashboard');
        Route::get('/total/information', 'TotalInfo');
        Route::get('/logout','Logout')->name('client.logout');
    });

    Route::controller(ClientProfileController::class)->group(function () {
        Route::get('/update/profile','ProfilePage');
        Route::get('/profile/info','Profile');
        Route::post('/profile/update','UpdateProfile');
        Route::get('/update/password','PasswordPage');
        Route::post('/password/update','UpdatePassword');
        Route::get('/account/details/{client_id}','ClientDetailsPage');
        Route::get('/account/details/info/{client_id}','ClientDetailsInfo');
    });

    Route::controller(ClientFoodController::class)->group(function () {
        Route::get('/food-list', 'FoodPage')->name('client.foods');
        Route::get('/index','index');
        Route::get('/create/food','CreatePage');
        Route::post('/store/food','store');

        Route::get('/food/details/{id}','DetailsPage');
        Route::get('/food/info/{id}','show');

        Route::get('/edit/food/{id}','EditPage');
        Route::post('/update/food','update');
        Route::post('/food/delete','delete');

        Route::get('/edit/food/multi-image/{id}','EditMultiImgPage');
        Route::post('/update-multi-image','updateMultiImg');
    });

    Route::controller(ClientOrderController::class)->group(function () {
        Route::get('/order/list', 'OrderPage')->name('client.orders');
        Route::get('/orders','OrderList');

        Route::get('/order/details/{order_id}','OrderDetailsPage');
        Route::get('/order/details/info/{order_id}','OrderDetails');

        Route::post('/approve/food/request','ApproveFoodRequest');
        Route::post('/delivered/food/request','DeliveredFoodRequest');
        Route::post('/cancel/food/request','CancelFoodRequest');
    });


    Route::controller(ClientComplainController::class)->group(function () {
        Route::get('/complain-list', 'ComplainPage')->name('client.complains');
        Route::get('/complains','ComplainList');

        Route::get('/complain/details/{complain_id}', 'ComplainDetailsPage');
        Route::get('/complain/details/info/{complain_id}','ComplainDetails');

        Route::post('/upload-editor-image','uploadEditorImage');
        Route::post('/delete-editor-image','deleteEditorImage');

        Route::post('/store-complain-feedback','StoreComplainFeedbackInfo');
        Route::post('/complain/delete','delete');
    });


    Route::controller(ClientNotificationController::class)->group(function () {
        Route::get('/notification/list', 'NotificationPage')->name('client.notifications');
        Route::get('/limited/notification/list', 'LimitedNotificationList');
        Route::get('/notification/list/info', 'NotificationList');
        Route::get('/markAsRead', 'MarkAsRead')->name('client.markRead');
        Route::delete('/delete/notification/{notificationId}', 'deleteNotification');
    });

    Route::controller(ClientReportController::class)->group(function () {
        Route::get('/todays/order/reports', 'TodaysReportPage')->name('client.todays.report');
        Route::get('/todays/order/information', 'TodaysOrderInfo');

        Route::get('/search/reports', 'ReportSearchPage')->name('client.search.report');
        Route::post('/order/by/search', 'OrderBySearch');
    });

    Route::controller(ClientTermsConditionsController::class)->group(function () {
        Route::get('/terms-conditions/{name}','TermsConditionsPage');
        Route::get('/terms-conditions/info/{name}','TermsConditionsInfo');
    });

    Route::controller(ClientCustomerListController::class)->group(function () {
        Route::get('/customer-list', 'CustomerPage')->name('client.customers');
        Route::get('/customers','CustomerList');

        Route::get('/customer/details/{customer_id}','CustomerDetailsPage');
        Route::get('/customer/details/info/{customer_id}','CustomerDetailsInfo');

        Route::get('/order/list/by/customer/{customer_id}','OrderListPageByCustomer');
        Route::get('/order/list/by/customer/info/{customer_id}','OrderListByCustomer');

        Route::get('/complain/list/by/customer/{customer_id}','ComplainListPageByCustomer');
        Route::get('/complain/list/by/customer/info/{customer_id}','ComplainListByCustomer');
    });

});


