<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('catalog', 'Catalog::index');
$routes->get('catalog/category/(:any)', 'Catalog::category/$1');
$routes->get('product/(:any)', 'Catalog::product/$1');
$routes->get('suppliers', 'Ecosystem::suppliers', ['filter' => 'accountauth']);
$routes->get('post-rfq', 'Ecosystem::rfq');
$routes->get('transport', 'Ecosystem::transport');
$routes->get('faq', 'Ecosystem::faq');
$routes->get('promotion', 'Ecosystem::promotion');
$routes->get('privacy-policy', 'Ecosystem::privacyPolicy');
$routes->get('terms-of-service', 'Ecosystem::termsOfService');
$routes->get('steel-suppliers-loha-mandi-ghaziabad', 'Ecosystem::seoSteelSuppliers');
$routes->get('iron-dealers-loha-mandi-ghaziabad', 'Ecosystem::seoIronDealers');
$routes->get('ms-steel-suppliers-ghaziabad', 'Ecosystem::seoMsSteelSuppliers');
$routes->get('steel-traders-ghaziabad', 'Ecosystem::seoSteelTraders');
$routes->get('tmt-dealers-ghaziabad', 'Ecosystem::seoTmtDealers');
$routes->get('ms-pipe-suppliers-ghaziabad', 'Ecosystem::seoMsPipeSuppliers');
$routes->get('steel-sheet-suppliers-ghaziabad', 'Ecosystem::seoSteelSheetSuppliers');
$routes->get('uploads/(:segment)/(:segment)', 'UploadController::show/$1/$2');
$routes->get('seller/(:any)/review', 'Storefront::reviewForm/$1');
$routes->post('seller/(:any)/review', 'Storefront::submitReview/$1');
$routes->get('seller/(:any)', 'Storefront::index/$1');
$routes->get('ad/go/(:num)', 'Ad::go/$1');
$routes->get('ad/view/(:num)', 'Ad::view/$1');

/*
 * -------------------------------------------------------------------
 * Account area — buyer & seller web front-end (session-authenticated)
 * -------------------------------------------------------------------
 */
$routes->group('', ['namespace' => 'App\Controllers\Account'], static function ($routes) {
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::register');
    $routes->get('account/register/seller/checkout', 'Auth::sellerCheckout');
    $routes->get('account/register/seller/pay', 'Auth::sellerPay');
    $routes->post('account/register/seller/pay', 'Auth::sellerVerify');
    $routes->get('login', 'Auth::login');
    $routes->get('login/otp', 'Auth::loginOtp');
    $routes->post('login/firebase', 'Auth::loginFirebase');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');

    $routes->group('', ['filter' => 'accountauth'], static function ($routes) {
        $routes->get('account', 'Dashboard::index');

        // Products (seller)
        $routes->get('account/products', 'Products::index');
        $routes->get('account/products/new', 'Products::form');
        $routes->post('account/products/new', 'Products::form');
        $routes->get('account/products/(:num)/edit', 'Products::form/$1');
        $routes->post('account/products/(:num)/edit', 'Products::form/$1');
        $routes->get('account/products/(:num)/toggle', 'Products::toggleActive/$1');
        $routes->get('account/products/(:num)/delete', 'Products::delete/$1');
        $routes->get('account/products/(:num)', 'Products::view/$1');

        // RFQs (buyer + seller inbox)
        $routes->get('account/rfqs', 'Rfqs::index');
        $routes->get('account/rfqs/new', 'Rfqs::create');
        $routes->post('account/rfqs/new', 'Rfqs::create');
        $routes->get('account/rfqs/(:num)', 'Rfqs::show/$1');
        $routes->get('account/rfqs/(:num)/cancel', 'Rfqs::cancel/$1');

        // Quotations (seller sends, buyer receives)
        $routes->get('account/quotations', 'Quotations::index');
        $routes->get('account/quotations/new/(:num)', 'Quotations::create/$1');
        $routes->post('account/quotations/new/(:num)', 'Quotations::create/$1');

        // Orders
        $routes->get('account/orders', 'Orders::index');
        $routes->get('account/orders/(:num)', 'Orders::show/$1');
        $routes->post('account/quotations/(:num)/accept', 'Orders::acceptQuote/$1');
        $routes->post('account/orders/(:num)/status', 'Orders::updateStatus/$1');

        // Payments (buyer)
        $routes->get('account/orders/(:num)/pay', 'Payments::pay/$1');
        $routes->post('account/orders/(:num)/pay', 'Payments::initiate/$1');
        $routes->post('account/orders/(:num)/pay/verify', 'Payments::verify/$1');
        $routes->get('account/payments', 'Payments::history');

        // Leads (seller)
        $routes->get('account/leads', 'Leads::index');
        $routes->post('account/leads/(:num)/status', 'Leads::updateStatus/$1');

        // CRM (seller)
        $routes->get('account/crm', 'Crm::index');
        $routes->get('account/crm/customers', 'Crm::customers');
        $routes->get('account/crm/customers/new', 'Crm::customerCreate');
        $routes->post('account/crm/customers/new', 'Crm::customerCreate');
        $routes->get('account/crm/customers/(:num)/edit', 'Crm::customerEdit/$1');
        $routes->post('account/crm/customers/(:num)/edit', 'Crm::customerEdit/$1');
        $routes->get('account/crm/customers/(:num)/delete', 'Crm::customerDelete/$1');
        $routes->get('account/crm/customers/(:num)', 'Crm::customerShow/$1');
        $routes->get('account/crm/deals', 'Crm::deals');
        $routes->get('account/crm/deal/new', 'Crm::dealCreate');
        $routes->get('account/crm/deal/new/(:num)', 'Crm::dealCreate/$1');
        $routes->post('account/crm/deal/new', 'Crm::dealCreate');
        $routes->get('account/crm/deals/(:num)/edit', 'Crm::dealEdit/$1');
        $routes->post('account/crm/deals/(:num)/edit', 'Crm::dealEdit/$1');
        $routes->post('account/crm/deals/(:num)/stage', 'Crm::dealStage/$1');
        $routes->get('account/crm/deals/(:num)/delete', 'Crm::dealDelete/$1');
        $routes->get('account/crm/tasks', 'Crm::tasks');
        $routes->post('account/crm/tasks', 'Crm::tasks');
        $routes->get('account/crm/tasks/(:num)/toggle', 'Crm::taskToggle/$1');
        $routes->get('account/crm/tasks/(:num)/delete', 'Crm::taskDelete/$1');
        $routes->get('account/crm/followups', 'Crm::followups');
        $routes->post('account/crm/followups', 'Crm::followups');
        $routes->get('account/crm/followups/(:num)/complete', 'Crm::followupComplete/$1');
        $routes->get('account/crm/followups/(:num)/delete', 'Crm::followupDelete/$1');
        $routes->get('account/crm/meetings', 'Crm::meetings');
        $routes->post('account/crm/meetings', 'Crm::meetings');
        $routes->post('account/crm/meetings/(:num)/status', 'Crm::meetingStatus/$1');
        $routes->get('account/crm/meetings/(:num)/delete', 'Crm::meetingDelete/$1');
        $routes->post('account/crm/notes/new', 'Crm::noteCreate');
        $routes->get('account/crm/notes/(:num)/delete', 'Crm::noteDelete/$1');

        // Inventory (seller)
        $routes->get('account/inventory', 'Inventory::index');
        $routes->get('account/inventory/stock', 'Inventory::stock');
        $routes->get('account/inventory/stock/new', 'Inventory::stockCreate');
        $routes->post('account/inventory/stock/new', 'Inventory::stockCreate');
        $routes->get('account/inventory/stock/(:num)/edit', 'Inventory::stockEdit/$1');
        $routes->post('account/inventory/stock/(:num)/edit', 'Inventory::stockEdit/$1');
        $routes->get('account/inventory/stock/(:num)/delete', 'Inventory::stockDelete/$1');
        $routes->get('account/inventory/warehouses', 'Inventory::warehouses');
        $routes->get('account/inventory/warehouses/new', 'Inventory::warehouseCreate');
        $routes->post('account/inventory/warehouses/new', 'Inventory::warehouseCreate');
        $routes->get('account/inventory/warehouses/(:num)/edit', 'Inventory::warehouseEdit/$1');
        $routes->post('account/inventory/warehouses/(:num)/edit', 'Inventory::warehouseEdit/$1');
        $routes->get('account/inventory/warehouses/(:num)/delete', 'Inventory::warehouseDelete/$1');
        $routes->get('account/inventory/warehouses/(:num)/default', 'Inventory::warehouseDefault/$1');
        $routes->get('account/inventory/transfers', 'Inventory::transfers');
        $routes->get('account/inventory/transfers/new', 'Inventory::transferCreate');
        $routes->post('account/inventory/transfers/new', 'Inventory::transferCreate');
        $routes->post('account/inventory/transfers/(:num)/status', 'Inventory::transferStatus/$1');

        // AI tools (seller)
        $routes->get('account/ai', 'Ai::index');
        $routes->get('account/ai/tools/(:num)', 'Ai::tools/$1');
        $routes->get('account/ai/tools/(:num)/copy', 'Ai::copy/$1');
        $routes->get('account/ai/chat', 'Ai::chat');
        $routes->post('account/ai/chat', 'Ai::chat');

        // Profile
        $routes->get('account/profile', 'Profile::index');
        $routes->post('account/profile', 'Profile::index');

        // Seller subscription (paywall for unpaid sellers)
        $routes->get('account/subscribe', 'Subscribe::index');
        $routes->get('account/subscribe/pay', 'Subscribe::pay');
        $routes->post('account/subscribe/pay', 'Subscribe::verify');

        // Chats (buyer + seller)
        $routes->get('account/chats', 'Chats::index');
        $routes->get('account/chats/new', 'Chats::newChat');
        $routes->get('account/chats/open/(:num)', 'Chats::open/$1');
        $routes->get('account/chats/(:num)', 'Chats::show/$1');
        $routes->post('account/chats/(:num)', 'Chats::show/$1');

        // Reviews
        $routes->get('account/orders/(:num)/review', 'Reviews::create/$1');
        $routes->post('account/orders/(:num)/review', 'Reviews::create/$1');
        $routes->get('account/seller-reviews', 'Reviews::sellerReviews');
        $routes->post('account/reviews/(:num)/reply', 'Reviews::reply/$1');

        // Notifications
        $routes->get('account/notifications', 'Notifications::index');
        $routes->get('account/notifications/(:num)/read', 'Notifications::markRead/$1');
        $routes->post('account/notifications/mark-all-read', 'Notifications::markAllRead');

        // Transport
        $routes->get('account/vehicles', 'Vehicles::index');
        $routes->post('account/vehicles', 'Vehicles::add');
        $routes->get('account/vehicles/(:num)/delete', 'Vehicles::delete/$1');
        $routes->get('account/transport', 'Transport::index');
        $routes->get('account/transport/fleets', 'Transport::fleets');
        $routes->get('account/transport/new', 'Transport::create');
        $routes->get('account/transport/new/(:num)', 'Transport::create/$1');
        $routes->post('account/transport/new', 'Transport::create');
        $routes->post('account/transport/new/(:num)', 'Transport::create/$1');
        $routes->get('account/transport/(:num)', 'Transport::show/$1');
        $routes->post('account/transport/(:num)/status', 'Transport::updateStatus/$1');
    });
});

/*
 * -------------------------------------------------------------------
 * Admin Web Panel (session-authenticated)
 * -------------------------------------------------------------------
 */
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('', 'Dashboard::index');
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');

    $routes->group('', ['filter' => 'adminauth'], static function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');

        // Users
        $routes->get('users', 'Users::index');
        $routes->get('users/(:num)', 'Users::show/$1');
        $routes->get('users/toggle/(:num)', 'Users::toggleStatus/$1');
        $routes->get('users/delete/(:num)', 'Users::delete/$1');

        // Sellers
        $routes->get('sellers', 'Sellers::index');
        $routes->get('sellers/(:num)', 'Sellers::show/$1');
        $routes->post('sellers/(:num)/moderate', 'Sellers::moderate/$1');
        $routes->post('sellers/(:num)/update', 'Sellers::update/$1');

        // Products
        $routes->get('products', 'Products::index');
        $routes->get('products/(:num)', 'Products::show/$1');
        $routes->post('products/(:num)/moderate', 'Products::moderate/$1');
        $routes->get('products/feature/(:num)', 'Products::toggleFeatured/$1');

        // Categories, brands, locations
        $routes->get('categories', 'Categories::index');
        $routes->post('categories', 'Categories::index');
        $routes->get('categories/delete/(:num)', 'Categories::delete/$1');
        $routes->post('categories/(:num)/update', 'Categories::update/$1');
        $routes->get('brands', 'Brands::index');
        $routes->post('brands', 'Brands::index');
        $routes->get('brands/delete/(:num)', 'Brands::delete/$1');
        $routes->post('brands/(:num)/update', 'Brands::update/$1');
        $routes->get('locations', 'Locations::index');
        $routes->post('locations', 'Locations::index');
        $routes->get('locations/delete/(:num)', 'Locations::delete/$1');
        $routes->post('locations/(:num)/update', 'Locations::update/$1');

        // Ads
        $routes->get('ads', 'Ads::index');
        $routes->post('ads', 'Ads::index');
        $routes->post('ads/(:num)/update', 'Ads::update/$1');
        $routes->get('ads/(:num)/toggle', 'Ads::toggle/$1');
        $routes->get('ads/delete/(:num)', 'Ads::delete/$1');

        // Oversight & reports
        $routes->get('oversight', 'Oversight::index');
        $routes->post('oversight/reviews/(:num)/moderate', 'Oversight::moderateReview/$1');
        $routes->get('reports', 'Reports::index');

        // AI oversight
        $routes->get('ai', 'Ai::index');
        $routes->get('ai/enhance', 'Ai::enhanceImage');
        $routes->get('ai/enhance-all', 'Ai::enhanceAll');
    });
});

/*
 * -------------------------------------------------------------------
 * API Routes — LOHA MANDI Ghaziabad
 * Prefix: /api   Format: JSON
 * -------------------------------------------------------------------
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    // ---------- Auth ----------
    $routes->post('register', 'AuthController::register');
    $routes->post('otp/send', 'AuthController::sendOtp');
    $routes->post('otp/verify', 'AuthController::verifyOtp');
    $routes->post('login', 'AuthController::login');
    $routes->post('forgot-password', 'AuthController::forgotPassword');

    // ---------- Public catalog ----------
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/tree', 'CategoryController::tree');
    $routes->get('categories/(:num)', 'CategoryController::show/$1');
    $routes->get('brands', 'BrandController::index');
    $routes->get('brands/(:num)', 'BrandController::show/$1');
    $routes->get('products', 'ProductController::index');
    $routes->get('products/(:num)', 'ProductController::show/$1', ['filter' => 'jwtoptional']);
    $routes->get('search', 'SearchController::index');
    $routes->get('search/suggest', 'SearchController::suggest');
    $routes->get('sellers/(:num)', 'SellerController::show/$1');
    $routes->get('rfqs/(:num)', 'RfqController::show/$1', ['filter' => 'jwtoptional']);

    // ---------- Authenticated ----------
    $routes->group('', ['filter' => 'jwtauth'], static function ($routes) {
        $routes->get('me', 'AuthController::me');

        // Products (seller management)
        $routes->post('products', 'ProductController::create');
        $routes->put('products/(:num)', 'ProductController::update/$1');
        $routes->delete('products/(:num)', 'ProductController::delete/$1');
        $routes->get('products/mine', 'ProductController::mine');
        $routes->post('products/(:num)/inquiry', 'ProductController::recordInquiry/$1');

        // Sellers / dashboard
        $routes->put('sellers/profile', 'SellerController::updateProfile');
        $routes->get('sellers/dashboard', 'SellerController::dashboard');
        $routes->get('sellers/leads', 'SellerController::leads');
        $routes->put('sellers/leads/(:num)', 'SellerController::updateLead/$1');

        // RFQ
        $routes->post('rfqs', 'RfqController::create');
        $routes->get('rfqs/mine', 'RfqController::mine');
        $routes->get('rfqs/for-sellers', 'RfqController::forSellers');
        $routes->put('rfqs/(:num)/action', 'RfqController::sellerAction/$1');

        // Quotations
        $routes->post('quotations', 'QuotationController::create');
        $routes->get('quotations/mine', 'QuotationController::mine');
        $routes->put('quotations/(:num)/status', 'QuotationController::respondToQuote/$1');

        // Admin / moderation
        $routes->get('admin/products/pending', 'AdminController::pendingProducts');
        $routes->post('admin/products/(:num)/moderate', 'AdminController::moderateProduct/$1');
        $routes->get('admin/sellers/pending', 'AdminController::pendingSellers');
        $routes->post('admin/sellers/(:num)/moderate', 'AdminController::moderateSeller/$1');
    });
});
